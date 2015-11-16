class Node
  ###
    Must be overridden by all subclasses.
  ###
  execute: (player, node) ->
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

    switch value.kind
      when 'const' then value.value

      when 'index'
        @readVar(value, player)

      when 'prop'
        if (value.prop is 'length')
          variable = player.memory.get(value.vid)
          if (variable.array) then variable.value.split(',').length
          else 1
        else
          throw new ExecutionError('unknown_property', [value.prop])

      when 'var'
        @readVar(value, player)

      when 'comp'
        leftVal = @executeValue(value.left, player)
        rightVal = @executeValue(value.right, player)
        player.stats.incArithmeticLogicOps()
        switch value.op
          when '+' then leftVal + rightVal
          when '-' then leftVal - rightVal
          when '*' then leftVal * rightVal
          when '/'
            throw new ExecutionError('divide_by_zero', []) if (rightVal is 0)
            parseInt(leftVal / rightVal)
          when '%' then leftVal % rightVal
          when '&' then leftVal and rightVal
          when '|' then leftVal or rightVal
          else
            throw new ExecutionError('unknown_arithmetic_op', [@operator])

      else
        throw new ExecutionError('unknown_kind', [value.kind])

  readVar: (source, player) ->
    switch (source.kind)
      when 'index'
        index = @executeIndex(source.index, player)
        player.stats.readArrayVar(source.vid, index)
        player.memory.arrayGet(source.vid, index)
      when 'var'
        vid = source.vid
        player.stats.readVar(vid) # tell the stats, that a variable has been read
        player.memory.get(vid).value # return the current value of the variable
      else throw new ExecutionError('unknown_kind', [source.kind])

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
      else throw new ExecutionError('unknown_kind', [destination.kind])

  ###
    Sets the cursor to the position of the node. Override to place it somewhere else!
  ###
  mark: (player, node) ->
    if (node is @nid)
      player.setCursor(@nid, 0)
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
      when 'function' then FunctionNode.parse(node, tree, memory)
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

  @parseAndCheckVar: (_class, node, memory) ->
    node = @findSubNode(node, _class)
    value = @parseValue(node.val(), memory)
    if value? and (value.kind is 'var' or value.kind is 'index')
      node.removeClass('error')
      value
    else
      node.addClass('error')
      null

  @parseValue: (value, memory) ->
    value = $.trim(value)
    # check for constants
    if (constant = DataType.parse(value))
      return {kind: 'const', type: constant.type, value: constant.value}

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
    if (/^[A-Za-z]+$/.test(value))
      vid = memory.find(value)
      if (vid > -1)
        memory.count(vid)
        return {kind: 'var', vid: vid}
      else return null

    value = value.replace(/\s*/g, '') # remove white spaces
    # check for simple computations (e.g. i+1)
    if (value.indexOf('(') is -1)
      split = value.split(/(-|\+|\*|\/|%|&|\|)/i)
      if (split.length is 3) # e.g. "i-1"
        left = @parseValue(split[0], memory)
        right = @parseValue(split[2], memory)
        if (left? and right? and "+-*/%&|".indexOf(split[1]) >= 0)
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
          if (left? and right? and "+-*/%&|".indexOf(value[1]) >= 0)
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
    split = value.split(/(-|\+|\*|\/|%|&|\||\(|\))/g)
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
    node = player.tree.get(@from).execute(player, 0)
    if (!node.scope?)
      # write new value
      @writeVar(@to, node.value, player)
    # return value
    node

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
    to = @parseAndCheckVar('.assign-to', node, memory)
    @validate(node, to? and from.size())
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
        curNode = player.tree.get(n).execute(player, node)
        break
    # use the value if there is one
    if (curNode?.value?) then value = curNode.value
    else value = false
    # compute next node
    if (curNode?.scope?) # check for function call
      curNode
    else if (curNode?.next?)
      { next: curNode.next, value: value }
    else if (curNode is -1) # return node was executed
      { next: -1, value: value }
    else if (@nodes.length > i + 1) # if curNode is done, take next from nodes
      { next: @nodes[i + 1], value: value }
    else
      { value: value }

  executeAll: (player, node, combine) ->
    value = combine is 'all'
    next = -1
    for n,i in @nodes
      if (node < n)
        next = n
        break
      else
        curValue = player.tree.get(n).execute(player, n).value
        value = value and curValue if (combine is 'all')
        value = value or curValue if (combine is 'any')
        break if (window.defaults.shortCircuit and not value)
    {value: value, next: next}

  mark: (player, node) ->
    # if BlockNode itself should be marked...
    if (node is @nid)
      # ... mark first child that wants
      for n,i in @nodes
        marked = player.tree.get(n).mark(player, n)
        return marked if marked > -1
      return -1
      # if a child node should be marked...
    else
      for n,i in @nodes
        # ... find suitable child and try marking it
        if (node <= n)
          marked = player.tree.get(n).mark(player, node)
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
      tree.push(child)
      # store each child-nid in block-node
      nodes[index] = child.nid
    )
    nid = tree.length
    new @(nid, nodes)

class CommentNode extends Node
  constructor: (@nid, @comment) ->

  execute: (player, node) ->
    {value: false}

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
      else throw new Error("CompareNode: unknown operator: '#{@operator}'!")

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

class FunctionNode extends Node
  constructor: (@nid, @callee, @paramsLine, @params) ->

  execute: (player, node) ->
    scope = player.scope
    curNode = $('#scope-' + scope + ' .node_' + @nid)
    if (curNode.data('return-value')?)
      # return value has already been computed
      value = curNode.data('return-value')
      # remove value once executed
      curNode.removeData('return-value')
      return { value: value }

    # otherwise call function
    @callFunction(player, curNode)

  callFunction: (player, node) ->
    newScope = player.scope + 1
    # prepare new scope
    scope = $('#proto-scope-' + @callee).clone(true, true).attr('id', 'scope-' + newScope)
    head = $('<a/>').data('target', '#scope-' + newScope).addClass('scope-' + newScope)
    head.attr('aria-controls', 'scope-' + newScope).attr('role', 'tab').attr('data-toggle', 'tab')
    head.text(scope.find('.algorithm-name').text())
    $('#scopes-head').append($('<li/>').attr('role', 'presentation').append(head))
    $('#scopes-body').append(scope)
    # compute active parameters
    params = @paramsLine
    { scope: newScope, node: @nid, params: params }

  toJSON: ->
    {
    nid: @nid
    node: 'function'
    callee: @callee
    paramsLine: @paramsLine
    params: @params
    }

  @parse: (node, tree, memory) =>
    # get callee
    callee = node.data('callee-id')
    # parse parameters from input field
    paramsLine = []
    paramsRaw = @findSubNode(node, '.act-pars-line').val()
    paramsLineError = false
    if (paramsRaw isnt '')
      for par in paramsRaw.split(';')
        par = @parseValue(par, node, memory)
        if (!par?) then paramsLineError = true
        else paramsLine.push(par)
    # parse parameters from sub-nodes
    params = BlockNode.parse(@findSubNode(node, '.act-pars'), tree, memory)
    tree.push(params)
    # validation
    @validate(node, callee > 0 and !paramsLineError)
    # create the node
    nid = tree.length
    new @(nid, callee, paramsLine, params.nid)

class IfNode extends Node
  constructor: (@nid, @condition, @ifBody, @elseBody, @op) ->

  execute: (player, node) ->
    # find matching sub-node
    if (node <= @condition)
      cond = player.tree.get(@condition)
      # execute the condition
      size = cond.size()
      if (size is 1) then condRetVal = cond.execute(player, node)
      else if (size > 1) then condRetVal = cond.executeAll(player, node, @op)
      else throw new ExecutionError('no_condition', [])
      # compute condition (only 0 and false are interpreted as false)
      value = condRetVal.value + 0 isnt 0
      # define the next step
      if condRetVal.next > -1 then { next: condRetVal.next, value: value }
      else if value then { next: @ifBody, value: value }
      else { next: @elseBody, value: value }
    else if (node <= @ifBody)
      player.tree.get(@ifBody).execute(player, node)
    else
      player.tree.get(@elseBody).execute(player, node)

  mark: (player, node) ->
    # mark condition if the IfNode should be marked
    if (node is @nid)
      player.tree.get(@condition).mark(player, @condition)
      # redirect mark-command to sub-nodes of condition, ifBody or elseBody
    else if (node <= @condition)
      player.tree.get(@condition).mark(player, node)
    else if (node <= @ifBody)
      player.tree.get(@ifBody).mark(player, node)
    else
      player.tree.get(@elseBody).mark(player, node)

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
    variable = @parseAndCheckVar('.inc-var', node, memory)
    @validate(node, variable?)
    operator = @findSubNode(node, '.inc-operation').val()
    nid = tree.length
    new @(nid, variable, operator)

class ReturnNode extends Node
  constructor: (@nid, @value) ->

  execute: (player, node) ->
    value = @executeValue(@value, player)
    $('#scope-' + player.scope + ' .return-value').val(value).focus()
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
    {value: leftVal isnt rightVal}

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
      cond = player.tree.get(@condition)
      # execute the condition
      size = cond.size()
      if (size is 1)
        condValue = cond.execute(player, node)
      else if (size > 1)
        condValue = cond.executeAll(player, node, @op)
      else throw new ExecutionError('no_condition', [])
      # define the next step
      if condValue.next > -1 then next: condValue.next
      else if condValue.value then next: @body # if condition is true, next node should be the body
      else {} # otherwise don't return a next node in order to mark this node as done
    else if (node <= @body)
      bodyValue = player.tree.get(@body).execute(player, node)
      if (bodyValue.scope?) # check for function call
        bodyValue
      else if bodyValue.next?
        next: bodyValue.next # if body has still more nodes to execute, let it!
      else
        next: @condition # otherwise jump back to condition

  mark: (player, node) ->
    condition = player.tree.get(@condition)
    if (node is @nid)
      # mark condition if the WhileNode should be marked
      condition.mark(player, @condition)
    else if (node <= @condition)
      # redirect mark-command to sub-nodes of condition
      condition.mark(player, node)
    else
      # redirect mark-command to sub-nodes of body
      body = player.tree.get(@body)
      if (body.size()) then body.mark(player, node) # deal with empty body
      else condition.mark(player, @condition)

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
  constructor: (@scope) ->
    @memory = new Memory($('#scope-' + @scope + ' .variables>tbody'))
    @reset()

  execute: (player, node) ->
    @get(@root).execute(player, node)

  mark: (player, node) ->
    @get(@root).mark(player, node)

  get: (nid) ->
    @nodes[nid]

  reset: ->
    @memory.reset()
    @nodes = []
    rootNode = BlockNode.parse($('#scope-' + @scope + ' .node_root'), @nodes, @memory)
    @root = @nodes.length
    @nodes.push rootNode

  toJSON: ->
    json = []
    for node, i in @nodes
      json[i] = node.toJSON()
    json

  @toJSON: ->
    new @(0).toJSON()

class window.Memory
  constructor: (@table) ->
    @memory = new Object()
    @original = new Object()
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

class window.DataType
  @parse: (value) ->
    # check for number
    intVal = parseInt(value)
    if (intVal + "" is value)
      return {type: 'int', value: intVal}

    # check for boolean
    boolVal = value.toLowerCase()
    if (boolVal is 'true' or boolVal is 'false')
      return {type: 'bool', value: boolVal is 'true'}

    # check for character TODO: data-type char
    # if (/^'[A-Za-z]'$/.test(value))
    #   return {kind: 'const', type: 'char', value: value}

    false

class window.ExecutionError extends Error
  constructor: (@message, @parts) ->