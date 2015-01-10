VARSITE = $("#insertVarsHere") # Specifies the site, where variables are to place.
SCRIPTSITE = $("#insertStepsHere") # Specifies the site, where variables are to place.

class Api
  editInfo: ->
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

  editVariable: (vid) ->
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
          if (opts.length) then opts.html(name) # update the name
          else $('.var-value').append(
            $('<option></option>') # append another option
            .addClass('var-' + vid) # give it a class
            .val('var-' + vid) # give it a value
            .html(name) # give it a name
          )
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)

  removeVariable: (vid) ->
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

  editScript: (tree) ->
    aid = $('#aid').data('val')
    html = SCRIPTSITE.html()
    $.ajax "api/edit-algorithm.php?area=script",
      type: 'POST'
      data: {aid: aid, tree: tree, html: html}
      dataType: 'json'
      success: (data) => # if response arrived...
        if data['error']? then @_printError(data['error'])
        else @_printSuccess(data['success'])
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Request Error: " + errorThrown)

  _printError: (msg) ->
    $('#editAlertText').html(msg)
    $('#editAlert').show('slow')

  _printSuccess: (msg) ->
    $('#editAlert').hide('slow')
    $('#saveSuccess').text(msg).show('slow', -> $(this).fadeOut(3000))

class Tree
  parse: (node) =>
    # extract the type
    type = node.data('node-type')

    # call proper parsing method
    switch type
      when 'assign' then response = @parseAssign(node)
      when 'compare' then response = @parseCompare(node)
      when 'constant' then response = @parseConstant(node)
      when 'if' then response = @parseIf(node)
      when 'var' then response = @parseVar(node)
      when 'while' then response = @parseWhile(node)
#      else
#        console.error "Parse error: Unknown type: '#{type}'"
    response

  parseRoot: ->
    # if the main body is empty, return empty string, parse tree otherwise
    if ($(SCRIPTSITE).filter(':empty').length) then ""
    else @parseBody(SCRIPTSITE)

  parseBody: (node) =>
    # prepare return value
    script = {}
    # loop level-1 elements:
    node.children('li').each((index, element) =>
      script[index] = @parse($(element))
    )
    script

  parseAssign: (node) =>
    from = @parseBody(@findSubNode(node, '.assign-from'))
    to = @parseBody(@findSubNode(node, '.assign-to'))

    {
      node: 'assign'
      from: from
      to: to
    }

  parseCompare: (node) =>
    left = @parseBody(@findSubNode(node, '.compare-left'))
    right = @parseBody(@findSubNode(node, '.compare-right'))
    operator = node.find('.compare-operation:first').val()

    {
      node: 'compare'
      left: left
      right: right
      operator: operator
    }

  parseConstant: (node) =>
    value = node.find('.constant-value:first').val()

    {
      node: 'constant'
      value: value
    }

  parseIf: (node) =>
    condition = @parseBody(@findSubNode(node, '.if-condition'))
    body = @parseBody(@findSubNode(node, '.if-body'))

    {
      node: 'if'
      condition: condition
      body: body
    }

  parseVar: (node) =>
    variable = node.find('.var-value:first').val()

    {
      node: 'var'
      var: variable
    }

  parseWhile: (node) ->
    condition = @parseBody(@findSubNode(node, '.while-condition'))
    body = @parseBody(@findSubNode(node, '.while-body'))

    {
    node: 'while'
    condition: condition
    body: body
    }

  findSubNode: (node, _class) ->
    node.find(_class + ':first')

class VariableForm
  constructor: (api) ->
    lastVid = $('.varRow')# find all variable rows
    .not('#var-prototype')# exclude the prototype
    .last()# pick the last one
    .data('vid') # extract the variable id
    @maxVarId = if lastVid? then lastVid + 1 else 0
    @api = api

  addRow: ->
    newRow = $('#var-prototype').clone(true)# get prototype
      .attr('id', 'var-' + @maxVarId)# change id
      .data('vid', @maxVarId)# change vid
      .appendTo(VARSITE) # add to the other rows
    newRow.find('.edit').show()
    newRow.find('.view').hide()
    newRow.show('slow')
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
    @api.editVariable(vid)

  performEdit: (vid) ->
    varRow = $('#var-' + vid)
    varRow.find('.edit').show()
    varRow.find('.view').hide()

  performRemove: (vid) ->
    @api.removeVariable(vid)

class StepForm
  constructor: (api, tree) ->
    @api = api
    @tree = tree

  addNode: (nodeId) ->
    # create new node from prototype
    node = $('#' + nodeId)
      .clone(true, true)
      .removeAttr('id')
      .appendTo(SCRIPTSITE)

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
      @api.editScript(@tree.parseRoot())
    )

  updateActionHandlers: (parent) ->
    # update action handlers
    parent.find('input, select').off('blur').blur =>
      @api.editScript(@tree.parseRoot())
    parent.find('.node-remove').off('click').click (event) =>
      @removeNode($(event.currentTarget).parents('.node:first'))

  updateSortable: ->
    # FIXME: find combined solution
    update = () =>
      @api.editScript(@tree.parseRoot())
    sortParams =
      connectWith: ".sortable"
      placeholder: "sortable-highlight"
      update: update
    SCRIPTSITE.sortable(sortParams)
    SCRIPTSITE.find('.sortable').sortable(sortParams)

$ ->
  # GENERAL
  api = new Api()
  $('#editAlertClose').click -> $('#editAlert').hide('slow')

  # INFORMATION SECTION
  $('#in-long')
  .focus -> $(this).val("") if ($(this).data('placeholder') == $(this).val())
  .blur -> $(this).val($(this).data('placeholder')) if ($(this).val() == "")
  $('#in-name, #in-desc, #in-long').blur -> api.editInfo()

  # VARIABLE SECTION
  varForm = new VariableForm(api)
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

  # STEPS SECTION
  tree = new Tree()
  stepForm = new StepForm(api, tree)
  stepForm.updateActionHandlers(SCRIPTSITE)
  stepForm.updateSortable()
  $('#node-btn-group').children('button').click ->
    stepForm.addNode($(this).data('node'))
    api.editScript(tree.parseRoot())
