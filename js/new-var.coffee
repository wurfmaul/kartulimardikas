###
This file contains every functionality concerning variable generation and management.
###

$ = jQuery
# Represents delimiter of list elements in customized view.
DELIM = ','
# Specifies the site, where variables are to place.
VARSITE = $("#placeVariablesHere")

###
This class is the central collection of all available variables. It is simply an implementation of a collection of key-value pairs.
###
class Variables
  # Initialization method number 1: no initialization.
  @UNINITIALIZED: 0
  # Initialization method number 2: random initialization.
  @RANDOMIZED: 1
  # Initialization method number 3: custom initialization.
  @CUSTOMIZED: 2

  constructor: ->
    # The basic array to store all the values.
    @vars = new Array()
    @maxId = 0

  add: (id, name, value, init) ->
    @vars.push({id: id, name: name, value: value, init: init})

  edit: (id, newName, newValue, init) ->
    @vars[@findId(id)] = {id: id, name: newName, value: newValue, init: init}

  findName: (name) ->
    for elem, i in @vars
      if elem.name is name
        return i
    -1

  findId: (id) ->
    for elem, i in @vars
      if elem.id is id
        return i
    -1

  get: (i) -> @vars[i]

  getById: (id) ->
    for elem in @vars
      if elem.id is id
        return elem

  getByName: (name) -> @vars[@find(name)]

  isArray: (i) -> $.isArray(@vars[i].value)

  isArrayByName: (name) -> $.isArray(@getByName(name).value)

  isArrayById: (id) -> $.isArray(@getById(id).value)

  removeByName: (name) -> @vars.splice(@find(name), 1)

  removeById: (id) -> @vars.splice(@findId(id), 1)

  size: -> @vars.length

  toString: (name) ->
    value = @getByName(name).value
    if $.isArray value
      ret = "";
      for i in [0..value.length]
        ret += value[i]
        ret += "; " if i < value.length - 1
      ret
    else
      value

class ElementFactory
  constructor: (@vars) ->
    @valid = new window.Validator(@vars)

    # The default value of a register's name.
  @DEFAULTNAME = ""
  # The default method for a register's initialization.
  @DEFAULTINIT = Variables.UNINITIALIZED
  # The default value for a register's value.
  @DEFAULTVALUE = ""

  create: (@id, @init) ->
    # check input fields
    if (@check(null))
      # add register to internal structure
      @vars.add(@id, @name, @value, @init)
      return true
    false

  edit: (@id, @init) ->
    oldName = @vars.getById(id).name
    # check input fields
    if (@check(oldName))
      # add register to internal structure
      @vars.edit(@id, @name, @value, @init)
      return true
    false

  check: (oldName) ->
    @valid.target("#var-#{@id}-nameField", "#alert-var")
    @valid.reset()
    # get name from input field
    @name = $("#var-#{@id}-name").val()
    check = @valid.checkName(@name, oldName)
    # retrieve value
    if @init is Variables.CUSTOMIZED
      @value = $("#var-#{@id}-value").val()
      @valid.target("#var-#{@id}-valueField", "#alert-var")
      check = @valid.checkValue(@value) && check
    else
      @value = "?"
    check

class ArrayFactory
  # The default value of a list's name.
  @DEFAULTNAME = ""
  # The default size of a list.
  @DEFAULTSIZE = 7
  # The default value of a register's initialization method.
  @DEFAULTINIT = Variables.UNINITIALIZED
  # The default value of a list's values.
  @DEFAULTVALUES = ""

  constructor: (@vars) ->
    @valid = new window.Validator(@vars)

    # Add the list that was specified by the HTML add form.
  create: (@id, @init) ->
    # check input fields
    if (@check(null))
      # add register to internal structure
      @vars.add(@id, @name, @values, @init)
      return true
    false

  ###
  Edit the list that was specified by the HTML add form.
  @param oldName
            The unique name of the list that is to be edited.
  ###
  edit: (@id, @init) ->
    oldName = @vars.getById(id).name
    # check input fields
    if (@check(oldName))
      # add register to internal structure
      @vars.edit(@id, @name, @values, @init)
      return true
    false

  ###
  Check the data that was provided by the HTML add/edit form by the validator.
  @param oldName The unique name of the list that is to be checked.
  ###
  check: (oldName) ->
    # tell validator that we are dealing with lists
    @valid.target("#var-#{@id}-nameField", "#alert-var")

    # clear errors
    $(".has-error").removeClass("has-error")
    $(".alert").alert('close')

    # get name from input field
    @name = $("#var-#{@id}-name").val()
    check = @valid.checkName(@name, oldName)

    # retrieve values
    @values = new Array()
    switch @init
      when Variables.UNINITIALIZED
        @size = $("#var-#{@id}-size").val()
        @values.push("?") for [0..@size-1]

      when Variables.RANDOMIZED
        @size = $("#var-#{@id}-size").val();
        @values = shuffle([0..@size-1])

      when Variables.CUSTOMIZED
        values = $("#var-#{@id}-value").val()
        @valid.target "#var-#{@id}-valueField", "#alert-var"
        if @valid.checkValues(values)
          tokens = values.split(DELIM)
          @size = tokens.length
          for i in [0..@size-1]
            # trim
            value = tokens[i].replace /\s/, ""
            # check value
            if @valid.checkValue(value)
              @values[i] = value
            else
              @values[i] = "?"
              check = false
        else
          check = false

      else
        $("#alert-var").append(window.Templates.error("List has to be initialized!"))
    check

class VariableForm
  constructor: ->
    @maxVarId = 0
    @vars = new Variables()
    @template = new window.Templates(@vars)
    @elementFactory = new ElementFactory(@vars)
    @arrayFactory = new ArrayFactory(@vars)

  addRowBelow: ->
    selection = @getSelection()
    if selection
      # insert row right after selection
      @addRow(selection.last())
      @moveSelectionDown("select")
    else
      # insert row after the last one
      vid = @addRow($(".varRow").last())
      @select("#var-" + vid)

  addRow: (prevRow) ->
    if prevRow.length > 0
      prevRow.after(@template.varRowEdit(@maxVarId))
    else
      # if it is the first line
      $("#insertVarsHere").append(@template.varRowEdit(@maxVarId))

    $("#var-" + @maxVarId).show("slow")
    @updateActionHandlers(@maxVarId)
    @updatePlaceholders()
    @maxVarId++

  checkAndCreateVar: (vid) ->
    success = false
    switch $("#slct-var-#{vid}-init").val()
      when "elem-?" then success = @elementFactory.create(vid, Variables.UNINITIALIZED)
      when "elem-value" then success = @elementFactory.create(vid, Variables.CUSTOMIZED)
      when "array-?" then success = @arrayFactory.create(vid, Variables.UNINITIALIZED)
      when "array-random" then success = @arrayFactory.create(vid, Variables.RANDOMIZED)
      when "array-custom" then success = @arrayFactory.create(vid, Variables.CUSTOMIZED)
      else console.log "can't recognize value..."
    success

  checkAndEditVar: (vid) ->
    success = false
    switch ($("#slct-var-#{vid}-init").val())
      when "elem-?" then success = @elementFactory.edit(vid, Variables.UNINITIALIZED)
      when "elem-value" then success = @elementFactory.edit(vid, Variables.CUSTOMIZED)
      when "array-?" then success = @arrayFactory.edit(vid, Variables.UNINITIALIZED)
      when "array-random" then success = @arrayFactory.edit(vid, Variables.RANDOMIZED)
      when "array-custom" then success = @arrayFactory.edit(vid, Variables.CUSTOMIZED)
      else console.log "can't recognize value..."
    success

  getSelection: ->
    selection = $("#insertVarsHere tr.ui-selected")
    if selection.length > 0
      selection
    else
      false

  clearSelection: ->
    $("#insertVarsHere .ui-selected").removeClass("ui-selected")

  # takes the jquery-id (incl. #) of a row ("#var-" + vid) and selects it
  select: (id) ->
    $(id).addClass("ui-selected")

  moveSelectionDown: (mode) ->
    selection = @getSelection()
    if selection?
      # if some line is selected
      # get next line, step over placeholder
      nextLine = selection.last().next().next() # next(".varRow")
      id = nextLine.prop("id")
      if id
        # if there is a line below
        switch mode
          when "move"
            nextLine.detach()
            selection.first().before(nextLine)
          when "select"
            @clearSelection()
            @select("#" + id)
          when "extend"
            @select("#" + id)
          else console.log "unknown mode: #{mode}"
        @updatePlaceholders()


  moveSelectionUp: (mode) ->
    selection = @getSelection()
    if selection?
      # if some line is selected
      # get previous line, step over placeholder
      prevLine = selection.first().prev().prev() # prev(".varRow")
      id = prevLine.prop("id")
      if id
        # if there is a line above
        switch mode
          when "move"
            prevLine.detach
            selection.last().after(prevLine)
          when "select"
            @clearSelection()
            @select("#" + id)
          when "extend"
            @select("#" + id)
          else console.log "unknown mode: #{mode}"
        @updatePlaceholders()


  performActionOnSelection: (mode) =>
    selection = @getSelection()
    if selection?
      @clearSelection()
      selection.each (index, element) =>
        vid = $(element).prop("id").split("-")[1]
        switch mode
          when "remove" then @performRemove(vid)
          when "cancel" then @performCancel(vid)
          when "check" then @performCheck(vid)
          else console.log "unknown mode: #{mode}"

  performCancel: (vid) ->
    if @vars.findId(vid) is -1
      # variable does not exist yet -> reset form
      #$("#var-" + vid).replaceWith(@template.varRowEdit(vid))
      #$("#var-" + vid).show()
      @performRemove(vid)
    else
      # variable exists -> discard changes
      $("#var-" + vid).replaceWith(@template.varRowShow(vid))

    @updateActionHandlers(vid)
    @select("#var-" + vid)

  performCheck: (vid) ->
    check = false
    if @vars.findId(vid) isnt -1
      # var already exists
      check = @checkAndEditVar(vid)
    else
      check = @checkAndCreateVar(vid)

    if (check)
      $("#var-" + vid).replaceWith(@template.varRowShow(vid))
      @updateActionHandlers(vid)
      @select("#var-" + vid)

  performEdit: (vid) ->
    $("#var-" + vid).replaceWith(@template.varRowEdit(vid))
    $("#var-" + vid).show()
    @updateActionHandlers(vid)
    @select("#var-" + vid)

  performRemove: (vid) ->
    @vars.removeById(vid) # FIXME call factory for deletion
    try
      nextVid = $("#var-" + vid).next().next().prop("id")
      @select("#" + nextVid)
    catch e
    #	$("#var-" + vid).hide("slow"); FIXME animation on deletion
    $("#var-" + vid).remove()
    @updatePlaceholders()

  updateActionHandlers: (vid) ->
    curRemoveButton = $("#btn-var-#{vid}-remove")
    curEditButton = $("#btn-var-#{vid}-edit")
    curCheckButton = $("#btn-var-#{vid}-check")
    curCancelButton = $("#btn-var-#{vid}-cancel")
    curValueSelect = $("#slct-var-#{vid}-init")

    curValueField = $("#var-#{vid}-valueField")
    curSizeField = $("#var-#{vid}-sizeField")

    # deactivate old action handlers
    curRemoveButton.off("click")
    curEditButton.off("click")
    curCheckButton.off("click")
    curCancelButton.off("click")
    curValueSelect.off("click")

    curRemoveButton.click =>
      @clearSelection()
      @performRemove(vid)

    curCancelButton.click =>
      @clearSelection()
      @performCancel(vid)

    curEditButton.click =>
      @clearSelection()
      @performEdit(vid)

    curCheckButton.click =>
      @clearSelection()
      @performCheck(vid)

    curValueSelect.click ->
      switch $(this).val()
        when "elem-value", "array-custom"
          curValueField.show("slow")
          curSizeField.hide("slow")
        when "array-?", "array-random"
          curValueField.hide("slow")
          curSizeField.show("slow")
        else
          curValueField.hide("slow")
          curSizeField.hide("slow")

  updatePlaceholders: ->
    $(".dummyRow").remove()
    $("#insertVarsHere").prepend(@template.varDummyRow())
    $(".varRow").after(@template.varDummyRow())

    $(".dummyRow").droppable
      accept: "#btnAddVar"
      hoverClass: "dummyRow-hover"
      drop: => @addRow(`$(this)`)

# The main window for adding/editing variables.
varForm = new VariableForm()
varForm.updatePlaceholders()

$ ->
  $("#insertVarsHere")
    .selectable
        cancel: ".handle, .form-control, button"
    .sortable
        handle: ".handle"
        placeholder: "ui-state-highlight"
        stop: -> varForm.updatePlaceholders()

  $("#btnAddVar").click ->
    varForm.addRowBelow()

  $("#btnAddVar").draggable
    helper: "clone"
    revert: "invalid"
    start: () -> $(".dummyRow").show()
    stop: () -> $(".dummyRow").hide()

  # bind key strokes
  $.keyStroke 38, -> varForm.moveSelectionUp("select")
  $.keyStroke 38, { modKeys: ['altKey'] }, -> varForm.moveSelectionUp("move")
  $.keyStroke 38, { modKeys: ['shiftKey'] }, -> varForm.moveSelectionUp("extend")
  $.keyStroke 40, -> varForm.moveSelectionDown("select")
  $.keyStroke 40, { modKeys: ['altKey'] }, -> varForm.moveSelectionDown("move")
  $.keyStroke 40, { modKeys: ['shiftKey'] }, -> varForm.moveSelectionDown("extend")
  $.keyStroke 46, -> varForm.performActionOnSelection("remove") # del
  $.keyStroke 27, -> varForm.performActionOnSelection("cancel") # esc
  $.keyStroke 13, -> varForm.performActionOnSelection("check") # return