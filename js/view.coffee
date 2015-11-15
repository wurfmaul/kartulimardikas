class Player
  constructor: (@tree, @scope) ->
    @curScope = $('#scope-' + @scope)
    @memory = @tree.memory
    @stats = new Stats(@memory, @scope)
    @speed = @loadSpeed()
    @breaks = @loadBreaks()
    @tempo = 0
    @reset()

  reset: ->
    # stop if playing
    @play() if @timer?
    # reset components
    @tree.reset()
    @stats.reset()
    # reset scopes
    $('#scopes-head').children(':gt(' + @scope + ')').remove()
    $('#scopes-body').children(':gt(' + @scope + ')').remove()
    # clear return value
    @curScope.find('.return-value').val('')
    # find first node to execute
    @curNode = null
    @nextNode = @tree.mark(@, @tree.root)
    @nextCandidate = null
    @cursorState = 0
    # reset highlighting and cursor
    @clearHighlight()
    if (@nextNode >= 0) then @setControls([0, 1, 1, 1])
    else @setControls([0, 0, 0, 0])
    # hide errors
    $('#alert').hide('slow')

  play: ->
    if @timer? # if currently playing => pause
      # clear timer
      @timer = clearInterval(@timer);
      # set button icon to play
      @curScope.find('.img-play').removeClass('glyphicon-pause').addClass('glyphicon-play')
      @tempo = 0
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
      @curScope.find('.img-play').removeClass('glyphicon-play').addClass('glyphicon-pause')
      @tempo = 1

  step: ->
    @clearHighlight()
    # if cursor is BEFORE the statement, perform execution
    if (@cursorState is 0)
      @curNode = @nextNode
      try
        # execute current step
        curNode = @tree.execute(@, @curNode)
        # set potential next node
        if (curNode.scope?)
          # signal for a function call
          @curNode = curNode.node
          @callFunction(curNode.scope, curNode.params)
          return

        if (curNode.next? and curNode.next >= 0) then @nextCandidate = curNode.next
        else @nextCandidate = null
      catch runtimeError
        @handleError(runtimeError)
        @setControls([1, 0, 0, 0])
        return false

      # if algorithm is stopped AFTER statement, update cursor and wait for next step
      if (@curScope.find('.stop-after').is(':checked'))
        @cursorState = 1
        @setCursor(@curNode, @cursorState)
        return true

    # mark next node if available
    if (@nextCandidate?)
      @cursorState = 0
      @nextNode = @tree.mark(@, @nextCandidate)
      @setControls([1, 1, 1, 1])
      # if algorithm is stopped before next execution, return true. Execute otherwise.
      if (@curScope.find('.stop-before').is(':checked')) then true
      else @step()
    else
      @play() if @timer?
      @unsetCursor()
      @setControls([1, 0, 0, 0])
      # check for outer scopes
      if (@scope > 0)
        value = @curScope.find('.return-value').val()
        window.players[@scope-1].returnFunction(@scope, value)
      false

  finish: ->
    @tempo = 2
    maxSteps = window.defaults.maxSteps
    for i in [0..maxSteps]
      return if !@step()
    @handleError(new ExecutionError('too_many_steps', [maxSteps]))

  callFunction: (scope, params) ->
    # deactivate navigation in outer scope
    @setControls([0,0,0,0])
    # switch to inner scope
    if @timer?
      @play() # pause
      @tempo = 1
    init(scope, @tempo)
    # load the parameters
    player = players[scope]
    $('#scope-' + scope).find('.variables .parameter').each(->
      vid = $(this).data('vid')
      value = params.shift().value
      player.memory.set(vid, value)
      player.stats.writeVar(vid, value)
    )
    # show inner scope
    $('#scopes-head .scope-' + scope).tab('show')

  returnFunction: (scope, value) ->
    # switch back to outer scope
    $('#scopes-head .scope-' + @scope).tab('show')
    # remove other scope
    $('#scopes-head .scope-' + scope).parent().remove()
    $('#scope-' + scope).remove()
    # use returned value
    @curScope.find('.node_' + @curNode).data('return-value', value)
    # reactivate navigation in outer scope
    @setControls([1,1,1,1])
    switch @tempo
      when 2 then @finish()
      when 1 then @play()
      else @step()

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
      @curScope.find('.stop-before').prop('checked', true)
      @curScope.find('.stop-before-btn').addClass('active')
    if (breaks is 'after' or breaks is 'both')
      @curScope.find('.stop-after').prop('checked', true)
      @curScope.find('.stop-after-btn').addClass('active')
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
    $('#alertText').html(msg)
    $('#alert').show('slow')

  clearHighlight: ->
    @curScope.find('.highlight-write').removeClass('highlight-write')
    @curScope.find('.highlight-read').removeClass('highlight-read')

  setControls: (settings) ->
    buttons = @curScope.find('.controls button')
    for i in [0..buttons.length]
      if (settings[i] is 0) then $(buttons[i]).attr('disabled', 'disabled')
      else $(buttons[i]).removeAttr('disabled')

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
    @curScope.find('.node_' + node).addClass('cursor ' + newClass)
    # set cursor in source code
    @curScope.find('.source-node-' + node).closest('.line').addClass('source-cursor ' + newClass)

  unsetCursor: ->
    # remove cursor from algorithm
    @curScope.find('.cursor').removeClass('cursor cursor-up cursor-down')
    # remove cursor from source code
    @curScope.find('.source-cursor').removeClass('source-cursor cursor-up cursor-down')

class Stats
  constructor: (@memory, @scope) ->
    @curScope = $('#scope-' + @scope)
    @stats = ['accesses', 'assignments', 'comparisons', 'arithmeticLogic']

  inc: (element) ->
    elem = @curScope.find('.stats-' + element)
    value = parseInt(elem.val()) + 1
    elem.val(value).addClass('highlight-write')

  incAccessOps: -> @inc(@stats[0])

  incAssignOps: -> @inc(@stats[1])

  incCompareOps: -> @inc(@stats[2])

  incArithmeticLogicOps: -> @inc(@stats[3])

  readVar: (vid) ->
    @curScope.find('.var-' + vid + ' .value-container').addClass('highlight-read')
    @incAccessOps()

  readArrayVar: (vid, index) ->
    @curScope.find('.var-' + vid + ' .offset_' + index).addClass('highlight-read')
    @incAccessOps()

  writeGeneric: (container, value) ->
    container.removeClass('highlight-read').addClass('highlight-write')
    container.find('.value').text(value)
    @incAssignOps()

  writeVar: (vid, value) ->
    container = @curScope.find('.var-' + vid + ' .value-container')
    @writeGeneric(container, value)

  writeArrayVar: (vid, index, value) ->
    container = @curScope.find('.var-' + vid + ' .offset_' + index)
    @writeGeneric(container, value)

  reset: ->
    # reset variables
    $.each(@memory.memory, (index, elem) =>
      row = @curScope.find('.var-' + index)
      if (elem.array)
        values = elem.value.split(',')
        $.each(values, (i, n) ->
          row.find(".offset_#{i}>.value").text(n)
        )
      else
        row.find('.value').text(elem.value)
    )
    # reset statistics
    $.each(@stats, (index, elem) =>
      @curScope.find('.stats-' + elem).val(0)
    )

###
  Deal with the breakpoint buttons and compute a valid state ('none' forbidden)
###
toggleBreakpoints = (button, player) ->
  curScope = $('#scope-' + player.scope)
  # compute status before clicking
  statusBefore = curScope.find('.stop-before-btn').hasClass('active')
  statusAfter = curScope.find('.stop-after-btn').hasClass('active')
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

init = (scope, tempo) ->
  # INITIALIZE SCOPE
  curScope = $('#scope-' + scope)
  tree = new Tree(scope)
  player = new Player(tree, scope)
  window.players[scope] = player # add scope to list of scopes

  # CONTROLS SECTION
  curScope.find('.btn-reset').click -> player.reset()
  curScope.find('.btn-play').click -> player.play()
  curScope.find('.btn-step').click -> player.step()
  curScope.find('.btn-finish').click -> player.finish()
  curScope.find('.speed-slider').slider(
    value: parseInt(1000 / player.speed),
    min: 1,
    max: 20,
    change: (event, ui) -> player.changeSpeed(1000 / ui.value)
  )
  curScope.find('.stop-before-btn, .stop-after-btn').click -> toggleBreakpoints($(this), player)

  # ALGORITHM SECTION
  curScope.find('.toggle-comment').click -> toggleComment($(this))

  # MEMORY SECTION
  curScope.find('.value-container').click(->
    value = $(this).hide().find('.value').text()
    if (offset = $(this).data('offset'))? # list
      input = $(this).siblings('.value-edit.offset_' + offset)
    else # value
      input = $(this).siblings('.value-edit')
    input.val(value).show().focus()
  )
  curScope.find('.value-edit').keyup((event) ->
    switch (event.which)
      when 13 # enter key
        if (newVal = DataType.parse($(this).val()))
          if (offset = $(this).data('offset'))? # list
            vid = $(this).closest('.variable').data('vid')
            index = $(this).data('offset')
            player.memory.arraySet(vid, index, newVal.value)
            player.stats.writeArrayVar(vid, index, newVal.value)
          else # single value
            vid = $(this).closest('.variable').data('vid')
            player.memory.set(vid, newVal.value)
            player.stats.writeVar(vid, newVal.value)
        else
          alert(window.l10n['invalid_value'].replace('%1', $(this).val()))
        $(this).blur()
      when 27 # esc key
        $(this).blur()
  ).blur(->
    $(this).hide().siblings('.value-container').show()
  )

  # continue execution
  switch tempo
    when 1 then player.play()
    when 2 then player.finish()

$ ->
  # prepare list of scopes
  window.players = {}
  # initialize outer scope
  init(0, 0)