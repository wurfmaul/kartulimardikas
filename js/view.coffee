class Player
  constructor: ->
    @curstep = 0

  reset: ->
    console.log('reset')
    @setControls([0, 0, 1, 1, 1])

  stepback: ->
    console.log('step back')

    # check for prev step
    if ($('#step_' + (@curstep - 1)).length)
      @setControls([1, 1, 1, 1, 1])
      @curstep--
    else
      @setControls([0, 0, 1, 1, 1])

  play: ->

  step: ->
    console.log('step ' + @curstep)
    $('.highlight').removeClass('highlight')
    $('#step_' + @curstep).addClass('highlight')


    # check for next step
    if ($('#step_' + (@curstep + 1)).length)
      @setControls([1, 1, 1, 1, 1])
      @curstep++
    else
      @setControls([1, 1, 0, 0, 0])

  finish: ->
    console.log('finish')
    @setControls([1, 1, 0, 0, 0])

  setControls: (settings) ->
    buttons = [$('#btn-reset'), $('#btn-stepback'), $('#btn-play'), $('#btn-step'), $('#btn-finish')]
    for i in [0..4]
      if (settings[i] is 0) then buttons[i].attr('disabled', 'disabled')
      else buttons[i].removeAttr('disabled')

$ ->
  player = new Player()
  player.setControls([0, 0, 1, 1, 1]) # reset controls
  tree = new Tree()
  $('.node:first').addClass('highlight') # set cursor to first node

  $('#btn-reset').click -> player.reset()
  $('#btn-stepback').click -> player.stepback()
  $('#btn-play').click ->
    player.play()
    $('#img-play').toggleClass('glyphicon-play glyphicon-pause')
  $('#btn-step').click -> player.step()
  $('#btn-finish').click -> player.finish()