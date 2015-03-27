window.SCRIPTSITE = $("#insertStepsHere") # Specifies the site, where variables are to place.

class Node
  @parse: (node) ->
    # extract the type
    type = node.data('node-type')

    # call proper parsing method
    switch type
      when 'assign' then response = AssignNode.parse(node)
      when 'compare' then response = CompareNode.parse(node)
      when 'constant' then response = ConstantNode.parse(node)
      when 'if' then response = IfNode.parse(node)
      when 'var' then response = VarNode.parse(node)
      when 'while' then response = WhileNode.parse(node)
      else
        console.error "Parse error: Unknown type: '#{type}'"
    response

  @findSubNode: (node, _class) ->
    node.find(_class + ':first')

  @toJSON: (nodes) ->
    json = []
    for node, i in nodes
      json[i] = node.toJSON()
    json

class AssignNode extends Node
  constructor: (@from, @to) ->

  @parse: (node) =>
    from = Tree.parseBody(@findSubNode(node, '.assign-from'))
    to = Tree.parseBody(@findSubNode(node, '.assign-to'))
    new @(from, to)

  toJSON: ->
    {
    node: 'assign'
    from: Node.toJSON(@from)
    to: Node.toJSON(@to)
    }

class CompareNode extends Node
  constructor: (@left, @right, @operator) ->

  @parse: (node) =>
    left = Tree.parseBody(@findSubNode(node, '.compare-left'))
    right = Tree.parseBody(@findSubNode(node, '.compare-right'))
    operator = @findSubNode(node, '.compare-operation').val()
    new @(left, right, operator)

  toJSON: ->
    {
    node: 'compare'
    left: Node.toJSON(@left)
    right: Node.toJSON(@right)
    operator: @operator
    }

class ConstantNode extends Node
  constructor: (@value) ->

  @parse: (node) =>
    value = @findSubNode(node, '.constant-value').val()
    new @(value)

  toJSON: ->
    {
    node: 'constant'
    value: @value
    }

class IfNode extends Node
  constructor: (@condition, @ifBody, @elseBody) ->

  @parse: (node) =>
    condition = Tree.parseBody(@findSubNode(node, '.if-condition'))
    ifBody = Tree.parseBody(@findSubNode(node, '.if-body'))
    elseBody = Tree.parseBody(@findSubNode(node, '.if-else'))
    new @(condition, ifBody, elseBody)

  toJSON: ->
    console.log(@ifBody)
    {
    node: 'if'
    condition: Node.toJSON(@condition)
    ifBody: Node.toJSON(@ifBody)
    elseBody: Node.toJSON(@elseBody)
    }

class VarNode extends Node
  constructor: (@variable) ->

  @parse: (node) =>
    variable = node.find('.var-value > :selected').val()
    new @(variable)

  toJSON: ->
    {
    node: 'var'
    vid: @variable
    }

class WhileNode extends Node
  constructor: (@condition, @body) ->

  @parse: (node) =>
    condition = Tree.parseBody(@findSubNode(node, '.while-condition'))
    body = Tree.parseBody(@findSubNode(node, '.while-body'))
    new @(condition, body)

  toJSON: ->
    {
    node: 'while'
    condition: Node.toJSON(@condition)
    body: Node.toJSON(@body)
    }

class window.Tree
  constructor: ->
    @tree = Tree.parseRoot()

  toJSON: ->
    json = []
    for node, i in @tree
      json[i] = node.toJSON()
    json

  @toJSON: ->
    tree = new @
    tree.toJSON()

  @parseRoot: =>
    # if the main body is empty, return empty string, parse tree otherwise
    if ($(SCRIPTSITE).filter(':empty').length) then ""
    else @parseBody(SCRIPTSITE)

  @parseBody: (node) =>
    # prepare return value
    tree = []
    # loop level-1 elements:
    node.children('li').each((index, element) =>
      tree[index] = Node.parse($(element))
    )
    tree
