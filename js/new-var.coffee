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
    @vars.push(new @Data(id, name, value, init))

  edit: (id, newName, newValue, init) ->
    i = @findId(id)
    @vars[i] = new @Data(id, newName, newValue, init)

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

  @Data: (id, name, value, init) ->
    @id = id
    @name = name
    @value = value
    @init = init

class ElementFactory
  constructor: (@vars) ->
    @valid = new Validator(@vars)

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
    @valid = new Validator(@vars)

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
        @values.push("?") for [0..@size]

      when Variables.RANDOMIZED
        @size = $("#var-#{@id}-size").val();
        @values = shuffle ([0..@size])

      when Variables.CUSTOMIZED
        values = $("#var-#{@id}-value").val()
        @valid.target "#var-#{@id}-valueField", "#alert-var"
        if @valid.checkValues(values)
          tokens = values.split(DELIM)
          @size = tokens.length
          for i in [0..@size]
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
        $("#alert-var").append(err.error("List has to be initialized!"))
    check

class VariableForm
  constructor: ->
    @maxVarId = 0
    @vars = new Variables()
    @varTemplate = new VariableTemplates(@vars)
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
      @addRow($(".varRow").last())

  addRow: (prevRow) ->
    if prevRow.length > 0
      prevRow.after(@varTemplate.rowEdit(@maxVarId))
    else
      # if it is the first line
      $("#insertVarsHere").append(@varTemplate.rowEdit(@maxVarId))
    $("#var-" + @maxVarId).show("slow")
    @updateActionHandlers(@maxVarId)
    @updatePlaceholders()
    @maxVarId++

  checkAndCreateVar: (vid) ->
    success = false
    switch $("#slct-var-" + vid + "-init").val()
      when "elem-?" then success = @elementFactory.create(vid, Variables.UNINITIALIZED)
      when "elem-value" then success = @elementFactory.create(vid, Variables.CUSTOMIZED)
      when "array-?" then success = @arrayFactory.create(vid, Variables.UNINITIALIZED)
      when "array-random" then success = @arrayFactory.create(vid, Variables.RANDOMIZED)
      when "array-custom" then success = @arrayFactory.create(vid, Variables.CUSTOMIZED)
      else console.log "can't recognize value..."
    success

  checkAndEditVar: (vid) ->
    success = false
    switch ($("#slct-var-" + vid + "-init").val())
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

  moveSelectionDown: (mode) ->
    selection = @getSelection()
    if selection?
      # if some line is selected
      # get next line, step over placeholder
      nextLine = selection.last().next().next() # next(".varRow")
      if nextLine.prop("id")?
        # if there is a line below
        switch mode
          when "move"
            nextLine.detach()
            selection.first().before(nextLine)
          when "select" then $("#insertVarsHere .ui-selected").removeClass("ui-selected")
          when "extend" then nextLine.addClass("ui-selected")
          else console.log "unknown mode: #{mode}"
        @updatePlaceholders()


  moveSelectionUp: (mode) ->
    selection = @getSelection()
    if selection?
      # if some line is selected
      # get previous line, step over placeholder
      prevLine = selection.first().prev().prev() # prev(".varRow")
      if prevLine.prop("id")?
        # if there is a line above
        switch mode
          when "move"
            prevLine.detach
            selection.last().after prevLine
          when "select" then $("#insertVarsHere .ui-selected").removeClass("ui-selected")
          when "extend" then prevLine.addClass("ui-selected")
          else console.log "unknown mode: #{mode}"
        @updatePlaceholders()


  performActionOnSelection: (mode) =>
    selection = @getSelection()
    if selection?
      @clearSelection()
      selection.each =>
        vid = $(this).prop("id").split "-", 1
        switch mode
          when "remove" then @performRemove(vid)
          when "cancel" then @performCancel(vid)
          when "check" then @performCheck(vid)
          else console.log "unknown mode: #{mode}"

  performCancel: (vid) ->
    if @vars.findId(vid) is -1
      # variable does not exist yet -> reset form
      $("#var-" + vid).replaceWith(@varTemplate.rowEdit(vid))
    else
      # variable exists -> discard changes
      $("#var-" + vid).replaceWith(@varTemplate.rowShow(vid))
    @updateActionHandlers(vid)
    @select(vid)

  performCheck: (vid) ->
    if @vars.findId(vid) isnt -1
      # var already exists
      if @checkAndEditVar(vid)
        $("#var-" + vid).replaceWith(@varTemplate.rowShow(vid))
    else
      if @checkAndCreateVar(vid)
        $("#var-" + vid).replaceWith(@varTemplate.rowShow(vid))
    @updateActionHandlers(vid)
    @select(vid)

  performEdit: (vid) ->
    $("#var-" + vid).replaceWith(@varTemplate.rowEdit(vid))
    @updateActionHandlers(vid)
    @select(vid)

  performRemove: (vid) ->
    @vars.removeById(vid) # FIXME call factory for deletion
    try
      nextVid = $("#var-" + vid).next().next().prop("id").split("-", 1)
      @select(nextVid)
    catch e
    #	$("#var-" + vid).hide("slow"); FIXME animation on deletion
    $("#var-" + vid).remove()
    @updatePlaceholders()

  clearSelection: ->
    $(".ui-selected").removeClass("ui-selected")

  select: (vid) ->
    $("#var-" + vid).addClass("ui-selected")

  updateActionHandlers: (vid) ->
    console.log "update #{vid}"
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
    $("#insertVarsHere").prepend(@varTemplate.dummyRow())
    $(".varRow").after(@varTemplate.dummyRow())

    $(".dummyRow").droppable
      accept: "#btnAddVar"
      hoverClass: "dummyRow-hover"
      drop: => @addRow(`$(this)`)

###
This class provides several templates for HTML content that is inserted to the page dynamically.
###
class VariableTemplates
  constructor: (@vars) ->

  rowShow: (vid) ->
    v = @vars.getById(vid)
    "<tr id='var-#{vid}' class='varRow'>
          <td class='handle' style='cursor: pointer;'>⣿</td>
          <td style='vertical-align: middle; text-alignment: left;'>
            <code class='cell'>#{v.name} = #{v.value}</code>
          </td>
          <td style='width: 65pt; text-align: center;'>
            <div class='btn-group btn-group-xs'>
              <button type='button' class='btn btn-default' id='btn-var-#{vid}-edit' value='#{vid}'><span class='glyphicon glyphicon-pencil'></span></button>
            </div>'
          </td>'
        </tr>"

  rowEdit: (vid) ->
    variable = @vars.getById(vid)
    name = ""
    elemUninitSelected = ""
    elemValueSelected = ""
    arrayUninitSelected = ""
    arrayRandomSelected = ""
    arrayCustomSelected = ""
    valueInvisible = " display: none;"
    value = ""
    sizeInvisible = " display: none;"
    sizeSelected = new Array()
    sizeSelected.push("") for [0..13]

    if variable?
      # write name
      name = variable.name
      # write init/value
      sel = " selected"
      switch variable.init
        when Variables.UNINITIALIZED
          if @vars.isArrayById(vid)
            arrayUninitSelected = sel
            sizeInvisible = ""
            sizeSelected[variable.value.length] = sel
          else
            elemUninitSelected = sel

        when Variables.RANDOMIZED
          arrayRandomSelected = sel
          sizeInvisible = ""
          sizeSelected[variable.value.length] = sel

        when Variables.CUSTOMIZED
          if @vars.isArrayById(vid)
            arrayCustomSelected = sel
          else
            elemValueSelected = sel
          valueInvisible = ""
          value = variable.value

        else console.log "unknown initialization #{variable.init}"

    "<tr id='var-#{vid}' class='varRow' style='display: none;'>
        <td class='handle' style='cursor: pointer;'>⣿</td>
        <td style='vertical-align: middle;'>
        <div class='col-xs-3'>
        	<div class='form-group' id='var-#{vid}-nameField' style='margin-bottom:0px'>
        		<label class='sr-only' for='var-#{vid}-name'>Variable name</label>
        		<input type='text' class='form-control' id='var-#{vid}-name' value='#{name}' placeholder='name'>
        	</div>
        </div>
        <div class='col-xs-2' style='text-align: center;'>
        	<div class='cell'><code>=</code></div>
        </div>
        <div class='col-xs-3'>
        	<div class='form-group' style='margin-bottom:0px'>
        		<label class='sr-only' for='var-#{vid}-init'>Initialization</label>
        		<select class='form-control' id='slct-var-#{vid}-init'>
        			<optgroup label='Element'>
        				<option value='elem-?' #{elemUninitSelected}>uninitialized</option>
        				<option value='elem-value' #{elemValueSelected}>value</option>
        			</optgroup>
        			<optgroup label='Array'>
        				<option value='array-?' #{arrayUninitSelected}>uninitialized</option>
        				<option value='array-random' #{arrayRandomSelected}>random</option>
        				<option value='array-custom' #{arrayCustomSelected}>custom</option>
        			</optgroup>
        		</select>
        	</div>
        </div>
        <div class='col-xs-4'>
        	<div class='form-group' id='var-#{vid}-valueField' style='margin-left: 0px; margin-bottom:0px; #{valueInvisible}'>
        		<label class='sr-only' for='var-#{vid}-value'>Initial value</label>
        		<input type='text' class='form-control' id='var-#{vid}-value' value='#{value}' placeholder='value'>
        	</div>
        	<div class='form-group' id='var-#{vid}-sizeField' style='margin-left: 0px; margin-bottom:0px; #{sizeInvisible}'>
        		<label class='sr-only' for='var-#{vid}-size'>Array size</label>
        		<select class='form-control' id='var-#{vid}-size'>
        			<optgroup label='Size'>
        				<option #{sizeSelected[2]}>2</option><option #{sizeSelected[3]}>3</option>
        				<option #{sizeSelected[4]}>4</option><option #{sizeSelected[5]}>5</option>
        				<option #{sizeSelected[6]}>6</option><option #{sizeSelected[7]}>7</option>
        				<option #{sizeSelected[8]}>8</option><option #{sizeSelected[9]}>9</option>
        				<option #{sizeSelected[10]}>10</option><option #{sizeSelected[11]}>11</option>
        				<option #{sizeSelected[12]}>12</option><option #{sizeSelected[13]}>13</option>
        			</optgroup>
        		</select>
        	</div>
        </div>
        </td>
        <td style='width: 65pt; text-align: center;'>
        <div class='btn-group btn-group-xs'>
        	<button type='button' class='btn btn-default' id='btn-var-#{vid}-check' value='#{vid}' title='Check and add/edit variable'>
            <span class='glyphicon glyphicon-ok'></span>
          </button>
        	<button type='button' class='btn btn-default' id='btn-var-#{vid}-cancel' value='#{vid}' title='Discard changes'>
            <span class='glyphicon glyphicon-remove'></span>
          </button>
        	<button type='button' class='btn btn-default' id='btn-var-#{vid}-remove' value='#{vid}' title='Remove variable'>
            <span class='glyphicon glyphicon-trash'></span>
          </button>
        </div>
        </td>
        </tr>"

  dummyRow: -> "<tr class='dummyRow' style='display: none;'><td colspan='3'></td></tr>"

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