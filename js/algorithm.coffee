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
  @parse: (node, tree) ->
    # extract the type
    type = node.data('node-type')

    # call proper parsing method
    switch type
      when 'assign' then AssignNode.parse(node, tree)
      when 'compare' then CompareNode.parse(node, tree)
      when 'constant' then ConstantNode.parse(node, tree)
      when 'if' then IfNode.parse(node, tree)
      when 'var' then VarNode.parse(node, tree)
      when 'while' then WhileNode.parse(node, tree)
      else
        throw new Exception("Parse error: Unknown type: '#{type}'")

class AssignNode extends Node
  constructor: (@nid, @from, @to) ->

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
      vid = fromNode.vid
      value = player.memory.get(vid)
    else
      throw new Exception('constants not implemented yet')
    #set new value
    player.memory.set(toNode.vid, value)
    # return next node-id
    toNode.nid + 1

  toJSON: ->
    {
    nid: @nid
    node: 'assign'
    from: @from
    to: @to
    }

  @parse: (node, tree) =>
    # parse from-node
    from = BlockNode.parse(@findSubNode(node, '.assign-from'), tree)
    tree.push from
    # parse to-node
    to = BlockNode.parse(@findSubNode(node, '.assign-to'), tree)
    tree.push to
    # create the node
    nid = tree.length
    new @(nid, from.nid, to.nid)

class BlockNode extends Node
  constructor: (@nid, @nodes) ->

  execute: (player) ->

  toJSON: ->
    {
    nid: @nid
    node: 'block'
    nodes: @nodes
    }

  @parse: (node, tree) =>
    # prepare return value
    nodes = []
    # loop level-1 elements:
    node.children('li').each((index, element) =>
      # parse child and add it to tree
      child = Node.parse($(element), tree)
      tree.push child
      # store each child-nid in block-node
      nodes[index] = child.nid
    )
    nid = tree.length
    new @(nid, nodes)

class CompareNode extends Node
  constructor: (@nid, @left, @right, @operator) ->

  execute: (player) ->

  toJSON: ->
    {
    nid: @nid
    node: 'compare'
    left: @left
    right: @right
    operator: @operator
    }

  @parse: (node, tree) =>
    # parse left node
    left = BlockNode.parse(@findSubNode(node, '.compare-left'), tree)
    tree.push left
    # parse right node
    right = BlockNode.parse(@findSubNode(node, '.compare-right'), tree)
    tree.push right
    # extract operator
    operator = node.find('.compare-operation:first').val()
    # create node
    nid = tree.length
    new @(nid, left.nid, right.nid, operator)

class ConstantNode extends Node
  constructor: (@nid, @value) ->

  toJSON: ->
    {
    nid: @nid
    node: 'constant'
    value: @value
    }

  @parse: (node, tree) =>
    value = node.find('.constant-value:first').val()
    nid = tree.length
    new @(nid, value)

class IfNode extends Node
  constructor: (@nid, @condition, @ifBody, @elseBody) ->

  execute: (player) ->

  toJSON: ->
    {
    nid: @nid
    node: 'if'
    condition: @condition
    ifBody: @ifBody
    elseBody: @elseBody
    }

  @parse: (node, tree) =>
    # parse condition node
    condition = BlockNode.parse(@findSubNode(node, '.if-condition'), tree)
    tree.push condition
    # parse if node
    ifBody = BlockNode.parse(@findSubNode(node, '.if-body'), tree)
    tree.push ifBody
    # parse else node
    elseBody = BlockNode.parse(@findSubNode(node, '.if-else'), tree)
    tree.push elseBody
    # create node
    nid = tree.length
    new @(nid, condition.nid, ifBody.nid, elseBody.nid)

class VarNode extends Node
  constructor: (@nid, @vid) ->

  toJSON: ->
    {
    nid: @nid
    node: 'var'
    vid: @vid
    }

  @parse: (node, tree) =>
    vid = node.find('.var-value > :selected').val()
    nid = tree.length
    new @(nid, vid)

class WhileNode extends Node
  constructor: (@nid, @condition, @body) ->

  toJSON: (tree) ->
    {
    nid: @nid
    node: 'while'
    condition: @condition
    body: @body
    }

  @parse: (node, tree) =>
    # parse condition node
    condition = BlockNode.parse(@findSubNode(node, '.while-condition'), tree)
    tree.push condition
    # parse body node
    body = BlockNode.parse(@findSubNode(node, '.while-body'), tree)
    tree.push body
    # create node
    nid = tree.length
    new @(nid, condition.nid, body.nid)

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

  @parseRoot: =>
    # prepare return value
    tree = []
    root = BlockNode.parse(SCRIPTSITE, tree)
    rootNid = tree.length
    tree[rootNid] = root
    tree

  @toJSON: ->
    new @().toJSON()