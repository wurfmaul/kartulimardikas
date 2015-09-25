window.VARSITE = $("#insertVarsHere") # Specifies the site, where variables are to place.

class Api
  @editInfo: ->
    aid = window.current.aid
    name = $('#in-name').val()
    desc = $('#in-desc').val()
    long = $('#in-long').val()
    $.ajax("api/algorithm.php?area=info",
      type: 'POST'
      data:
        aid: aid
        name: name
        desc: desc
        long: long
        lang: window.current.lang
      dataType: 'json'
      success: (data) => # if response arrived...
        if data['error']?
          @_printError(data['error'])
          if data['name']?
            $('#in-name')
            .val(data['name'])# restore name
            .closest('.form-group').addClass('has-error') # mark as invalid
        else
          @_printSuccess(data['success'])
          $('.has-error').removeClass('has-error')
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)
    )

  @editTags: ->
    aid = window.current.aid
    tags = $('#in-tags').val()
    $.ajax("api/tag.php",
      type: 'POST'
      data:
        aid: aid
        tags: tags
        lang: window.current.lang
      dataType: 'json'
      success: (data) => # if response arrived...
        $('#in-tags').val(data['tags'])
        if data['error']?
          @_printError(data['error'])
          if data['tags']?
            $('#in-tags').closest('.form-group').addClass('has-error') # mark as invalid
        else
          @_printSuccess(data['success'])
          $('.has-error').removeClass('has-error')
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)
    )

  @editVariable: (vid) ->
    varRow = $('#var-' + vid)
    aid = window.current.aid
    name = varRow.find('.name').val()
    type = varRow.find('.type').val()
    value = varRow.find('.value').val()
    size = varRow.find('.size').val()
    $.ajax("api/algorithm.php?area=var&action=edit",
      type: 'POST'
      data:
        aid: aid
        vid: vid
        name: name
        type: type
        value: value
        size: size
        lang: window.current.lang
      dataType: 'json'
      success: (data) => # if response arrived...
        msg = data['error'] ? ""
        varRow = $('#var-' + vid)

        # check for errors
        for token in ['name', 'type', 'value', 'size']
          if data['error-' + token]?
            msg += data['error-' + token]
            varRow.find('.' + token + '-group').addClass('has-error')
          else
            varRow.find('.' + token + '-group').removeClass('has-error')
            varRow.data(token, data[token])

        if msg isnt "" then @_printError(msg)
        else
          @_printSuccess(data['success'])
          # change from edit mode to view mode
          varRow.find('.edit').hide()
          varRow.find('.view .cell').text(data['viewLabel'])
          varRow.find('.view .details').text('(' + data['viewMode'] + ')') if (data['viewMode']?)
          varRow.find('.view').show()
          initVarInput($('.insertStepsHere').find('.combobox'))
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)
    )

  @removeVariable: (vid) ->
    aid = window.current.aid
    $.ajax("api/algorithm.php?area=var&action=remove",
      type: 'POST'
      data:
        aid: aid
        vid: vid
        lang: window.current.lang
      dataType: 'json'
      success: (data) => # if response arrived...
        # print response
        if data['error']? then @_printError(data['error'])
        else
          @_printSuccess(data['success'])
          # hide and remove row
          $('#var-' + vid).hide('slow', -> $(this).remove())
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)
    )

  @editScript: (tree) ->
    aid = window.current.aid
    $.ajax("api/algorithm.php?area=script",
      type: 'POST'
      data:
        aid: aid
        tree: tree
        lang: window.current.lang
      dataType: 'json'
      success: (data) => # if response arrived...
        if data['error']? then @_printError(data['error'])
        else @_printSuccess(data['success'])
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Storage Error: " + errorThrown)
    )

  @parseMarkdown: (source, target) ->
    $.ajax("api/markdown.php",
      type: 'POST'
      data: {source: $(source).val()}
      dataType: 'json'
      success: (data) =>
        if (data['html'] is '')
          $(target).parent().hide('slow')
        else
          $(target).html(data['html']).parent().show('slow')
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)
    )

  @_printError: (msg) ->
    $('#alertText').html(msg)
    $('#alert').show('slow')

  @_printSuccess: (msg) ->
    $('#alert').hide('slow')
    $('#saveSuccess:hidden').text(msg).show('slow', -> $(this).fadeOut(3000))

class VariableForm
  constructor: ->
    lastVid = $('.varRow')# find all variable rows
    .not('#var-prototype')# exclude the prototype
    .last()# pick the last one
    .data('vid') # extract the variable id
    @maxVarId = if lastVid? then lastVid + 1 else 0
    @updateVarCount()

  addRow: ->
    newRow = $('#var-prototype').clone(true)# get prototype
    .attr('id', 'var-' + @maxVarId)# change id
    .data('vid', @maxVarId)# change vid
    .appendTo(VARSITE) # add to the other rows
    newRow.find('.btn-var-count').data('target', 'var-' + @maxVarId) # change highlight target
    newRow.find('.edit').show()
    newRow.find('.view').hide()
    newRow.show('slow')
    initValueInput(newRow.find('.value-group'))
    @maxVarId++

  updateVarCount: ->
    $('.varRow').not('#var-prototype').each(->
      # update the variable counter
      vid = $(this).data('vid')
      tree = new Tree()
      count = tree.memory.get(vid).count
      $(this).find('.counter').text(count)
      # (de)activate remove-button according to counter
      if (count isnt 0)
        $(this).find('.btn-var-remove').attr('disabled', 'disabled')
      else
        $(this).find('.btn-var-remove').removeAttr('disabled')
    )

  performCancel: (vid) ->
    $('#alert').hide('slow')
    varRow = $('#var-' + vid)
    name = varRow.data('name')
    if (name? and name isnt "")
      varRow.find('.name').val(name)
      varRow.find('.init').val(varRow.data('init'))
      varRow.find('.value').val(varRow.data('value'))
      varRow.find('.size').val(varRow.data('size'))
      varRow.find('.edit').hide()
      varRow.find('.view').show()
    else
      varRow.hide('slow', -> $(this).remove())

  performCheck: (vid) ->
    Api.editVariable(vid)

  performEdit: (vid) ->
    varRow = $('#var-' + vid)
    # TODO allow renaming of variables
    initValueInput(varRow.find('.value-group'))
    varRow.find('.edit').show().find('.name').attr('disabled', 'disabled')
    varRow.find('.view').hide()

  performRemove: (vid) ->
    Api.removeVariable(vid)

class StepForm
  constructor: (@varForm) ->

  addNode: (prototypeId) ->
    # create new node from prototype
    node = $('#' + prototypeId)
    .clone(true, true)
    .removeAttr('id')
    .appendTo(SCRIPTSITE)
    # update the variable counter
    @varForm.updateVarCount()
    # remove sortable completely
    $('.sortable').each(->
      if ($(this).sortable("instance")?)
        $(this).sortable("destroy")
    )
    # and reinitialize it
    @updateSortable()
    # make sure, the action handlers (click...) work for the new element
    @updateActionHandlers(node)

  removeNode: (node) ->
    node.hide('slow', =>
      node.remove()
      @varForm.updateVarCount()
      @saveChanges()
    )

  saveChanges: ->
    # parse the tree
    tree = Tree.toJSON()
    # search for invalid-flags
    if (SCRIPTSITE.find('.invalid').length)
      console.error('Not saved due to parsing errors!')
    else
      @varForm.updateVarCount()
      Api.editScript(tree)

  updateActionHandlers: (parent) ->
    # update action handlers
    initVarInput(parent.find('.combobox'))
    initFuncInput(parent.find('.combobox-functions'))
    parent.find('input, textarea').off('blur').blur => @saveChanges() # save when leaving inputs
    parent.find('select').off('change').change => @saveChanges() # save when changing selects
    parent.find('.node-remove').off('click').click (event) =>
      @removeNode($(event.currentTarget).parents('.node:first'))

  updateSortable: ->
    update = =>
      @saveChanges()
    sortParams =
      connectWith: ".sortable"
      placeholder: "sortable-highlight"
      update: update
    dropParams =
      hoverClass: "ui-state-hover",
      over: (event, ui) ->
        console.log('over')
        $(this).find('.show-on-hover').show('slow')
        $(this).find('.hide-on-hover').hide('slow')
      out: (event, ui) ->
        console.log('out')
#        $(this).find('.show-on-hover').hide('slow')
#        $(this).find('.hide-on-hover').show('slow')
      drop: (event,ui) ->
        console.log('drop')
        $(this).find('.show-on-hover, .hide-on-hover').removeClass('show-on-hover hide-on-hover')
    SCRIPTSITE.sortable(sortParams)
    SCRIPTSITE.find('.sortable').sortable(sortParams)
    SCRIPTSITE.find('.droppable').droppable(dropParams)

updateVisibility = (variable) ->
  # show/hide input fields according to the init selection
  option = $(variable).find('option:selected')
  size = option.closest('.varRow').find('.size-group')
  if (option.data('target') is '.size') then size.show('slow')
  else size.hide('slow')

###
  Calls the callback function after a while, if it is not interrupted.
###
typeWatch = (->
  timer = 0
  return (callback, ms) ->
    clearTimeout (timer)
    timer = setTimeout(callback, ms)
)()

refreshPreview = ->
  Api.parseMarkdown($("#in-long"), $('#description-preview'))

$ ->
  # INFORMATION SECTION
  initTagInput($('#in-tags'))
  $('#in-name, #in-desc, #in-long').blur -> Api.editInfo()
  $('#in-tags').blur -> Api.editTags()
  $('#in-long').keyup(-> typeWatch((-> refreshPreview()), 500))
  $('#refresh-preview').click(-> refreshPreview())

  # VARIABLE SECTION
  varForm = new VariableForm()
  $('#btnAddVar').click -> varForm.addRow()
  $('.btn-var-cancel').click -> varForm.performCancel($(this).parents('.varRow').data('vid'))
  $('.btn-var-check').click -> varForm.performCheck($(this).parents('.varRow').data('vid'))
  $('.btn-var-edit').click -> varForm.performEdit($(this).parents('.varRow').data('vid'))
  $('.btn-var-remove').click -> varForm.performRemove($(this).parents('.varRow').data('vid'))
  $('.type').change -> updateVisibility($(this))

  # STEPS SECTION
  stepForm = new StepForm(varForm)
  stepForm.updateActionHandlers(SCRIPTSITE)
  stepForm.updateSortable()
  # handlers for new-node buttons
  $('#node-btn-group a').click ->
    stepForm.addNode($(this).data('node'))
  # handlers for expanding/collapsing comments
  $('.toggle-comment').click(->
    $(this).parent().find('.comment-container').toggle('slow')
    $(this).toggleClass('fa-plus-square fa-minus-square')
  )
  # parse once in order to validate the tree
  Tree.toJSON()