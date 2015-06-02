window.SCRIPTSITE = $(".insertStepsHere") # Specifies the site, where variables are to place.
SHORT_CIRCUIT = true # Short-circuit evaluation

class Node
  ###
    Must be overridden by all subclasses.
  ###
  execute: (player) ->
    throw new Error('Node must override execute() method!')

  ###
    This method executes the index part of an array access.
  ###
  executeIndex: (variable, player) ->
    # constant value as index (e.g. a[1])
    if (variable.kind is 'const' and variable.type is 'int')
      variable.value

      # variable as index (e.g. a[i])
    else if (variable.kind is 'var')
      player.stats.readVar(variable.vid)
      player.memory.get(variable.vid).value

      # compound value as index (e.g. a[i+1])
    else if (variable.kind is 'comp')
      @executeValue(variable, player)

    else
      throw new ExecutionError('unsupported_index', [variable.kind])

  executeValue: (value, player) ->
    if (!value?.kind?)
      throw new ExecutionError('could_not_execute_value', [value])
    memory = player.memory

    switch value.kind
      when 'const' then value.value

      when 'index'
        index = @executeIndex(value.index, player)
        player.stats.readArrayVar(value.vid, index)
        memory.arrayGet(value.vid, index)

      when 'prop'
        if (value.prop is 'length')
          variable = memory.get(value.vid)
          if (variable.array) then variable.value.split(',').length
          else 1
        else
          throw new ExecutionError('unknown_property', [value.prop])

      when 'var'
        vid = value.vid
        player.stats.readVar(vid) # tell the stats, that a variable has been read
        memory.get(vid).value # return the current value of the variable

      when 'comp'
        leftVal = @executeValue(value.left, player)
        rightVal = @executeValue(value.right, player)
        player.stats.incArithmeticOps()
        switch value.op
          when '+' then leftVal + rightVal
          when '-' then leftVal - rightVal
          when '*' then leftVal * rightVal
          when '/'
            throw new ExecutionError('divide_by_zero', []) if (rightVal is 0)
            parseInt(leftVal / rightVal)
          when '%' then leftVal % rightVal
          else
            throw new ExecutionError('unknown_arithmetic_op', [@operator])

      else
        throw new ExecutionError('unknown_kind', [value.kind])

  readVar: () ->

  writeVar: (destination, value, player) ->
    switch (destination.kind)
      when 'index'
        index = @executeIndex(destination.index, player)
        player.memory.arraySet(destination.vid, index, value)
        player.stats.writeArrayVar(destination.vid, index, value)
      when 'var'
        player.memory.set(destination.vid, value)
        player.stats.writeVar(destination.vid, value)
      when 'const' then throw new ExecutionError('assign_to_const', [destination.value])
      when 'prop' then throw new ExecutionError('assign_to_prop', [])
      else
        throw new ExecutionError('unknown_kind', [destination.kind])

  ###
    Sets the cursor to the position of the node. Override to place it somewhere else!
  ###
  mark: (player, node) ->
    if (node is @nid)
      player.setCursor(@nid)
      @nid
    else
      -1

  ###
    Must be overridden by all subclasses.
  ###
  toJSON: ->
    throw new Error('Node must override toJSON() method!')

  ###
    Delegates the parse function to the right subclass.
    Must be overridden by all subclasses.
  ###
  @parse: (node, tree, memory) ->
    # extract the type
    type = node.data('node-type')

    # call proper parsing method
    switch type
      when 'assign' then AssignNode.parse(node, tree, memory)
      when 'comment' then CommentNode.parse(node, tree, memory)
      when 'compare' then CompareNode.parse(node, tree, memory)
      when 'if' then IfNode.parse(node, tree, memory)
      when 'inc' then IncNode.parse(node, tree, memory)
      when 'return' then ReturnNode.parse(node, tree, memory)
      when 'swap' then SwapNode.parse(node, tree, memory)
      when 'value' then ValueNode.parse(node, tree, memory)
      when 'while' then WhileNode.parse(node, tree, memory)
      else
        throw new Error("Parse error: unknown type: '#{type}'")

  ###
  # For combo boxes: Inspects the given value and defines its kind
  # and properties.
  ###
  @parseAndCheckValue: (_class, node, memory) ->
    node = @findSubNode(node, _class)
    value = @parseValue(node.val(), memory)
    if !value? then node.addClass('error')
    else node.removeClass('error')
    value

  @parseValue: (value, memory) ->
    # check for const (int)
    intVal = parseInt(value)
    if (intVal + "" is value)
      return {kind: 'const', type: 'int', value: intVal}

    # check for array ([])
    open = value.indexOf('[')
    close = value.lastIndexOf(']')
    if (open > -1 and close > open)
      vid = memory.find(value.substr(0, open))
      inner = @parseValue(value.substr(open + 1, close - open - 1), memory)
      if vid > -1 and inner?
        memory.count(vid)
        return {kind: 'index', vid: vid, index: inner}
      else return null

    # check for property (.length)
    period = value.indexOf('.')
    if (period > -1 and value.substr(period + 1) is "length")
      vid = memory.find(value.substr(0, period))
      if (vid > -1)
        memory.count(vid)
        return {kind: 'prop', type: 'int', vid: vid, prop: 'length'}
      else return null

    # check for variable name
    vid = memory.find(value)
    if (vid > -1)
      memory.count(vid)
      return {kind: 'var', vid: vid}

    value = value.replace(/\s*/g, '') # remove white spaces
    # check for simple computations (e.g. i+1)
    if (value.indexOf('(') is -1)
      split = value.split(/(-|\+|\*|\/|%)/i)
      if (split.length is 3) # e.g. "i-1"
        left = @parseValue(split[0], memory)
        right = @parseValue(split[2], memory)
        if (left? and right? and "+-*/%".indexOf(split[1]) >= 0)
          return {kind: 'comp', left: left, right: right, op: split[1]}
        else return null

    # check for complex computations (using parenthesis)
    if (value = @parsePars(value))?
      switch (Object.keys(value).length)
        when 1 # unnecessary pars
          return @parseValue(value[0], memory)
        when 3 # binary
          left = @parseValue(value[0], memory)
          right = @parseValue(value[2], memory)
          op = value[1]
          if (left? and right? and "+-*/%".indexOf(value[1]) >= 0)
            return {kind: 'comp', left: left, right: right, op: op}

    # return null, if value is not valid
    null

  ###
    Deals with complex binary expressions within parenthesis. It goes one level
    deep (say: not recursive). Returns an object with one value if it is just
    a simple expression within parenthesis. It the expression is more complex,
    it returns an object of size 3 that contains two expressions left, right along
    with the used operator.
  ###
  @parsePars: (value) ->
    level = 0
    result = {}
    index = 0
    split = value.split(/(-|\+|\*|\/|%@|\(|\))/g)
    for i in [0...split.length]
      chunk = split[i]
      if (chunk is '') then continue

      if (level is 0)
        if (chunk is '(') then level++
        else if (chunk is ')') then level--
        else result[index++] = chunk
      else # level > 0
        if (chunk is '(') then level++
        else if (chunk is ')')
          if (--level is 0)
            index++
            continue
        if (result[index]?) then result[index] += chunk
        else result[index] = chunk

    if (level isnt 0)
      console.log("Unbalanced")
      null
    else
      result

  ###
  # Returns node's first sub-node of class _class.
  ###
  @findSubNode: (node, _class) ->
    node.find(_class + ':first')

  @validate: (node, check) ->
    flag = node.find('.invalid-flag:first')
    if (check)
      node.removeClass('invalid')
      flag.hide()
    else
      node.addClass('invalid')
      flag.show()

class AssignNode extends Node
  constructor: (@nid, @from, @to) ->

  execute: (player, node) ->
    # get new value
    value = player.tree.tree[@from].execute(player, 0).value
    # write new value
    @writeVar(@to, value, player)
    # return value
    value: value

  toJSON: ->
    {
    nid: @nid
    node: 'assign'
    from: @from
    to: @to
    }

  @parse: (node, tree, memory) =>
    # parse from-node
    from = BlockNode.parse(@findSubNode(node, '.assign-from'), tree, memory)
    tree.push from
    # parse to-value
    to = @parseAndCheckValue('.assign-to', node, memory)
    @validate(node, to?)
    # create the node
    nid = tree.length
    new @(nid, from.nid, to)

class BlockNode extends Node
  constructor: (@nid, @nodes) ->
    @curNode = 0

  execute: (player, node) ->
    curNode = null
    # find matching sub-node
    for n,i in @nodes
      if (node <= n)
        curNode = player.tree.tree[n].execute(player, node)
        break
    # use the value if there is one
    if (curNode?.value?) then value = curNode.value
    else value = null
    # compute next node
    if (curNode?.next?)
      {next: curNode.next, value: value}
    else if (curNode is -1) # return node was executed
      {next: -1, value: value}
    else if (@nodes.length > i + 1) # if curNode is done, take next from nodes
      {next: @nodes[i + 1], value: value}
    else
      {value: value}

  executeAll: (player, node, combine) ->
    value = combine is 'all'
    next = -1
    for n,i in @nodes
      if (node < n)
        next = n
        break
      else
        curValue = player.tree.tree[n].execute(player, n).value
        value = value and curValue if (combine is 'all')
        value = value or curValue if (combine is 'any')
        break if (SHORT_CIRCUIT and not value)
    {value: value, next: next}

  mark: (player, node) ->
    # if BlockNode itself should be marked...
    if (node is @nid)
      # ... mark first child that wants
      for n,i in @nodes
        marked = player.tree.tree[n].mark(player, n)
        return marked if marked > -1
      return -1
      # if a child node should be marked...
    else
      for n,i in @nodes
        # ... find suitable child and try marking it
        if (node <= n)
          marked = player.tree.tree[n].mark(player, node)
          if marked > -1 then return marked
            # ... otherwise try the next child
          else node = n + 1
    return -1

  size: ->
    @nodes.length

  toJSON: ->
    {
    nid: @nid
    node: 'block'
    nodes: @nodes
    }

  @parse: (node, tree, memory) =>
    # prepare return value
    nodes = []
    # loop level-1 elements:
    node.children('li.node').each((index, element) =>
      # parse child and add it to tree
      child = Node.parse($(element), tree, memory)
      tree.push child
      # store each child-nid in block-node
      nodes[index] = child.nid
    )
    nid = tree.length
    new @(nid, nodes)

class CommentNode extends Node
  constructor: (@nid, @comment) ->

  execute: (player, node) ->

  mark: (player, node) ->
    -1 # never let this node be marked

  toJSON: ->
    {
    nid: @nid
    node: 'comment'
    comment: @comment
    }

  @parse: (node, tree, memory) =>
    comment = @findSubNode(node, '.comment-text').val()
    nid = tree.length
    new @(nid, comment)

class CompareNode extends Node
  constructor: (@nid, @left, @right, @operator) ->

  execute: (player, node) ->
    leftVal = @executeValue(@left, player)
    rightVal = @executeValue(@right, player)
    player.stats.incCompareOps()
    switch @operator
      when 'le' then value: leftVal <= rightVal
      when 'lt' then value: leftVal < rightVal
      when 'eq' then value: leftVal == rightVal
      when 'gt' then value: leftVal > rightVal
      when 'ge' then value: leftVal >= rightVal
      when 'ne' then value: leftVal != rightVal
      else
        throw new Error("CompareNode: unknown operator: '#{@operator}'!")

  toJSON: ->
    {
    nid: @nid
    node: 'compare'
    left: @left
    right: @right
    operator: @operator
    }

  @parse: (node, tree, memory) =>
    left = @parseAndCheckValue('.compare-left', node, memory)
    right = @parseAndCheckValue('.compare-right', node, memory)
    @validate(node, left? and right?)
    operator = @findSubNode(node, '.compare-operation').val()
    nid = tree.length
    new @(nid, left, right, operator)

class IfNode extends Node
  constructor: (@nid, @condition, @ifBody, @elseBody, @op) ->

  execute: (player, node) ->
    # find matching sub-node
    if (node <= @condition)
      cond = player.tree.tree[@condition]
      # execute the condition
      size = cond.size()
      if (size is 1) then condRetVal = cond.execute(player, node)
      else if (size > 1) then condRetVal = cond.executeAll(player, node, @op)
      else throw new ExecutionError('no_condition', [])
      # define the next step
      if condRetVal.next > -1 then next: condRetVal.next
      else if condRetVal.value then next: @ifBody
      else next: @elseBody
    else if (node <= @ifBody)
      next: player.tree.tree[@ifBody].execute(player, node).next
    else
      next: player.tree.tree[@elseBody].execute(player, node).next

  mark: (player, node) ->
    # mark condition if the IfNode should be marked
    if (node is @nid)
      player.tree.tree[@condition].mark(player, @condition)
      # redirect mark-command to sub-nodes of condition, ifBody or elseBody
    else if (node <= @condition)
      player.tree.tree[@condition].mark(player, node)
    else if (node <= @ifBody)
      player.tree.tree[@ifBody].mark(player, node)
    else
      player.tree.tree[@elseBody].mark(player, node)

  toJSON: ->
    {
    nid: @nid
    node: 'if'
    condition: @condition
    ifBody: @ifBody
    elseBody: @elseBody
    op: @op
    }

  @parse: (node, tree, memory) =>
    # parse condition node
    condition = BlockNode.parse(@findSubNode(node, '.if-condition'), tree, memory)
    tree.push condition
    # parse if node
    ifBody = BlockNode.parse(@findSubNode(node, '.if-body'), tree, memory)
    tree.push ifBody
    # parse else node
    elseBody = BlockNode.parse(@findSubNode(node, '.if-else'), tree, memory)
    tree.push elseBody
    # deal with condition nodes
    size = condition.size()
    @validate(node, size > 0)
    # parse operator
    op = @findSubNode(node, '.if-operator')
    if (size > 1) then op.show()
    else op.hide()
    # create node
    nid = tree.length
    new @(nid, condition.nid, ifBody.nid, elseBody.nid, op.val())

class IncNode extends Node
  constructor: (@nid, @variable, @operator) ->

  execute: (player, node) ->
    vid = @variable.vid
    # increment value of variable
    value = @executeValue(@variable, player)
    if (@operator is 'inc') then newValue = value + 1
    else newValue = value - 1

    if (@variable.kind is 'index')
      index = @executeValue(@variable.index, player)
      player.memory.arraySet(vid, index, newValue)
      player.stats.writeArrayVar(vid, index, newValue)
    else
      player.memory.set(vid, newValue)
      player.stats.writeVar(vid, newValue)
    # return the value before incrementing (like i++)
    {value: value}

  toJSON: ->
    {
    nid: @nid
    node: 'inc'
    var: @variable
    operator: @operator
    }

  @parse: (node, tree, memory) =>
    variable = @parseAndCheckValue('.inc-var', node, memory)
    @validate(node, variable?)
    operator = @findSubNode(node, '.inc-operation').val()
    nid = tree.length
    new @(nid, variable, operator)

class ReturnNode extends Node
  constructor: (@nid, @value) ->

  execute: (player, node) ->
    value = @executeValue(@value, player)
    $('#returnValue').val(value)
    -1 # no further steps

  toJSON: ->
    {
    nid: @nid
    node: 'return'
    value: @value
    }

  @parse: (node, tree, memory) =>
    value = @parseAndCheckValue('.return-value', node, memory)
    @validate(node, value?)
    nid = tree.length
    new @(nid, value)

class SwapNode extends Node
  constructor: (@nid, @left, @right) ->

  execute: (player, node) ->
    # get values
    leftVal = @executeValue(@left, player)
    rightVal = @executeValue(@right, player)
    # write values
    @writeVar(@left, rightVal, player)
    @writeVar(@right, leftVal, player)
    {}

  toJSON: ->
    {
    nid: @nid
    node: 'swap'
    left: @left
    right: @right
    }

  @parse: (node, tree, memory) =>
    left = @parseAndCheckValue('.swap-left', node, memory)
    right = @parseAndCheckValue('.swap-right', node, memory)
    @validate(node, left? and right?)
    nid = tree.length
    new @(nid, left, right)

class ValueNode extends Node
  constructor: (@nid, @value) ->

  execute: (player, node) ->
    {value: @executeValue(@value, player)}

  toJSON: ->
    {
    nid: @nid
    node: 'value'
    value: @value
    }

  @parse: (node, tree, memory) =>
    value = @parseAndCheckValue('.value-var', node, memory)
    @validate(node, value?)
    nid = tree.length
    new @(nid, value)

class WhileNode extends Node
  constructor: (@nid, @condition, @body, @op) ->

  execute: (player, node) ->
    # find matching sub-node
    if (node <= @condition)
      cond = player.tree.tree[@condition]
      # execute the condition
      size = cond.size()
      if (size is 1) then condValue = cond.execute(player, node)
      else if (size > 1) then condValue = cond.executeAll(player, node, @op)
      else throw new ExecutionError('no_condition', [])
      # define the next step
      if condValue.next > -1 then next: condValue.next
      else if condValue.value then next: @body # if condition is true, next node should be the body
      else {} # otherwise don't return a next node in order to mark this node as done
    else if (node <= @body)
      bodyValue = player.tree.tree[@body].execute(player, node)
      if bodyValue.next? then next: bodyValue.next # if body has still more nodes to execute, let it!
      else next: @condition # otherwise jump back to condition

  mark: (player, node) ->
    # mark condition if the WhileNode should be marked
    if (node is @nid)
      player.tree.tree[@condition].mark(player, @condition)
      # redirect mark-command to sub-nodes of condition, ifBody or elseBody
    else if (node <= @condition)
      player.tree.tree[@condition].mark(player, node)
    else
      player.tree.tree[@body].mark(player, node)

  toJSON: ->
    {
    nid: @nid
    node: 'while'
    condition: @condition
    body: @body
    op: @op
    }

  @parse: (node, tree, memory) =>
    # parse condition node
    condition = BlockNode.parse(@findSubNode(node, '.while-condition'), tree, memory)
    tree.push condition
    # parse body node
    body = BlockNode.parse(@findSubNode(node, '.while-body'), tree, memory)
    tree.push body
    # deal with condition nodes
    size = condition.size()
    @validate(node, size > 0)
    # parse operator
    op = @findSubNode(node, '.while-operator')
    if (size > 1) then op.show()
    else op.hide()
    # create node
    nid = tree.length
    new @(nid, condition.nid, body.nid, op.val())

class window.Tree
  constructor: ->
    @reset()

  executeStep: (player, node) ->
    @tree[@root].execute(player, node)

  mark: (player, node) ->
    @tree[@root].mark(player, node)

  get: (nid) ->
    @tree[nid]

  reset: () ->
    @memory = new Memory($('.variables>tbody'))
    @tree = []
    rootNode = BlockNode.parse(SCRIPTSITE, @tree, @memory)
    @root = @tree.length
    @tree.push rootNode

  toJSON: ->
    json = []
    for node, i in @tree
      json[i] = node.toJSON()
    json

  @toJSON: ->
    new @().toJSON()

class window.Memory
  constructor: (@table) ->
    @memory = []
    @original = []
    @table.children().not('#var-prototype').each((index, element) =>
      vid = $(element).data('vid')
      name = $(element).data('name')
      value = $(element).data('value')
      array = $(element).data('type').substr(0, 5) is 'array'
      @memory[vid] =
        vid: vid, name: name, value: value, array: array, count: 0
      @original[vid] =
        vid: vid, name: name, value: value, array: array, count: 0
    )

  count: (vid) =>
    variable = @memory[vid]
    ++variable.count

  find: (name) =>
    vid = -1 # return a not-found-value
    $.each(@memory, (index, elem) ->
      vid = elem.vid if (elem.name is name)
    )
    vid

  get: (vid) =>
    @memory[vid]

  set: (vid, value) =>
    try
      value.split(',')
      @memory[vid].array = true
    catch error
      @memory[vid].array = false
    @memory[vid].value = value

  arrayCheck: (vid, index) =>
    variable = @get(vid)
    # check if the variable is an array
    if (!variable.array)
      throw new ExecutionError('no_array_for_index', [variable.name])
    array = variable.value.split(',')
    # check if the array is long enough
    if (index < 0 or array.length <= index)
      throw new ExecutionError('index_out_of_bounds', [variable.name, index, array.length])
    array

  arrayGet: (vid, index) =>
    array = @arrayCheck(vid, index)
    value = array[index]
    if (parseInt(value) + '' is value) then parseInt(value)
    else value

  arraySet: (vid, index, value) =>
    array = @arrayCheck(vid, index)
    array[index] = value
    @set(vid, array.join(','))

  reset: =>
    $.each(@original, (index, elem) =>
      @memory[index].value = elem.value
      @memory[index].count = 0
    )

class window.ExecutionError extends Error
  constructor: (@message, @parts) ->