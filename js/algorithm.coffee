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

class AssignNode extends Node
  @parse: (node) =>
    from = Tree.parseBody(@findSubNode(node, '.assign-from'))
    to = Tree.parseBody(@findSubNode(node, '.assign-to'))

    {
    node: 'assign'
    from: from
    to: to
    }

class CompareNode extends Node
  @parse: (node) =>
    left = Tree.parseBody(@findSubNode(node, '.compare-left'))
    right = Tree.parseBody(@findSubNode(node, '.compare-right'))
    operator = node.find('.compare-operation:first').val()

    {
    node: 'compare'
    left: left
    right: right
    operator: operator
    }

class ConstantNode extends Node
  @parse: (node) =>
    value = node.find('.constant-value:first').val()

    {
    node: 'constant'
    value: value
    }

class IfNode extends Node
  @parse: (node) =>
    condition = Tree.parseBody(@findSubNode(node, '.if-condition'))
    ifBody = Tree.parseBody(@findSubNode(node, '.if-body'))
    elseBody = Tree.parseBody(@findSubNode(node, '.if-else'))

    {
    node: 'if'
    condition: condition
    ifBody: ifBody
    elseBody: elseBody
    }

class VarNode extends Node
  @parse: (node) =>
    variable = node.find('.var-value > :selected')

    {
    node: 'var'
    vid: variable.val()
    }

class WhileNode extends Node
  @parse: (node) =>
    condition = Tree.parseBody(@findSubNode(node, '.while-condition'))
    body = Tree.parseBody(@findSubNode(node, '.while-body'))

    {
    node: 'while'
    condition: condition
    body: body
    }

class window.Tree
  @parseRoot: =>
    # if the main body is empty, return empty string, parse tree otherwise
    if ($(SCRIPTSITE).filter(':empty').length) then ""
    else @parseBody(SCRIPTSITE)

  @parseBody: (node) =>
    # prepare return value
    script = {}
    # loop level-1 elements:
    node.children('li').each((index, element) =>
      script[index] = Node.parse($(element))
    )
    script