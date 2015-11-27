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

class window.ExecutionError extends Error
  constructor: (@message, @parts) ->