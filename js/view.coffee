class Player
  @curstep

  constructor: (@tree, @memory) ->
    @reset()

  reset: ->
    @curstep = 0
    @setCursor(0)
    @setControls([0, 0, 1, 1, 1])

  stepback: ->
    # check for prev step
    if ($('#node_' + (@curstep - 1)).length)
      @setControls([1, 1, 1, 1, 1])
      @curstep--
      @setCursor(@curstep)
    else
      @setControls([0, 0, 1, 1, 1])

  play: ->
    console.log(@tree)

  step: ->
    # execute current step
    @nextstep = @tree.executeStep(@)
    # prepare the next step
    if ($('#node_' + @nextstep).length)
      @setControls([1, 1, 1, 1, 1])
      @curstep = @nextstep
      @setCursor(@curstep)
    else
      @setControls([1, 1, 0, 0, 0])

  finish: ->
    @setControls([1, 1, 0, 0, 0])

  setControls: (settings) ->
    buttons = [$('#btn-reset'), $('#btn-stepback'), $('#btn-play'), $('#btn-step'), $('#btn-finish')]
    for i in [0..4]
      if (settings[i] is 0) then buttons[i].attr('disabled', 'disabled')
      else buttons[i].removeAttr('disabled')

  setCursor: (node) ->
    $('.cursor').removeClass('cursor')
    $('#node_' + node).addClass('cursor')

class Memory
  constructor: (@table) ->
    @memory = []
    @table.children().each((index, element) =>
      vid = $(element).data('vid')
      value = $(element).find('.value').val()
      @memory[vid] = value
    )

  get: (vid) =>
    @memory[vid]

  set: (vid, value) =>
    @memory[vid] = value
    @table.children('#var-' + vid).find('.value')
    .val(value)# set new value
    .addClass('highlight-write') # mark as edited

$ ->
  tree = new Tree()
  memory = new Memory($('#variables'))
  player = new Player(tree, memory)

  $('#btn-reset').click -> player.reset()
  $('#btn-stepback').click -> player.stepback()
  $('#btn-play').click ->
    player.play()
    $('#img-play').toggleClass('glyphicon-play glyphicon-pause')
  $('#btn-step').click -> player.step()
  $('#btn-finish').click -> player.finish()