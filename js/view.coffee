class Player
  constructor: (@tree, @memory, @stats) ->
    @reset()

  reset: ->
    @tree.reset()
    @curNode = null
    @nextNode = @tree.extract(@tree.root).mark(@)
    @setControls([0, 0, 1, 1, 1])

  back: ->
    # check for prev step
#    if ($('#node_' + (@curNode - 1)).length)
#      @setControls([1, 1, 1, 1, 1])
#      @curNode--
#      @setCursor(@curNode)
#    else
#      @setControls([0, 0, 1, 1, 1])

  play: ->
    console.log(@tree)

  step: ->
    @clearHighlight()
    # execute current step
    @curNode = @nextNode
    nextNode = @tree.executeStep(@, @curNode)
    # prepare the next step
    if (nextNode?)
      @nextNode = @tree.extract(nextNode).mark(@)
      @setControls([1, 1, 1, 1, 1])
    else
      @unsetCursor()
      @setControls([1, 1, 0, 0, 0])

  finish: ->
    @unsetCursor()
    @setControls([1, 1, 0, 0, 0])

  clearHighlight: ->
    $('.highlight-write').removeClass('highlight-write')
    $('.highlight-compare').removeClass('highlight-compare')

  setControls: (settings) ->
    buttons = [$('#btn-reset'), $('#btn-back'), $('#btn-play'), $('#btn-step'), $('#btn-finish')]
    for i in [0..4]
      if (settings[i] is 0) then buttons[i].attr('disabled', 'disabled')
      else buttons[i].removeAttr('disabled')

  setCursor: (node) ->
    @unsetCursor()
    $('#node_' + node).addClass('cursor')

  unsetCursor: ->
    $('.cursor').removeClass('cursor')

class Memory
  constructor: (@table) ->
    @memory = []
    @table.children().each((index, element) =>
      vid = $(element).data('vid')
      value = $(element).find('.value').val()
      @memory[vid] = value
    )

  get: (vid) =>
    # highlight source
    @table.children('#var-' + vid).find('.value')
    .addClass('highlight-compare')
    # return value
    @memory[vid]

  set: (vid, value) =>
    @memory[vid] = value
    # change value in vars section
    @table.children('#var-' + vid).find('.value')
    .val(value)# set new value
    .addClass('highlight-write') # mark as edited

class Stats
  incWriteOps: ->
    $('#stats-now')
    .val(parseInt($('#stats-now').val()) + 1)
    .addClass('highlight-write')

  incCompareOps: ->
    $('#stats-noc')
    .val(parseInt($('#stats-noc').val()) + 1)
    .addClass('highlight-compare')

$ ->
  tree = new Tree()
  memory = new Memory($('#variables'))
  stats = new Stats()
  player = new Player(tree, memory, stats)

  $('#btn-reset').click -> player.reset()
  $('#btn-back').click -> player.back()
  $('#btn-play').click ->
    player.play()
    $('#img-play').toggleClass('glyphicon-play glyphicon-pause')
  $('#btn-step').click -> player.step()
  $('#btn-finish').click -> player.finish()