TIMEOUT = 500 # milliseconds between the steps
MAX_STEPS = 1000 # number of steps an algorithm may take

class Player

  constructor: (@tree, @stats) ->
    @memory = @tree.memory
    @reset()

  reset: ->
    @tree.reset()
    @memory.reset()
    @stats.reset()
    @curNode = null
    @nextNode = @tree.extract(@tree.root).mark(@)
    @clearHighlight()
    @setCursor(@nextNode)
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
    if @timer? # if currently playing => pause
      # clear timer
      @timer = clearInterval(@timer);
      # set button icon to play
      $('#img-play')
      .removeClass('glyphicon-pause')
      .addClass('glyphicon-play')
    else # play
      # set an interval and perform step after step
      @timer = setInterval(=>
        @step()
      , TIMEOUT)
      # set button icon to pause
      $('#img-play')
      .removeClass('glyphicon-play')
      .addClass('glyphicon-pause')

  step: ->
    @clearHighlight()
    # execute current step
    @curNode = @nextNode
    curNode = @tree.executeStep(@, @curNode)
    # prepare the next step
    if (curNode.next? and curNode.next >= 0)
      @nextNode = @tree.tree[curNode.next].mark(@)
      @setControls([1, 1, 1, 1, 1])
      true
    else
      @play() if @timer?
      @unsetCursor()
      @setControls([1, 1, 0, 0, 0])
      false

  finish: ->
    for i in [0..MAX_STEPS]
      return if !@step()
    throw new Error("Could not terminate in #{MAX_STEPS} iterations!")

  clearHighlight: ->
    $('.highlight-write').removeClass('highlight-write')
    $('.highlight-compare').removeClass('highlight-compare')

  setControls: (settings) ->
    buttons = [$('#btn-reset'), $('#btn-back'), $('#btn-play'), $('#btn-step'), $('#btn-finish')]
    for i in [0..4]
      if (settings[i] is 0) then buttons[i].attr('disabled', 'disabled')
      else buttons[i].removeAttr('disabled')

  setCursor: (node) ->
    $('#cursor').show().insertBefore('#node_' + node)

  unsetCursor: ->
    $('#cursor').hide()

class Stats
  constructor: (@memory) ->

  incWriteOps: ->
    $('#stats-now')
    .val(parseInt($('#stats-now').val()) + 1)
    .addClass('highlight-write')

  incCompareOps: ->
    $('#stats-noc')
    .val(parseInt($('#stats-noc').val()) + 1)
    .addClass('highlight-write')

  readVar: (vid) ->
    $('#var-' + vid).find('.value').addClass('highlight-compare')

  writeVar: (vid, value) ->
    $('#var-' + vid).find('.value').val(value)
    .removeClass('highlight-compare').addClass('highlight-write')
    @incWriteOps()

  writeArrayVar: (vid, index, value) ->
    $('#var-' + vid).find('.offset_' + index).val(value)
    .removeClass('highlight-compare').addClass('highlight-write')
    @incWriteOps()

  reset: ->
    # reset variables
    $.each(@memory.memory, (index, elem) ->
      $('#var-' + index).find('.value').val(elem.value)
    )
    # reset statistics
    $('#stats-now').val(0)
    $('#stats-noc').val(0)
$ ->
  tree = new Tree()
  stats = new Stats(tree.memory)
  player = new Player(tree, stats)

  $('#btn-reset').click -> player.reset()
  $('#btn-back').click -> player.back()
  $('#btn-play').click -> player.play()
  $('#btn-step').click -> player.step()
  $('#btn-finish').click -> player.finish()