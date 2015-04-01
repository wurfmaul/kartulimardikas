window.SCRIPTSITE = $("#insertStepsHere") # Specifies the site, where variables are to place.

class Node

  ###
  # Must be overridden by all subclasses.
  ###
  execute: (player) ->
    throw new Exception('Node must override execute() method!')

  ###
  # Must be overridden by all subclasses.
  ###
  toJSON: ->
    throw new Exception('Node must override toJSON() method!')

  ###
  # Returns node's first sub-node of class _class.
  ###
  @findSubNode: (node, _class) ->
    node.find(_class + ':first')

  ###
  # Delegates the parse function to the right subclass.
  #
  # Must be overridden by all subclasses.
  ###
  @parse: (node) ->
    # extract the type
    type = node.data('node-type')

    # call proper parsing method
    switch type
      when 'assign' then AssignNode.parse(node)
      when 'compare' then CompareNode.parse(node)
      when 'constant' then ConstantNode.parse(node)
      when 'if' then IfNode.parse(node)
      when 'var' then VarNode.parse(node)
      when 'while' then WhileNode.parse(node)
      else
        throw new Exception("Parse error: Unknown type: '#{type}'")

  @toJSON: (nodes) ->
    json = []
    for node, i in nodes
      json[i] = node.toJSON()
    json

class AssignNode extends Node
  constructor: (@id, @from, @to) ->

  check: ->
    @from.length == 1 &&
      (@from[0] instanceof VarNode || @from[0] instanceof ConstantNode) &&
      @to.length == 1 &&
      @to[0] instanceof VarNode

  execute: (player) ->
    toNode = @to[0]
    fromNode = @from[0]
    # get new value
    value = null
    if (fromNode instanceof VarNode)
      vid = fromNode.variable
      value = player.memory.get(vid)
    else
      throw new Exception('constants not implemented yet')
    #set new value
    player.memory.set(toNode.variable, value)
    # return next node-id
    toNode.id + 1

  toJSON: ->
    {
    node: 'assign'
    from: Node.toJSON(@from)
    to: Node.toJSON(@to)
    }

  @parse: (node) =>
    id = node.data('node-id')
    from = Tree.parseBody(@findSubNode(node, '.assign-from'))
    to = Tree.parseBody(@findSubNode(node, '.assign-to'))
    new @(id, from, to)

class CompareNode extends Node
  constructor: (@id, @left, @right, @operator) ->

  execute: (player) ->
    console.log('execute cond')

  toJSON: ->
    {
    node: 'compare'
    left: Node.toJSON(@left)
    right: Node.toJSON(@right)
    operator: @operator
    }

  @parse: (node) =>
    id = node.data('node-id')
    left = Tree.parseBody(@findSubNode(node, '.compare-left'))
    right = Tree.parseBody(@findSubNode(node, '.compare-right'))
    operator = @findSubNode(node, '.compare-operation').val()
    new @(id, left, right, operator)

class ConstantNode extends Node
  constructor: (@id, @value) ->

  toJSON: ->
    {
    node: 'constant'
    value: @value
    }

  @parse: (node) =>
    id = node.data('node-id')
    value = @findSubNode(node, '.constant-value').val()
    new @(id, value)

class IfNode extends Node
  constructor: (@id, @condition, @ifBody, @elseBody) ->

  execute: (player) ->
    console.log(@condition)

  toJSON: ->
    {
    node: 'if'
    condition: Node.toJSON(@condition)
    ifBody: Node.toJSON(@ifBody)
    elseBody: Node.toJSON(@elseBody)
    }

  @parse: (node) =>
    id = node.data('node-id')
    condition = Tree.parseBody(@findSubNode(node, '.if-condition'))
    ifBody = Tree.parseBody(@findSubNode(node, '.if-body'))
    elseBody = Tree.parseBody(@findSubNode(node, '.if-else'))
    new @(id, condition, ifBody, elseBody)

class VarNode extends Node
  constructor: (@id, @variable) ->

  toJSON: ->
    {
    node: 'var'
    vid: @variable
    }

  @parse: (node) =>
    id = node.data('node-id')
    variable = node.find('.var-value > :selected').val()
    new @(id, variable)

class WhileNode extends Node
  constructor: (@id, @condition, @body) ->

  toJSON: ->
    {
    node: 'while'
    condition: Node.toJSON(@condition)
    body: Node.toJSON(@body)
    }

  @parse: (node) =>
    id = node.data('node-id')
    condition = Tree.parseBody(@findSubNode(node, '.while-condition'))
    body = Tree.parseBody(@findSubNode(node, '.while-body'))
    new @(id, condition, body)

class window.Tree
  constructor: ->
    @tree = Tree.parseRoot()

  executeStep: (player) ->
    console.log(@)
    console.log(player)
#    @prevStep = @nextStep
#    @nextStep = @tree[0]
#    @nextStep.execute(player)

  toJSON: ->
    json = []
    for node, i in @tree
      json[i] = node.toJSON()
    json

  @parseBody: (node) =>
    # prepare return value
    tree = []
    # loop level-1 elements:
    node.children('li').each((index, element) =>
      tree[index] = Node.parse($(element))
    )
    tree

  @parseRoot: =>
    # if the main body is empty, return empty string, parse tree otherwise
    if ($(SCRIPTSITE).filter(':empty').length) then ""
    else @parseBody(SCRIPTSITE)

  @toJSON: ->
    tree = new @
    tree.toJSON()