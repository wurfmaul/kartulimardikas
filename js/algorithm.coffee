window.SCRIPTSITE = $(".insertStepsHere") # Specifies the site, where variables are to place.

class Node

  ###
  # Must be overridden by all subclasses.
  ###
  execute: (player) ->
    throw new Exception('Node must override execute() method!')

  ###
  # Must be overridden by all subclasses. Sets the cursor to the position
  # of the node, or places it inside.
  ###
  mark: (player) ->
    throw new Exception('Node must override mark() method!')

  ###
  # Must be overridden by all subclasses.
  ###
  toJSON: ->
    throw new Exception('Node must override toJSON() method!')

  ###
  # For combo boxes: Inspects the given value and defines its kind
  # and properties.
  ###
  @checkAndExtract: (value, memory) ->
    # check for const (int)
    intVal = parseInt(value)
    if (intVal + "" is value)
      return {kind: 'const', type: 'int', value: intVal}
    # check for array ([])
    open = value.indexOf('[')
    close = value.lastIndexOf(']')
    if (open > -1 and close > open)
      vid = memory.find(value.substr(0, open))
      inner = @checkAndExtract(value.substr(open + 1, close - open - 1), memory)
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
    # return null, if value is not valid
    null

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
  @parse: (node, tree, memory) ->
    # extract the type
    type = node.data('node-type')

    # call proper parsing method
    switch type
      when 'arithmetic' then ArithmeticNode.parse(node, tree, memory)
      when 'assign' then AssignNode.parse(node, tree, memory)
      when 'compare' then CompareNode.parse(node, tree, memory)
      when 'constant' then ConstantNode.parse(node, tree, memory)
      when 'if' then IfNode.parse(node, tree, memory)
      when 'inc' then IncNode.parse(node, tree, memory)
      when 'var' then VarNode.parse(node, tree, memory)
      when 'while' then WhileNode.parse(node, tree, memory)
      else
        throw new Exception("Parse error: Unknown type: '#{type}'")

class ArithmeticNode extends Node
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
      when 'plus' then leftVal + rightVal
      when 'minus' then leftVal - rightVal
      when 'times' then leftVal * rightVal
      when 'by' then leftVal / rightVal
      when 'mod' then leftVal % rightVal
      else
        throw new Exception("Unknown operator: '#{@operator}'")

  mark: (player) ->
    player.setCursor(@nid)
    @nid

  toJSON: ->
    {
    nid: @nid
    node: 'arithmetic'
    left: @left
    right: @right
    operator: @operator
    }

  @parse: (node, tree, memory) =>
    # parse left node
    left = BlockNode.parse(@findSubNode(node, '.arithmetic-left'), tree)
    tree.push left
    # parse right node
    right = BlockNode.parse(@findSubNode(node, '.arithmetic-right'), tree)
    tree.push right
    # extract operator
    operator = node.find('.arithmetic-operation:first').val()
    # create node
    nid = tree.length
    new @(nid, left.nid, right.nid, operator)

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

  @parse: (node, tree, memory) =>
    # parse from-node
    from = BlockNode.parse(@findSubNode(node, '.assign-from'), tree, memory)
    tree.push from
    # parse to-node
    to = @checkAndExtract(@findSubNode(node, '.assign-to').val(), memory)
    # create the node
    nid = tree.length
    new @(nid, from.nid, to)

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

class CompareNode extends Node
  constructor: (@nid, @left, @right, @operator) ->

  check: (tree) -> true

  execute: (player, node) ->
    leftVal = 0
    rightVal = 0
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

  @parse: (node, tree, memory) =>
    left = @checkAndExtract(@findSubNode(node, '.compare-left').val(), memory)
    right = @checkAndExtract(@findSubNode(node, '.compare-right').val(), memory)
    operator = @findSubNode(node, '.compare-operation').val()
    nid = tree.length
    new @(nid, left, right, operator)

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

  @parse: (node, tree, memory) =>
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
    # create node
    nid = tree.length
    new @(nid, condition.nid, ifBody.nid, elseBody.nid)

class IncNode extends Node
  constructor: (@nid, @variable) ->

  execute: (player, node) ->
    @vid = 0
    # increment value of variable
    value = parseInt(player.memory.get(@vid))
    player.memory.set(@vid, value + 1)
    # return the value before incrementing (like i++)
    value

  toJSON: ->
    {
    nid: @nid
    node: 'inc'
    var: @variable
    }

  @parse: (node, tree, memory) =>
    variable = @checkAndExtract(@findSubNode(node, '.inc-var').val(), memory)
    nid = tree.length
    new @(nid, variable)

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

  @parse: (node, tree, memory) =>
    vid = node.find('.var-value > :selected').val()
    nid = tree.length
    new @(nid, vid)

class WhileNode extends Node
  constructor: (@nid, @condition, @body) ->

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
    node: 'while'
    condition: @condition
    body: @body
    }

  @parse: (node, tree, memory) =>
    # parse condition node
    condition = BlockNode.parse(@findSubNode(node, '.while-condition'), tree, memory)
    tree.push condition
    # parse body node
    body = BlockNode.parse(@findSubNode(node, '.while-body'), tree, memory)
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
    @table.children(':visible').each((index, element) =>
      vid = $(element).data('vid')
      variable =
        vid: vid
        name: $(element).data('name')
        value: $(element).data('value')
        count: 0
      @memory[vid] = variable
      @original[vid] = variable
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
    @memory[vid] = value

  reset: =>
    @table.children(':visible').each((index, element) =>
      vid = $(element).data('vid')
      $(element).find('.value').val(@original[vid].value)
      @memory[vid] = @original[vid]
    )