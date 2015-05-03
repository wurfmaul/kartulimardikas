window.SCRIPTSITE = $(".insertStepsHere") # Specifies the site, where variables are to place.

class Node

  ###
  # Must be overridden by all subclasses.
  ###
  execute: (player) ->
    throw new Exception('Node must override execute() method!')

  mark: (player) ->
    throw new Exception('Node must override mark() method!')

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

  execute: (player, node) ->
    toNode = player.tree.extract(@to)
    fromNode = player.tree.extract(@from)
    # get new value
    if (fromNode instanceof VarNode)
      vid = fromNode.vid
      value = player.memory.get(vid)
    else
      value = fromNode.value
    # set new value
    player.memory.set(toNode.vid, value)
    player.stats.incWriteOps()
    # return null to mark as done
    null

  mark: (player) ->
    player.setCursor(@nid)
    @nid

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
    @curNode = 0

  execute: (player, node) ->
    nextNode = null
    # find matching sub-node
    for n,i in @nodes
      if (node <= n)
        nextNode = player.tree.extract(n).execute(player, node)
        break
    # compute next node
    if nextNode? then nextNode
    else @nodes[i + 1] # if curNode is done, take next from nodes

  mark: (player) ->
    first = player.tree.extract(@nodes[0])
    first?.mark(player)

  size: ->
    @nodes.length

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
    node.children('li.node').each((index, element) =>
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

  check: (tree) ->
    # check for right dimensions
    return false if (tree[@left].size() != 1 or tree[@right].size() != 1)
    # extract children
    left = tree.extract(@left)
    right = tree.extract(@right)
    # check for right classes
    (left instanceof VarNode or left instanceof ConstantNode) and
      (right instanceof VarNode or right instanceof ConstantNode)

  execute: (player, node) ->
    left = player.tree.extract(@left)
    right = player.tree.extract(@right)
    leftVal = left.execute(player, node)
    rightVal = right.execute(player, node)
    player.stats.incCompareOps()
    switch @operator
      when 'le' then leftVal <= rightVal
      when 'lt' then leftVal < rightVal
      when 'eq' then leftVal == rightVal
      when 'gt' then leftVal > rightVal
      when 'ge' then leftVal >= rightVal
      when 'ne' then leftVal != rightVal
      else
        throw new Exception("Unknown operator: '#{@operator}'")

  mark: (player) ->
    player.setCursor(@nid)
    @nid

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

  execute: (player, node) ->
    parseInt(@value)

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

  execute: (player, node) ->
    # find matching sub-node
    if (node <= @condition)
      if player.tree.extract(@condition).execute(player, node) then @ifBody
      else @elseBody
    else if (node <= @ifBody)
      player.tree.extract(@ifBody).execute(player, node)
    else
      player.tree.extract(@elseBody).execute(player, node)

  mark: (player) ->
    condition = player.tree.extract(@condition)
    condition.mark(player)

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

  execute: (player, node) ->
    parseInt(player.memory.get(@vid))

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

  toJSON: ->
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
    @reset()

  executeStep: (player, node) ->
    @tree[@root].execute(player, node)

  extract: (nid) ->
    node = @tree[nid]
    if (node instanceof BlockNode and node.size() == 1) then @tree[node.nodes[0]]
    else node

  reset: () ->
    @tree = []
    rootNode = BlockNode.parse(SCRIPTSITE, @tree)
    @root = @tree.length
    @tree.push rootNode

  toJSON: ->
    json = []
    for node, i in @tree
      json[i] = node.toJSON()
    json

  @toJSON: ->
    new @().toJSON()