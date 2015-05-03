TIMEOUT = 500 # milliseconds between the steps
MAXSTEPS = 1000 # number of steps an algorithm may take

class Player

  constructor: (@tree, @memory, @stats) ->
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
    nextNode = @tree.executeStep(@, @curNode)
    # prepare the next step
    if (nextNode?)
      @nextNode = @tree.extract(nextNode).mark(@)
      @setControls([1, 1, 1, 1, 1])
      true
    else
      @play() if @timer?
      @unsetCursor()
      @setControls([1, 1, 0, 0, 0])
      false

  finish: ->
    for i in [0..MAXSTEPS]
      return if !@step()
    throw new Exception("Could not terminate!")

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

class Memory
  constructor: (@table) ->
    @memory = []
    @original = []
    @table.children().each((index, element) =>
      vid = $(element).data('vid')
      value = $(element).find('.value').val()
      @memory[vid] = value
      @original[vid] = value
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

  reset: =>
    @table.children().each((index, element) =>
      vid = $(element).data('vid')
      $(element).find('.value').val(@original[vid])
      @memory[vid] = @original[vid]
    )

class Stats
  incWriteOps: ->
    $('#stats-now')
    .val(parseInt($('#stats-now').val()) + 1)
    .addClass('highlight-write')

  incCompareOps: ->
    $('#stats-noc')
    .val(parseInt($('#stats-noc').val()) + 1)
    .addClass('highlight-compare')

  reset: ->
    $('#stats-now').val(0)
    $('#stats-noc').val(0)
$ ->
  tree = new Tree()
  memory = new Memory($('#variables'))
  stats = new Stats()
  player = new Player(tree, memory, stats)

  $('#btn-reset').click -> player.reset()
  $('#btn-back').click -> player.back()
  $('#btn-play').click -> player.play()
  $('#btn-step').click -> player.step()
  $('#btn-finish').click -> player.finish()