# TODO handler
#script = new window.Tree().parseRoot()

class window.Tree
  parse: (node) =>
    # extract the type
    type = node.data('node-type')

    # call proper parsing method
    switch type
      when 'assign' then response = @parseAssign node
      when 'compare' then response = @parseCompare node
      when 'constant' then response = @parseConstant node
      when 'if' then response = @parseIf node
      when 'var' then response = @parseVar node
      when 'while' then response = @parseWhile node
      else console.error "Parse error: Unknown type: '#{type}'"
    response

  parseRoot: ->
    # prepare return value
    script = {}
    # loop level-1 elements:
    $('#placeAlgorithmHere > tbody > tr').each((index, element) =>
      # find the table element that represents one node
      node = $(element).find('.step:first')
      # and parse the node
      script[index] = @parse node
    )
    script

  parseAssign: (node) =>
    console.log 'parsing assign...'
    from = @parse @findSubNode(node, '.assign-from')
    to = @parse @findSubNode(node, '.assign-to')

    {
      node: 'assign'
      from: from
      to: to
    }

  parseCompare: (node) =>
    console.log 'parsing compare...'
    left = @parse @findSubNode(node, '.compare-left')
    right = @parse @findSubNode(node, '.compare-right')
    operator = node.find('.compare-operation:first').val()

    {
      node: 'compare'
      left: left
      right: right
      operator: operator
    }

  parseConstant: (node) =>
    console.log 'parsing constant...'
    value = node.find('.constant-value:first').val()

    {
      node: 'constant'
      value: value
    }

  parseIf: (node) =>
    console.log 'parsing if...'
    condition = @parse @findSubNode(node, '.if-condition')
    body = @parse @findSubNode(node, '.if-body')

    {
      node: 'if'
      condition: condition
      body: body
    }

  parseVar: (node) =>
    console.log 'parsing var...'
    variable = node.find('.var-value:first').val()

    {
      node: 'var'
      var: variable
    }

  parseWhile: (node) ->
    console.log 'parsing while...'
    'while'

  findSubNode: (node, _class) ->
    node.find(_class + ':first > table')