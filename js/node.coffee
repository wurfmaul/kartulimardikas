class window.Node
  ###
    Must be overridden by all subclasses.
  ###
  execute: (player, node) ->
    throw new Error('Node must override execute() method!')

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
    value = Value.parse(node.val(), memory)
    if (!value? or node.val() is '') then node.addClass('error')
    else node.removeClass('error')
    value

  @parseAndCheckVar: (_class, node, memory) ->
    node = @findSubNode(node, _class)
    value = Value.parse(node.val(), memory)
    if value? and (value.kind is 'var' or value.kind is 'index')
      node.removeClass('error')
      value
    else
      node.addClass('error')
      null

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

class window.AssignNode extends Node
  constructor: (@nid, @to, @fromNode, @fromVal) ->

  execute: (player, node) ->
    # get new value from sub-nodes
    if (@fromNode.size())
      from = @fromNode.execute(player, 0)
      return from if (from.scope?)
      value = from.value
    else
      # get new value from input box
      value = @fromVal.execute(player)
    # write new value
    Value.write(@to, value, player)
    # return value
    { value: value }

  toJSON: ->
    { i: @nid, n: 'as', t: @to?.toJSON(), f: @fromNode.nid, v: @fromVal?.toJSON() }

  @parse: (node, tree, memory) =>
    # parse to-value
    to = @parseAndCheckVar('.assign-to', node, memory)
    # parse from-node
    fromNode = BlockNode.parse(@findSubNode(node, '.assign-from'), tree, memory)
    tree.push fromNode
    # parse from-value
    fromVal = @parseAndCheckValue('.assign-from-val', node, memory)
    # validate
    @validate(node, to? and (fromNode.size() or fromVal?))
    # create the node
    nid = tree.length
    new @(nid, to, fromNode, fromVal)

class window.BlockNode extends Node
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

  size: -> @nodes.length

  toJSON: -> { i: @nid, n: 'bk', c: @nodes }

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

class window.CommentNode extends Node
  constructor: (@nid, @comment) ->

  execute: (player, node) ->
    {value: false}

  mark: (player, node) ->
    -1 # never let this node be marked

  toJSON: -> { i: @nid, n: 'cm', c: @comment }

  @parse: (node, tree, memory) =>
    comment = @findSubNode(node, '.comment-text').val()
    nid = tree.length
    new @(nid, comment)

class window.CompareNode extends Node
  constructor: (@nid, @left, @right, @operator) ->

  execute: (player, node) ->
    leftVal = @left.execute(player)
    rightVal = @right.execute(player)
    player.stats.incCompareOps()
    switch @operator
      when 'le' then value: leftVal <= rightVal
      when 'lt' then value: leftVal < rightVal
      when 'eq' then value: leftVal == rightVal
      when 'gt' then value: leftVal > rightVal
      when 'ge' then value: leftVal >= rightVal
      when 'ne' then value: leftVal != rightVal
      else throw new Error("CompareNode: unknown operator: '#{@operator}'!")

  toJSON: -> { i: @nid, n: 'cp', l: @left?.toJSON(), r: @right?.toJSON(), o: @operator }

  @parse: (node, tree, memory) =>
    left = @parseAndCheckValue('.compare-left', node, memory)
    right = @parseAndCheckValue('.compare-right', node, memory)
    @validate(node, left? and right?)
    operator = @findSubNode(node, '.compare-operation').val()
    nid = tree.length
    new @(nid, left, right, operator)

class window.FunctionNode extends Node
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

  toJSON: -> { i: @nid, n: 'ft', c: @callee, l: @paramsLine, p: @params }

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

class window.IfNode extends Node
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

  toJSON: -> { i: @nid, n: 'if', c: @condition, b: @ifBody, e: @elseBody, o: @op }

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

class window.IncNode extends Node
  constructor: (@nid, @variable, @operator) ->

  execute: (player, node) ->
    vid = @variable.vid
    # increment value of variable
    value = @variable.execute(player)
    switch (@operator)
      when 'i' then newValue = value + 1
      when 'd' then newValue = value - 1
      else throw new Error('IncNode: invalid operator ' + @operator)

    if (@variable.kind is 'index')
      index = @variable.index.execute(player)
      player.memory.arraySet(vid, index, newValue)
      player.stats.writeArrayVar(vid, index, newValue)
    else
      player.memory.set(vid, newValue)
      player.stats.writeVar(vid, newValue)
    # return the value before incrementing (like i++)
    {value: value}

  toJSON: -> { i: @nid, n: 'ic', v: @variable?.toJSON(), o: @operator }

  @parse: (node, tree, memory) =>
    variable = @parseAndCheckVar('.inc-var', node, memory)
    @validate(node, variable?)
    operator = @findSubNode(node, '.inc-operation').val()
    nid = tree.length
    new @(nid, variable, operator)

class window.ReturnNode extends Node
  constructor: (@nid, @value) ->

  execute: (player, node) ->
    value = @value.execute(player)
    $('#scope-' + player.scope + ' .return-value').val(value).focus()
    -1 # no further steps

  toJSON: -> { i: @nid, n: 'rt', v: @value?.toJSON() }

  @parse: (node, tree, memory) =>
    value = @parseAndCheckValue('.return-val', node, memory)
    @validate(node, value?)
    nid = tree.length
    new @(nid, value)

class window.SwapNode extends Node
  constructor: (@nid, @left, @right) ->

  execute: (player, node) ->
    # get values
    leftVal = @left.execute(player)
    rightVal = @right.execute(player)
    # write values
    Value.write(@left, rightVal, player)
    Value.write(@right, leftVal, player)
    {value: leftVal isnt rightVal}

  toJSON: -> { i: @nid, n: 'sw', l: @left?.toJSON(), r: @right?.toJSON() }

  @parse: (node, tree, memory) =>
    left = @parseAndCheckValue('.swap-left', node, memory)
    right = @parseAndCheckValue('.swap-right', node, memory)
    @validate(node, left? and right?)
    nid = tree.length
    new @(nid, left, right)

class window.ValueNode extends Node
  constructor: (@nid, @value) ->

  execute: (player, node) ->
    {value: @value.execute(player)}

  toJSON: -> { i: @nid, n: 'vl', v: @value?.toJSON() }

  @parse: (node, tree, memory) =>
    value = @parseAndCheckValue('.value-var', node, memory)
    @validate(node, value?)
    nid = tree.length
    new @(nid, value)

class window.WhileNode extends Node
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

  toJSON: -> { i: @nid, n: 'wl', c: @condition, b: @body, o: @op }

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

