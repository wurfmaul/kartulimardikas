###
// FIXME: find something that works!
//$(window).bind('beforeunload', function() {
//    //warn before page is left, to prevent data loss
//    return 'Are you sure you want to leave? Unsaved changes get lost!';
//});
###

$ ->
  $(".panel-heading").click ->
    $(this).find("span").toggleClass("glyphicon-chevron-right glyphicon-chevron-down")

###
function updateSelects() {
    var optionsVars = "";
    for (var i = 0; i < vars.size(); i++) {
        optionsVars += "<option>" + vars.get(i).name + "</option>";
    }
    $(".slct-allVars").html(optionsVars);

    var optionsNonVoidInsts = "";
    for (var i = 0; i < instr.size(); i++) {
        var inst = instr.get(i);
        if (inst.retType != instr.RETVOID) {
            optionsNonVoidInsts += '<option value="' + inst.id + '">' + inst.toString() + "</option>";
        }
    }
    $(".slct-allNonVoidInsts").html(optionsNonVoidInsts);

    var optionsAllInsts = "";
    for (var i = 0; i < instr.size(); i++) {
        var inst = instr.get(i);
        optionsAllInsts += '<option value="' + inst.id + '">' + inst.toString() + "</option>";
    }
    $(".slct-allInsts").html(optionsAllInsts);

    var optionsLines = "";
    for (var i = 0; i < lines.size(); i++) {
        var inst = lines.get(i);
        optionsLines += '<option value="' + inst.id + '">' + inst.toString() + "</option>";
    }
    $(".slct-allLines").html(optionsLines);

    var optionsBool = "";
    for (var i = 0; i < vars.size(); i++) {
        var curVar = vars.get(i);
        optionsVars += '<option value="v' + curVar.id + '">' + curVar.toString() + "</option>";
    }
    for (var i = 0; i < instr.size(); i++) {
        var inst = instr.get(i);
        if (inst.retType == instr.RETBOOL) {
            optionsBool += '<option value="i' + inst.id + '">' + inst.toString() + "</option>";
        }
    }
    $(".slct-allBools").html(optionsBool);
}
###

###
 This class provides a validator for the client-provided text input. It offers the possibillity to check the
 correctness of the input fields of the main form.
###
class window.Validator
  # Enum value for registers (see this.target).
  @REGISTER: 0
  # Enum value for lists.
  @LIST: 1

  constructor: (@vars) ->
    # This is the form's input field that is to be highlighted in case of an error.
    @inputField
    # This is the area in the form where the error message should be shown.
    @errorLoc

  checkNotEmpty: (value) ->
    if (not value? or value.replace(/\s+/g, "") is "")
      @inputField.addClass("has-error");
      @errorLoc.append(Templates.error("Value is not allowed: '#{value}'"));
      return false
    true

  checkExists: (id) ->
    if (id?)
      @inputField.addClass("has-error")
      @errorLoc.append(Templates.error("No valid variable/intruction selected."))
      return false
    true

  checkIndex: (value, maxIndex) ->
    if (value.search(/^\d+$/) isnt 0)
      @inputField.addClass("has-error")
      @errorLoc.append(Templates.error("Index '#{value}' is not valid. Must be integer."))
      return false
    else if (value > maxIndex)
      @inputField.addClass("has-error")
      @errorLoc.append(Templates.error("Index '#{value}' is out of range (max. #{maxIndex})"))
      return false
    true

  ###
   * This method checks whether the entered name is valid or not. It moreover
   * checks if the name does already exist.
   *
   * @param name
   *                The entered string for the variable's name.
   *
   * @param ignoreName
   *                Optionally a name that is ignored by the
   *                name-duplication-check.
  ###
  checkName: (name, ignoreName) ->
    msg = new Array()
    # check if name exists
    if name isnt ignoreName and @vars.findName(name) isnt -1
      msg.push("Name '#{name}' does already exist!")

    # check validity of name, \w = [A-Za-z0-9_]
    if (name.search(/^[a-zA-Z]\w*$/) == -1)
      msg.push("Name '#{name}' is not valid. Allowed characters: [A-Za-z0-9_], starting with letter.")

    # print messages
    if (msg.length > 0)
      @inputField.addClass("has-error")
      @errorLoc.append(Templates.error(msg.pop())) while (msg.length > 0)
      return false
    true

  ###
   * This method checks whether the entered value is valid or not.
   *
   * @param value
   *                The entered string for the variable's value.
  ###
  checkValue: (value) ->
    # check for integer, \d = [0-9]
    check = value.search(/^\s*-?\s*\d+\s*$/) == 0

    # check for string
    # check |= value.search(/^["'][A-ZÄÖÜa-zäöü0-9_ ]*["']$/) == 0;

    if (!check)
      @inputField.addClass("has-error")
      @errorLoc.append(Templates.error("Value '" + value + "' is not valid. Allowed: integers"))
      return false
    true

  checkValues: (values) ->
    # check for valid number, \d = [0-9], \s = white
    check = values.search(/^(-?\d+\s*,\s*)+(-?\d+\s*,?\s*)$/) == 0
    if (!check)
      @inputField.addClass("has-error")
      @errorLoc.append(Templates.error("Values '#{values}' are not valid. Allowed: integers. Separator: ,"))
      return false
    true

  ###
   * Dependent on whether a register or a list is currently checked, the HTML
   * elements in the page differ. Therefore there has to be a mode for every
   * different variable, providing information on where input is taken
   * from and errors are written to.
  ###
  target: (inputField, errorLocation) ->
    @inputField = $(inputField)
    @errorLoc = $(errorLocation)

  reset: ->
    $(".has-error").removeClass("has-error")
    $(".alert").alert('close')

###
This class provides several templates for HTML content that is inserted to the page dynamically.
###
class window.Templates
  constructor: (@vars) ->

  varRowShow: (vid) ->
    v = @vars.getById(vid)
    """
    <tr id="var-#{vid}" class="varRow">
      <td class="handle">⣿</td>
      <td style="vertical-align: middle; text-align: left;">
        <code class="cell">#{v.name} = #{v.value}</code>
      </td>
      <td class='buttonArea'>
        <div class="btn-group btn-group-xs">
          <button type="button" class="btn btn-default" id="btn-var-#{vid}-edit" value="#{vid}">
            <span class="glyphicon glyphicon-pencil"></span>
          </button>
          <button type="button" class="btn btn-default" id="btn-var-#{vid}-remove" value="#{vid}" title="Remove variable">
            <span class="glyphicon glyphicon-trash"></span>
          </button>
        </div>
      </td>
    </tr>
    """

  varRowEdit: (vid) ->
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
        when @vars.constructor.UNINITIALIZED
          if @vars.isArrayById(vid)
            arrayUninitSelected = sel
            sizeInvisible = ""
            sizeSelected[variable.value.length] = sel
          else
            elemUninitSelected = sel

        when @vars.constructor.RANDOMIZED
          arrayRandomSelected = sel
          sizeInvisible = ""
          sizeSelected[variable.value.length] = sel

        when @vars.constructor.CUSTOMIZED
          if @vars.isArrayById(vid)
            arrayCustomSelected = sel
          else
            elemValueSelected = sel
          valueInvisible = ""
          value = variable.value

        else
          console.log "unknown initialization #{variable.init}"

    """
    <tr id="var-#{vid}" class="varRow" style="display: none;">
      <td class="handle">⣿</td>
      <td style="vertical-align: middle;">
        <div class="col-xs-3">
          <div class="form-group" id="var-#{vid}-nameField" style="margin-bottom:0px">
            <label class="sr-only" for="var-#{vid}-name">Variable name</label>
            <input type="text" class="form-control" id="var-#{vid}-name" value="#{name}" placeholder="name">
          </div>
        </div>
        <div class="col-xs-2" style="text-align: center;">
          <div class="cell"><code>=</code></div>
        </div>
        <div class="col-xs-3">
          <div class="form-group" style="margin-bottom:0px">
            <label class="sr-only" for="var-#{vid}-init">Initialization</label>
            <select class="form-control" id="slct-var-#{vid}-init">
              <optgroup label="Element">
                <option value="elem-?" #{elemUninitSelected}>uninitialized</option>
                <option value="elem-value" #{elemValueSelected}>value</option>
              </optgroup>
              <optgroup label="Array">
                <option value="array-?" #{arrayUninitSelected}>uninitialized</option>
                <option value="array-random" #{arrayRandomSelected}>random</option>
                <option value="array-custom" #{arrayCustomSelected}>custom</option>
              </optgroup>
            </select>
          </div>
        </div>
        <div class="col-xs-4">
          <div class="form-group" id="var-#{vid}-valueField" style="margin-left: 0px; margin-bottom:0px; #{valueInvisible}">
            <label class="sr-only" for="var-#{vid}-value">Initial value</label>
            <input type="text" class="form-control" id="var-#{vid}-value" value="#{value}" placeholder="value">
          </div>
          <div class="form-group" id="var-#{vid}-sizeField" style="margin-left: 0px; margin-bottom:0px; #{sizeInvisible}">
            <label class="sr-only" for="var-#{vid}-size">Array size</label>
            <select class="form-control" id="var-#{vid}-size">
              <optgroup label="Size">
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
      <td class='buttonArea'>
        <div class="btn-group btn-group-xs">
          <button type="button" class="btn btn-default" id="btn-var-#{vid}-check" value="#{vid}" title="Check and add/edit variable">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <button type="button" class="btn btn-default" id="btn-var-#{vid}-cancel" value="#{vid}" title="Discard changes">
            <span class="glyphicon glyphicon-remove"></span>
          </button>
        </div>
      </td>
    </tr>
    """

  varDummyRow: ->
    """<tr class="dummyRow" style="display: none;"><td colspan="3"></td></tr>"""

  ###
   * Returns an HTML representation of an error message.
   *
   * @param message
   *                The message that is to be displayed.
  ###
  @error: (message) ->
    """
    <div class="alert alert-danger alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <strong>Error!</strong> #{message}
    </div>
    """

  ###
   * Returns an HTML representation of a warning message.
   *
   * @param message
   *                The message that is to be displayed.
  ###
  @warning: (message) ->
    """
    <div class="alert alert-warning alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <strong>Warning!</strong> #{message}
    </div>
    """