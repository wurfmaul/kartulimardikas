$ = jQuery
# Specifies the site, where variables are to place.
VARSITE = $("#insertVarsHere")

$ ->
  varForm = new VariableForm()
  $("#btnAddVar").click -> varForm.addRow()
  $('.btn-var-cancel').click -> varForm.performCancel($(this).parents('.varRow').data('vid'))
  $('.btn-var-check').click -> varForm.performCheck($(this).parents('.varRow').data('vid'))
  $('.btn-var-edit').click -> varForm.performEdit($(this).parents('.varRow').data('vid'))
  $('.btn-var-remove').click -> varForm.performRemove($(this).parents('.varRow').data('vid'))
  $('.init').change -> # show/hide input fields according to the init selection
    varRow = $(this).parents('.varRow')
    target = $(this).find(':selected').data('target')
    showValue = (target == '.value')
    showSize = (target == '.size')
    varRow.find('.value-group').show('slow') if showValue
    varRow.find('.size-group').show('slow') if showSize
    varRow.find('.size-group').hide('slow') if !showSize
    varRow.find('.value-group').hide('slow') if !showValue

class VariableForm
  constructor: ->
    lastVid = $('.varRow') # find all variable rows
      .not('#var-prototype') # exclude the prototype
      .last() # pick the last one
      .data('vid') # extract the vid
    @maxVarId = if lastVid? then lastVid + 1 else 0
    @api = new Api()

  addRow: ->
    newRow = $('#var-prototype').clone(true) # get prototype
      .attr('id', 'var-' + @maxVarId) # change id
      .data('vid', @maxVarId) # change vid
      .appendTo(VARSITE) # add to the other rows
    newRow.find('.edit').show()
    newRow.find('.view').hide()
    newRow.show("slow")
    @maxVarId++

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
    varRow = $('#var-' + vid)
    aid = $('#aid').text()
    name = varRow.find('.name').val()
    init = varRow.find('.init').val()
    value = varRow.find('.value').val()
    size = varRow.find('.size').val()
    @api.editVariable(aid, vid, name, init, value, size)

  performEdit: (vid) ->
    varRow = $('#var-' + vid)
    varRow.find('.edit').show()
    varRow.find('.view').hide()

  performRemove: (vid) ->
    aid = $('#aid').text()
    @api.removeVariable(aid, vid)

class Api
  editVariable: (aid, vid, name, init, value, size) ->
    $.ajax "api/edit-algorithm.php?area=var&action=edit",
      type: 'POST'
      data: { aid: aid, vid: vid, name: name, init: init, value: value, size: size }
      dataType: 'json'
      success: (data) -> # if response arrived...
        msg = data['error'] ? ""
        varRow = $('#var-'+vid)

        # check for errors
        for token in ['name', 'init', 'value', 'size']
          if data['error-' + token]?
            msg += data['error-' + token]
            varRow.find('.' + token + '-group').addClass('has-error')
          else
            varRow.find('.' + token + '-group').removeClass('has-error')
            varRow.find('.' + token).val(data[token])
            varRow.data(token, data[token])

        # set aid if new algorithm was created
        if data['aid']? then $('#aid').text(data['aid']);

        if msg isnt "" # if error
          printError(msg)
        else
          printSuccess(data['success'])
          varRow.find('.edit').hide()
          varRow.find('.view .cell').text(data['viewMode'])
          varRow.find('.view').show()
      error: (jqXHR, textStatus, errorThrown) -> # if request failed
        printError("Request Error: " + errorThrown)

  removeVariable: (aid, vid) ->
    $.ajax "api/edit-algorithm.php?area=var&action=remove",
      type: 'POST'
      data: { aid: aid, vid: vid }
      dataType: 'json'
      success: (data) -> # if response arrived...
        if data['error']? # if error
          printError(data['error'])
        else
          printSuccess(data['success'])
          $('#var-' + vid).hide('slow', -> $(this).remove())

      error: (jqXHR, textStatus, errorThrown) -> # if request failed
        printError("Request Error: " + errorThrown)