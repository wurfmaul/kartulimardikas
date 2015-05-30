TIMEOUT = 500 # milliseconds between the steps
MAX_STEPS = 1000 # number of steps an algorithm may take

class Player
  constructor: (@tree, @stats) ->
    @memory = @tree.memory
    @speed = @computeSpeed()
    @reset()

  reset: ->
    # reset components
    @tree.reset()
    @memory.reset()
    @stats.reset()
    # delete return value
    $('#returnValue').val('')
    # find first node to execute
    @curNode = null
    @nextNode = @tree.mark(@, @tree.root)
    # reset highlighting and cursor
    @clearHighlight()
    @setControls([0, 0, 1, 1, 1])
    # hide errors
    $('#viewAlert').hide('slow')

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
      @playStep = 0
      @timer = setInterval(=>
        if (@playStep <= MAX_STEPS)
          @step()
          @playStep++
        else @handleError(new ExecutionError('too_many_steps', [MAX_STEPS]))
      , @speed)
      # set button icon to pause
      $('#img-play')
      .removeClass('glyphicon-play')
      .addClass('glyphicon-pause')

  step: ->
    error = false
    try
      @clearHighlight()
      # execute current step
      @curNode = @nextNode
      curNode = @tree.executeStep(@, @curNode)
    catch runtimeError
      error = runtimeError
      @handleError(error)

    # prepare the next step
    if (error? and error)
      @setControls([1, 1, 0, 0, 0])
      false
    else if (curNode.next? and curNode.next >= 0)
      @nextNode = @tree.mark(@, curNode.next)
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
    @handleError(new ExecutionError('too_many_steps', [MAX_STEPS]))

  changeSpeed: (value) ->
    @speed = value
    # press pause and play again in order to re-initialize timer
    @play()
    @play()
    # store the new speed to the browser's local storage
    localStorage.setItem('speed', value)

  computeSpeed: ->
    if (localStorage? and (speed = localStorage.getItem('speed'))?)
      # first instance: ask local storage about speed
      speed
    else
      # second instance: take default value
      TIMEOUT

  handleError: (error) ->
    # errorCodes is defined by view.phtml
    msg = errorCodes[error.message]
    if (msg?)
      # Insert all the parts into the message
      $.each(error.parts, (index, elem) ->
        msg = msg.replace(new RegExp('%' + (index + 1), 'g'), elem)
      )
    else
      msg = errorCodes['undefined']
      console.error(error)
    $('#viewAlertText').html(msg)
    $('#viewAlert').show('slow')

  clearHighlight: ->
    $('.highlight-write').removeClass('highlight-write')
    $('.highlight-read').removeClass('highlight-read')

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

class Stats
  constructor: (@memory) ->
    @stats = ['accesses', 'assignments', 'comparisons', 'arithmetic']

  inc: (element) ->
    elem = $('#stats-' + element)
    value = parseInt(elem.val()) + 1
    elem.val(value).addClass('highlight-write')

  incAccessOps: -> @inc(@stats[0])

  incAssignOps: -> @inc(@stats[1])

  incCompareOps: -> @inc(@stats[2])

  incArithmeticOps: -> @inc(@stats[3])

  readVar: (vid) ->
    $('#var-' + vid).find('.value-container').addClass('highlight-read')
    @incAccessOps()

  readArrayVar: (vid, index) ->
    $('#var-' + vid).find('.offset_' + index).addClass('highlight-read')
    @incAccessOps()

  writeGeneric: (container, value) ->
    container.removeClass('highlight-read').addClass('highlight-write')
    container.find('.value').text(value)
    @incAssignOps()

  writeVar: (vid, value) ->
    container = $('#var-' + vid).find('.value-container')
    @writeGeneric(container, value)

  writeArrayVar: (vid, index, value) ->
    container = $('#var-' + vid).find('.offset_' + index)
    @writeGeneric(container, value)

  reset: ->
    # reset variables
    $.each(@memory.memory, (index, elem) ->
      row = $('#var-' + index)
      if (elem.array)
        values = elem.value.split(',')
        $.each(values, (i, n) ->
          row.find(".offset_#{i}>.value").text(n)
        )
      else
        row.find('.value').text(elem.value)
    )
    # reset statistics
    $.each(@stats, (index, elem) ->
      $('#stats-' + elem).val(0)
    )
$ ->
  tree = new Tree()
  stats = new Stats(tree.memory)
  player = new Player(tree, stats)

  $('#btn-reset').click -> player.reset()
  $('#btn-play').click -> player.play()
  $('#btn-step').click -> player.step()
  $('#btn-finish').click -> player.finish()

  $('#speed-slider').slider(
    value: parseInt(1000 / player.speed),
    min: 1,
    max: 20,
    change: (event, ui) -> player.changeSpeed(1000 / ui.value)
  )