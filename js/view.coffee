class Player
  constructor: (@tree) ->
    @memory = @tree.memory
    @stats = new Stats(@memory)
    @speed = @loadSpeed()
    @breaks = @loadBreaks()
    @reset()

  reset: ->
    # stop if playing
    @play() if @timer?
    # reset components
    @tree.reset()
    @stats.reset()
    # delete return value
    $('#returnValue').val('')
    # find first node to execute
    @curNode = null
    @nextNode = @tree.mark(@, @tree.root)
    @nextCandidate = null
    @cursorState = 0
    # reset highlighting and cursor
    @clearHighlight()
    if (@nextNode >= 0) then @setControls([0, 0, 1, 1, 1])
    else @setControls([0, 0, 0, 0, 0])
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
      maxSteps = window.defaults.maxSteps
      @playStep = 0
      @timer = setInterval(=>
        if (@playStep <= maxSteps)
          @step()
          @playStep++
        else @handleError(new ExecutionError('too_many_steps', [maxSteps]))
      , @speed)
      # set button icon to pause
      $('#img-play')
      .removeClass('glyphicon-play')
      .addClass('glyphicon-pause')

  step: ->
    @clearHighlight()
    # if cursor is BEFORE the statement, perform execution
    if (@cursorState is 0)
      @curNode = @nextNode
      try
        # execute current step
        curNode = @tree.execute(@, @curNode)
        # set potential next node
        if (curNode.next? and curNode.next >= 0) then @nextCandidate = curNode.next
        else @nextCandidate = null
      catch runtimeError
        @handleError(runtimeError)
        @setControls([1, 1, 0, 0, 0])
        return false

      # if algorithm is stopped AFTER statement, update cursor and wait for next step
      if ($('#stop-after').is(':checked'))
        @cursorState = 1
        @setCursor(@curNode, @cursorState)
        return true

    # mark next node if available
    if (@nextCandidate?)
      @cursorState = 0
      @nextNode = @tree.mark(@, @nextCandidate)
      @setControls([1, 1, 1, 1, 1])
      # if algorithm is stopped before next execution, return true. Execute otherwise.
      if ($('#stop-before').is(':checked')) then true
      else @step()
    else
      @play() if @timer?
      @unsetCursor()
      @setControls([1, 1, 0, 0, 0])
      false


  finish: ->
    maxSteps = window.defaults.maxSteps
    for i in [0..maxSteps]
      return if !@step()
    @handleError(new ExecutionError('too_many_steps', [maxSteps]))

  changeSpeed: (value) ->
    @speed = value
    # press pause and play again in order to re-initialize timer
    @play()
    @play()
    # store the new speed to the browser's local storage
    if (localStorage?) then localStorage.setItem('speed', value)

  loadSpeed: ->
    if (localStorage? and (speed = localStorage.getItem('speed'))?)
      # first priority: ask local storage about speed
      speed
    else
      # second priority: take default value
      window.defaults.speed

  changeBreaks: (status) ->
    # store value to browser's local storage
    if (localStorage?) then localStorage.setItem('breaks', status)

  loadBreaks: ->
    if (localStorage? and (localBreaks = localStorage.getItem('breaks'))?)
      # first priority: ask local storage about speed
      breaks = localBreaks
    else
      # second priority: take default value
      breaks = window.defaults.breaks
    # store settings
    @changeBreaks(breaks)
    # change the ui
    if (breaks is 'before' or breaks is 'both')
      $('#stop-before').prop('checked', true)
      $('#stop-before-btn').addClass('active')
    if (breaks is 'after' or breaks is 'both')
      $('#stop-after').prop('checked', true)
      $('#stop-after-btn').addClass('active')
    # return status
    breaks

  handleError: (error) ->
    # errorCodes is defined by view.phtml
    msg = errorCodes[error.message]
    if (msg?)
      # Insert all the parameters into the message
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

  ###
    node = node_id of the node the cursor should be attached to
    position = the position of the cursor: 0 => before statement, 1 => after statement
  ###
  setCursor: (node, position) ->
    switch (position)
      when 0 then newClass = 'cursor-up'
      when 1 then newClass = 'cursor-down'
    @unsetCursor()
    # set cursor in algorithm
    $('#node_' + node).addClass('cursor ' + newClass)
    # set cursor in source code
    $('#source-node-' + node).closest('.line').addClass('source-cursor ' + newClass)

  unsetCursor: ->
    # remove cursor from algorithm
    $('.cursor').removeClass('cursor cursor-up cursor-down')
    # remove cursor from source code
    $('.source-cursor').removeClass('source-cursor cursor-up cursor-down')

class Stats
  constructor: (@memory) ->
    @stats = ['accesses', 'assignments', 'comparisons', 'arithmeticLogic']

  inc: (element) ->
    elem = $('#stats-' + element)
    value = parseInt(elem.val()) + 1
    elem.val(value).addClass('highlight-write')

  incAccessOps: -> @inc(@stats[0])

  incAssignOps: -> @inc(@stats[1])

  incCompareOps: -> @inc(@stats[2])

  incArithmeticLogicOps: -> @inc(@stats[3])

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

###
  Deal with the breakpoint buttons and compute a valid state ('none' forbidden)
###
toggleBreakpoints = (button, player) ->
  # compute status before clicking
  statusBefore = $('#stop-before-btn').hasClass('active')
  statusAfter = $('#stop-after-btn').hasClass('active')
  # compute status after clicking
  if (button.data('break') is 'before') then statusBefore = !statusBefore
  if (button.data('break') is 'after') then statusAfter = !statusAfter

  if (statusBefore and statusAfter)
    player.changeBreaks('both')
  else if (statusBefore)
    player.changeBreaks('before')
  else if (statusAfter)
    player.changeBreaks('after')
  else
    button.button('toggle') # toggle button back to state before clicking

toggleComment = (element) ->
  container = element.parent()
  # toggle collapse/expand icon
  element.toggleClass('fa-plus-square fa-minus-square')

  # animate collapsing
  if (container.hasClass('collapsed'))
    # dirty hack as animating to 'auto' does not work!
    curHeight = container.height()
    container.css('height', 'auto')
    autoHeight = container.height()
    container.css('height', curHeight)
    newHeight = autoHeight
  else
    newHeight = '20px'
  container.animate({height: newHeight}, 'slow', 'linear', ->
    container.toggleClass('collapsed')
  )

$ ->
  tree = new Tree()
  player = new Player(tree)

  # CONTROLS SECTION
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
  $('#stop-before-btn, #stop-after-btn').click -> toggleBreakpoints($(this), player)

  # ALGORITHM SECTION
  $('.toggle-comment').click -> toggleComment($(this))

  # MEMORY SECTION
  $('.value-container').click(->
    value = $(this).hide().find('.value').text()
    if (offset = $(this).data('offset'))? # list
      input = $(this).siblings('.value-edit.offset_' + offset)
    else # value
      input = $(this).siblings('.value-edit')
    input.val(value).show().focus()
  )
  $('.value-edit').keyup((event) ->
    switch (event.which)
      when 13 # enter key
        newVal = $(this).val()
        if (/^[0-9]+$/.test(newVal))
          if (offset = $(this).data('offset'))? # list
            vid = $(this).closest('.variable').data('vid')
            index = $(this).data('offset')
            player.memory.arraySet(vid, index, newVal)
            player.stats.writeArrayVar(vid, index, newVal)
          else # single value
            vid = $(this).closest('.variable').data('vid')
            player.memory.set(vid, newVal)
            player.stats.writeVar(vid, newVal)
        else
          alert(window.l10n['invalid_value'].replace('%1', newVal))
        $(this).blur()
      when 27 # esc key
        $(this).blur()
  ).blur(->
    $(this).hide().siblings('.value-container').show()
  )