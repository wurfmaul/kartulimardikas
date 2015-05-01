window.VARSITE = $("#insertVarsHere") # Specifies the site, where variables are to place.

class Api
  @editInfo: ->
    aid = $('#aid').data('val')
    name = $('#in-name').val()
    desc = $('#in-desc').val()
    long = $('#in-long').val()
    $.ajax "api/edit-algorithm.php?area=info",
      type: 'POST'
      data: {aid: aid, name: name, desc: desc, long: long}
      dataType: 'json'
      success: (data) => # if response arrived...
        if data['error']? then @_printError(data['error'])
        else @_printSuccess(data['success'])
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)

  @editVariable: (vid) ->
    varRow = $('#var-' + vid)
    aid = $('#aid').data('val')
    name = varRow.find('.name').val()
    init = varRow.find('.init').val()
    value = varRow.find('.value').val()
    size = varRow.find('.size').val()
    $.ajax "api/edit-algorithm.php?area=var&action=edit",
      type: 'POST'
      data: {aid: aid, vid: vid, name: name, init: init, value: value, size: size}
      dataType: 'json'
      success: (data) => # if response arrived...
        msg = data['error'] ? ""
        varRow = $('#var-' + vid)

        # check for errors
        for token in ['name', 'init', 'value', 'size']
          if data['error-' + token]?
            msg += data['error-' + token]
            varRow.find('.' + token + '-group').addClass('has-error')
          else
            varRow.find('.' + token + '-group').removeClass('has-error')
            varRow.find('.' + token).val(data[token])
            varRow.data(token, data[token])

        if msg isnt "" then @_printError(msg)
        else
          @_printSuccess(data['success'])
          # change from edit mode to view mode
          varRow.find('.edit').hide()
          varRow.find('.view .cell').text(data['viewMode'])
          varRow.find('.view').show()
          # update existing var-steps
          opts = $('.var-value > .var-' + vid)
          if (opts.length)
            opts.html(name) # update the name
            $('.var-value-input.var-' + vid).val(name)
          else
            $('.var-value').append(
              $('<option>')# append another option
              .addClass('var-' + vid)# give it a class
              .val(vid)# give it a value
              .html(name) # give it a name
            )
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)

  @removeVariable: (vid) ->
    aid = $('#aid').data('val')
    $.ajax "api/edit-algorithm.php?area=var&action=remove",
      type: 'POST'
      data: {aid: aid, vid: vid}
      dataType: 'json'
      success: (data) => # if response arrived...
        # print response
        if data['error']? then @_printError(data['error'])
        else
          @_printSuccess(data['success'])
          # hide and remove row
          $('#var-' + vid).hide('slow', -> $(this).remove())
          # update existing var-steps
          $('.var-value > .var-' + vid).remove()
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)

  @editScript: (tree) ->
    aid = $('#aid').data('val')
    $.ajax "api/edit-algorithm.php?area=script",
      type: 'POST'
      data: {aid: aid, tree: tree}
      dataType: 'json'
      success: (data) => # if response arrived...
        if data['error']? then @_printError(data['error'])
        else @_printSuccess(data['success'])
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Storage Error: " + errorThrown)

  @_printError: (msg) ->
    $('#editAlertText').html(msg)
    $('#editAlert').show('slow')

  @_printSuccess: (msg) ->
    $('#editAlert').hide('slow')
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
    newRow.find('.edit').show()
    newRow.find('.view').hide()
    newRow.show('slow')
    @maxVarId++

  updateVarCount: ->
    $('.varRow').not('#var-prototype').each(->
      # update the variable counter
      id = $(this).attr('id')
      count = SCRIPTSITE.find(".#{id}:selected").length
      $(this).find('.counter').text(count)
      # (de)activate remove-button according to counter
      if (count isnt 0)
        $(this).find('.btn-var-remove').attr('disabled', 'disabled')
      else
        $(this).find('.btn-var-remove').removeAttr('disabled')
    )

  performCancel: (vid) ->
    $('#editAlert').hide('slow')
    varRow = $('#var-' + vid)
    if varRow.data('name')?
      varRow.find('.name').val(varRow.data('name'))
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
    varRow.find('.edit').show()
    varRow.find('.view').hide()

  performRemove: (vid) ->
    Api.removeVariable(vid)

class StepForm
  constructor: (@varForm) ->

  addNode: (prototypeId) ->
    # create new node from prototype
    node = $('#' + prototypeId)
      .clone(true, true)
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
    # TODO: undo function
    node.hide('slow', =>
      node.remove()
      @varForm.updateVarCount()
      Api.editScript(Tree.toJSON())
    )

  saveChanges: ->
    @varForm.updateVarCount()
    Api.editScript(Tree.toJSON())

  updateActionHandlers: (parent) ->
    # update action handlers
    parent.find('input').off('blur').blur => @saveChanges() # save when leaving inputs
    parent.find('select').off('change').change => @saveChanges() # save when changing selects
    parent.find('.node-remove').off('click').click (event) =>
      @removeNode($(event.currentTarget).parents('.node:first'))

  updateSortable: ->
    # FIXME: find combined solution
    update = () =>
      Api.editScript(Tree.toJSON())
    sortParams =
      connectWith: ".sortable"
      placeholder: "sortable-highlight"
      update: update
    SCRIPTSITE.sortable(sortParams)
    SCRIPTSITE.find('.sortable').sortable(sortParams)

$ ->
  # GENERAL
  $('#editAlertClose').click -> $('#editAlert').hide('slow')

  # INFORMATION SECTION
  $('#in-long')
  .focus -> $(this).val("") if ($(this).data('placeholder') == $(this).val())
  .blur -> $(this).val($(this).data('placeholder')) if ($(this).val() == "")
  $('#in-name, #in-desc, #in-long').blur -> Api.editInfo()

  # VARIABLE SECTION
  varForm = new VariableForm()
  $('#btnAddVar').click -> varForm.addRow()
  $('.btn-var-cancel').click -> varForm.performCancel($(this).parents('.varRow').data('vid'))
  $('.btn-var-check').click -> varForm.performCheck($(this).parents('.varRow').data('vid'))
  $('.btn-var-edit').click -> varForm.performEdit($(this).parents('.varRow').data('vid'))
  $('.btn-var-remove').click -> varForm.performRemove($(this).parents('.varRow').data('vid'))
  $('.btn-var-count').click -> # highlight all the variable usages
    id = $(this).data('target')
    SCRIPTSITE.find(".#{id}:selected").closest('tr').children().addClass('highlight')
    setTimeout(-> # remove the highlight after some time
      SCRIPTSITE.find('.highlight').removeClass('highlight')
    , 1500)
  $('.init').change -> # show/hide input fields according to the init selection
    varRow = $(this).parents('.varRow')
    target = $(this).find(':selected').data('target')
    showValue = (target == '.value')
    showSize = (target == '.size')
    varRow.find('.value-group').show('slow') if showValue
    varRow.find('.size-group').show('slow') if showSize
    varRow.find('.size-group').hide('slow') if !showSize
    varRow.find('.value-group').hide('slow') if !showValue

  # STEPS SECTION
  stepForm = new StepForm(varForm)
  stepForm.updateActionHandlers(SCRIPTSITE)
  stepForm.updateSortable()
  $('#node-btn-group').children('button').click ->
    stepForm.addNode($(this).data('node'))
    Api.editScript(Tree.toJSON())