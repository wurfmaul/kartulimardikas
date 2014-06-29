/**
 * This file contains every functionality concerning variable generation 
 * and management.
 */

/** Represents delimiter of list elements in customized view. */
var DELIM = ',';
/** Specifies the site, where variables are to place. */
var VARSITE = $("#placeVariablesHere");
/** The main window for adding/editing variables. */
var varForm = new VariableForm();
/** Represents the collection of existing variables */
var vars = new Variables();

/** Provides an interface for adding/editing registers. */
var elementFactory = new ElementFactory();
/** Provides an interface for adding/editing lists. */
var arrayfactory = new ArrayFactory();

var varTemplate = new VarTemplate();
var maxVarId = 0;

$("#btn-addVar").click(function() {
    $(this).hide();
    $("#placeVariablesHere").show();
    varForm.addRowBelow(-1);
});

function ElementFactory() {
    this.id;
    this.name;
    this.init;
    this.value;

    /** The default value of a register's name. */
    this.DEFAULTNAME = "";
    /** The default method for a register's initialization. */
    this.DEFAULTINIT = vars.UNINITIALIZED;
    /** The default value for a register's value. */
    this.DEFAULTVALUE = "";

    this.create = function(id, init) {
	this.id = id;
	this.init = init;
	// check input fields
	if(this.check(null)) {
	    // add register to internal structure
	    vars.add(this.id, this.name, this.value, this.init);
	    return true;
	}
	return false;
    };

    this.edit = function(id, init) {
	this.id = id;
	this.init = init;
	var oldName = vars.getById(id).name;
	// check input fields
	if(this.check(oldName)) {
	    // add register to internal structure
	    vars.edit(this.id, this.name, this.value, this.init);
	    return true;
	}
	return false;
    };

    this.check = function(oldName) {
	valid.target("#var-" + this.id + "-nameField", "#alert-var");
	valid.reset();
	
	// get name from input field
	this.name = $("#var-" + this.id + "-name").val();
	var check = valid.checkName(this.name, oldName);

	// retrieve value
	if (this.init == vars.CUSTOMIZED) {
	    this.value = $("#var-" + this.id + "-value").val();
	    valid.target("#var-" + this.id + "-valueField", "#alert-var");
	    check = valid.checkValue(this.value) && check;
	} else {
	    this.value = "?";
	}
	return check;
    };
}

function ArrayFactory() {
    this.id;
    /** The name of the currently edited list. */
    this.name;
    /** The size of the currently edited list. */
    this.size;
    /** The initialization method of the currently edited list. */
    this.init;
    /** The values of the currently edited list. */
    this.values;

    /** The default value of a list's name. */
    this.DEFAULTNAME = "";
    /** The default size of a list. */
    this.DEFAULTSIZE = 7;
    /** The default value of a register's initialization method. */
    this.DEFAULTINIT = vars.UNINITIALIZED;
    /** The default value of a list's values. */
    this.DEFAULTVALUES = "";

    /** Add the list that was specified by the HTML add form. */
    this.create = function(id, init) {
	this.id = id;
	this.init = init;
	// check input fields
	if(this.check(null)) {
	    // add register to internal structure
	    vars.add(this.id, this.name, this.values, this.init);
	    return true;
	}
	return false;
    };

    /**
     * Edit the list that was specified by the HTML add form.
     * 
     * @param oldName
     *                The unique name of the list that is to be edited.
     */
    this.edit = function(id, init) {
	this.id = id;
	this.init = init;
	
	var oldName = vars.getById(id).name;
	// check input fields
	if(this.check(oldName)) {
	    // add register to internal structure
	    vars.edit(this.id, this.name, this.values, this.init);
	    return true;
	}
	return false;
    };

    /**
     * Check the data that was provided by the HTML add/edit form by the
     * validator.
     * 
     * @param oldName
     *                The unique name of the list that is to be checked.
     */
    this.check = function(oldName) {
	// tell validator that we are dealing with lists
	valid.target("#var-" + this.id + "-nameField", "#alert-var");
	
	// clear errors
	$(".has-error").removeClass("has-error");
	$(".alert").alert('close');

	// get name from input field
	this.name = $("#var-" + this.id + "-name").val();
	var check = valid.checkName(this.name, oldName);

	// retrieve values
	this.values = new Array();
	switch (this.init) {
	case vars.UNINITIALIZED:
	    this.size = $("#var-" + this.id + "-size").val();
	    for(var i = 0; i < this.size; i++) {
		this.values.push("?");
	    }
	    break;
	case vars.RANDOMIZED:
	    this.size = $("#var-" + this.id + "-size").val();
	    for(var i = 0; i < this.size; i++) {
		this.values.push(i);
	    }
	    shuffle(this.values);
	    break;
	case vars.CUSTOMIZED:
	    var values = $("#var-" + this.id + "-value").val();
	    valid.target("#var-" + this.id + "-valueField", "#alert-var");
	    if (valid.checkValues(values)) {
		var tokens = values.split(DELIM);
		this.size = tokens.length;

		for(var i = 0; i < this.size; i++) {
		    // trim
		    var value = tokens[i].replace(/ /, "");
		    // check value
		    if (valid.checkValue(value)) {
			this.values[i] = value;
		    } else {
			this.values[i] = "?";
			check = false;
		    }
		}
	    } else {
		check = false;
	    }
	    break;
	default:
	    $("#alert-var").append(err.error("List has to be initialized!"));
	}
	return check;
    };

}

function VariableForm() {
    
    this.addRow = function(lastRow) {
	if (typeof lastRow.prop("id") === "undefined") {
	    // if it is the first line
	    $("#insertVarsHere").append(varTemplate.rowEdit(maxVarId));
	} else {
	    lastRow.after(varTemplate.rowEdit(maxVarId));
	}
	this.updateActionHandlers(maxVarId);
	this.updatePlaceholders();
	maxVarId++;
    };
    
    this.checkAndCreateVar = function(vid) {
	var success = false;
	switch($("#slct-var-" + vid + "-init").val()) {
	case "elem-?":
	    success = elementFactory.create(vid, vars.UNINITIALIZED);
	    break;
	case "elem-value":
	    success = elementFactory.create(vid, vars.CUSTOMIZED);
	    break;
	case "array-?":
	    success = arrayfactory.create(vid, vars.UNINITIALIZED);
	    break;
	case "array-random":
	    success = arrayfactory.create(vid, vars.RANDOMIZED);
	    break;
	case "array-custom":
	    success = arrayfactory.create(vid, vars.CUSTOMIZED);
	    break;
	}
	return success;
    };
    
    this.checkAndEditVar = function(vid) {
	var success = false;
	switch($("#slct-var-" + vid + "-init").val()) {
	case "elem-?":
	    success = elementFactory.edit(vid, vars.UNINITIALIZED);
	    break;
	case "elem-value":
	    success = elementFactory.edit(vid, vars.CUSTOMIZED);
	    break;
	case "array-?":
	    success = arrayfactory.edit(vid, vars.UNINITIALIZED);
	    break;
	case "array-random":
	    success = arrayfactory.edit(vid, vars.RANDOMIZED);
	    break;
	case "array-custom":
	    success = arrayfactory.edit(vid, vars.CUSTOMIZED);
	    break;
	}
	return success;
    };
    
    this.moveSelectionDown = function(mode) {
	var selection = $("#insertVarsHere tr.ui-selected");
	if (typeof selection.last().prop("id") !== "undefined") {
	    // if some line is selected
	    // get next line, step over placeholder
	    var nextLine = selection.last().next().next(); // next(".varRow")
	    if (typeof nextLine.prop("id") !== "undefined") {
		// if there is a line below
		
		switch (mode) {
		case "move":
		    nextLine.detach();
		    selection.first().before(nextLine);
		    break;
		case "select":
		    $("#insertVarsHere .ui-selected").removeClass("ui-selected");
		case "extend":
		    nextLine.addClass("ui-selected");
		}
		this.updatePlaceholders();
	    }
	}
    };
    
    this.moveSelectionUp = function(mode) {
	var selection = $("#insertVarsHere tr.ui-selected");
	if (typeof selection.first().prop("id") !== "undefined") {
	    // if some line is selected
	    // get previous line, step over placeholder
	    var prevLine = selection.first().prev().prev(); // prev(".varRow")
	    if (typeof prevLine.prop("id") !== "undefined") {
		// if there is a line above
		switch (mode) {
		case "move":
		    prevLine.detach();
		    selection.last().after(prevLine);
		    break;
		case "select":
		    $("#insertVarsHere .ui-selected").removeClass("ui-selected");
		case "extend":
		    prevLine.addClass("ui-selected");
		}
		this.updatePlaceholders();
	    }
	}
    };
    
    this.performActionOnSelection = function(mode) {
	var selection = $("#insertVarsHere tr.ui-selected");
	this.clearSelection();
	
	selection.each(function() {
	    var vid = $(this).prop("id").split("-")[1];
	    switch (mode) {
	    case "remove":
		varForm.performRemove(vid);
		break;
	    case "cancel":
		varForm.performCancel(vid);
		break;
	    case "check":
		varForm.performCheck(vid);
		break;
	    default:
		break;
	    }
	});
    };
    
    this.performCancel = function(vid) {
	if (vars.findId(vid) == -1) {
	    // variable does not exist yet -> reset form
	    $("#var-" + vid).replaceWith(varTemplate.rowEdit(vid));
	} else {
	    // variable exists -> discard changes
	    $("#var-" + vid).replaceWith(varTemplate.rowShow(vid));
	}
	this.updateActionHandlers(vid);
	this.select(vid);
    };
    
    this.performCheck = function(vid) {
	if (vars.findId(vid) != -1) {
	    // var already exists
	    if(this.checkAndEditVar(vid)) {
		$("#var-" + vid).replaceWith(varTemplate.rowShow(vid));
	    }
	} else {
	    if (this.checkAndCreateVar(vid)) {
		$("#var-" + vid).replaceWith(varTemplate.rowShow(vid));
	    }
	}
	this.updateActionHandlers(vid);
	this.select(vid);
    };
    
    this.performEdit = function(vid) {
	$("#var-" + vid).replaceWith(varTemplate.rowEdit(vid));
	this.updateActionHandlers(vid);
	this.select(vid);
    };
    
    this.performRemove = function(vid) {
	vars.removeById(vid); // FIXME call factory for deletion
	try {
	    var nextVid = $("#var-" + vid).next().next().prop("id").split("-")[1];
	    this.select(nextVid);
	} catch (e) {
	}
	$("#var-" + vid).remove();
	this.updatePlaceholders();
    };

    this.clearSelection = function() {
	$(".ui-selected").removeClass("ui-selected");
    };
    
    this.select = function(vid) {
	$("#var-" + vid).addClass("ui-selected");
    };
    
    this.updateActionHandlers = function(vid) {
	var curRemoveButton = $("#btn-var-" + vid + "-remove");
	var curEditButton = $("#btn-var-" + vid + "-edit");
	var curCheckButton = $("#btn-var-" + vid + "-check");
	var curCancelButton = $("#btn-var-" + vid + "-cancel");
	var curValueSelect = $("#slct-var-" + vid + "-init");
	
	// deactivate old action handlers
	curRemoveButton.off("click");
	curEditButton.off("click");
	curCheckButton.off("click");
	curCancelButton.off("click");
	curValueSelect.off("click");

	curRemoveButton.click(function() {
	    varForm.clearSelection();
	    varForm.performRemove(vid); 
	});

	curCancelButton.click(function() {
	    varForm.clearSelection();
	    varForm.performCancel(vid);
	});
	
	curEditButton.click(function() {
	    varForm.clearSelection();
	    varForm.performEdit(vid);
	});

	curCheckButton.click(function() {
	    varForm.clearSelection();
	    varForm.performCheck(vid);
	});
	
	curValueSelect.click(function() {
	    var value = $(this).val();
	    var targetVal = $($(this).data("options").targetVal);
	    var targetSize = $($(this).data("options").targetSize);
	    
	    switch (value) {
	    case "elem-value":
	    case "array-custom":
		targetVal.show("slow");
		targetSize.hide("slow");
		break;
	    case "array-?":
	    case "array-random":
		targetVal.hide("slow");
		targetSize.show("slow");
		break;
	    default:
		targetVal.hide("slow");
	    	targetSize.hide("slow");
	    }
	});
    };
    
    this.updatePlaceholders = function() {
	$(".dummyRow").remove();
	$("#insertVarsHere").prepend(varTemplate.dummyRow());
	$(".varRow").after(varTemplate.dummyRow());

	$(".dummyRow").droppable({
	    accept: "#btnAddVar",
	    hoverClass: "dummyRow-hover",
	    drop: function( event, ui ) {
		varForm.addRow($(this));
	    }
	});
    };
}

/**
 * This class is the central collection of all available variables. It is
 * simply an implementation of a collection of key-value pairs.
 */
function Variables() {
    /** Initialization method number 1: no initialization. */
    this.UNINITIALIZED = 0;
    /** Initialization method number 2: random initialization. */
    this.RANDOMIZED = 1;
    /** Initialization method number 3: custom initialization. */
    this.CUSTOMIZED = 2;

    /** The basic array to store all the values. */
    this.vars = new Array();
    this.maxId = 0;

    this.add = function(id, name, value, init) {
	this.vars.push(new this.Data(id, name, value, init));
    };

    this.edit = function(id, newName, newValue, init) {
	var i = this.findId(id);
	this.vars[i] = new this.Data(id, newName, newValue, init);
    };
    
    this.find = function(name) {
	for (var i = 0; i < this.size(); i++) {
	    if (this.vars[i].name == name) {
		return i;
	    }
	}
	return -1;
    };
    
    this.findId = function(id) {
	for (var i = 0; i < this.size(); i++) {
	    if (this.vars[i].id == id) {
		return i;
	    }
	}
	return -1;
    };
    
    this.get = function(i) {
	return this.vars[i];
    };
    
    this.getById = function(id) {
	for (var i = 0; i < this.size(); i++) {
	    if(this.vars[i].id == id) {
		return this.vars[i];
	    }
	}
    };
    
    this.getByName = function(name) {
	return this.vars[this.find(name)];
    };
    
    this.isArray = function(i) {
	return $.isArray(this.vars[i].value);
    };
    
    this.isArrayByName = function(name) {
	return $.isArray(this.getByName(name).value);
    };
    
    this.isArrayById = function(id) {
	return $.isArray(this.getById(id).value);
    };

    this.removeByName = function(name) {
	this.vars.splice(this.find(name), 1);
    };
    
    this.removeById = function(id) {
	this.vars.splice(this.findId(id), 1);
    };

    this.size = function() {
	return this.vars.length;
    };

    this.toString = function(name) {
	var value = this.getByName(name).value;
	if ($.isArray(value)) {
	    var ret = "";
	    for (var i = 0; i < value.length; i++){
		ret += value[i];
		if (i < value.length - 1)
		    ret += "; ";
	    }
	    return ret;
	} else {
	    return value;
	}
    };

    this.Data = function(id, name, value, init) {
	this.id = id;
	this.name = name;
	this.value = value;
	this.init = init;
    };
}

/**
 * This class provides several templates for HTML content that is inserted to
 * the page dynamically.
 */
function VarTemplate() {
    this.rowShow = function(vid) {
	var v = vars.getById(vid);
	
	return ''
	+ '<tr id="var-' + vid + '" class="varRow">'
	+ '	<td class="handle" style="cursor: pointer;">⣿</td>'
	+ '	<td style="vertical-align: middle; text-alignment: left;">'
	+ '		<code class="cell">' + v.name + ' = ' + v.value + '</code>'
	+ '	</td>'
	+ '	<td style="width: 65pt; text-align: center;">'
	+ '		<div class="btn-group btn-group-xs">'
	+ '			<button type="button" class="btn btn-default" id="btn-var-' + vid + '-edit" value="' + vid + '"><span class="glyphicon glyphicon-pencil"></span></button>'
	+ '		</div>'
	+ '	</td>'
	+ '</tr>'
	+ '';
    };
    
    this.rowEdit = function(vid) {
	var variable = vars.getById(vid);

	var name = "";
	var elemUninitSelected = "";
	var elemValueSelected = "";
	var arrayUninitSelected = "";
	var arrayRandomSelected = "";
	var arrayCustomSelected = "";
	var valueInvisible = " display: none;";
	var value = "";
	var sizeInvisible = " display: none;";
	var sizeSelected = new Array(13);
	for (var i = 0; i < 13; i++)
	    sizeSelected[i] = "";
	
	if (typeof variable != "undefined") {
	    // write name
	    name = variable.name;
	    
	    // write init/value
	    var sel = " selected";
	    switch (variable.init) {
	    case vars.UNINITIALIZED:
		if (vars.isArrayById(vid)) {
		    arrayUninitSelected = sel;
		    sizeInvisible = "";
		    sizeSelected[variable.value.length] = sel;
		} else
		    elemUninitSelected = sel;
		break;
	    case vars.RANDOMIZED:
		arrayRandomSelected = sel;
		sizeInvisible = "";
		sizeSelected[variable.value.length] = sel;
		break;
	    case vars.CUSTOMIZED:
		if (vars.isArrayById(vid))
		    arrayCustomSelected = sel;
		else
		    elemValueSelected = sel;
		valueInvisible = "";
		value = variable.value;
		break;
	    }
	}
	
	return ''
	+ '<tr id="var-' + vid + '" class="varRow">'
	+ '<td class="handle" style="cursor: pointer;">⣿</td>'
	+ '<td style="vertical-align: middle;">'
	+ '<div class="col-xs-3">'
	+ '	<div class="form-group" id="var-' + vid + '-nameField" style="margin-bottom:0px">'
	+ '		<label class="sr-only" for="var-' + vid + '-name">Variable name</label>'
	+ '		<input type="text" class="form-control" id="var-' + vid + '-name" value="' + name + '" placeholder="name">'
	+ '	</div>'
	+ '</div>'
	+ '<div class="col-xs-2" style="text-align: center;">'
	+ '	<div class="cell"><code>=</code></div>'
	+ '</div>'
	+ '<div class="col-xs-3">'
	+ '	<div class="form-group" style="margin-bottom:0px">'
	+ '		<label class="sr-only" for="var-' + vid + '-init">Initialization</label>'
	+ '		<select class="form-control" id="slct-var-' + vid + '-init" '
	+ '			data-options=\'{"targetVal":"#var-' + vid + '-valueField", "targetSize":"#var-' + vid + '-sizeField"}\'>'
	+ '			<optgroup label="Element">'
	+ '				<option value="elem-?"' + elemUninitSelected + '>uninitialized</option>'
	+ '				<option value="elem-value"' + elemValueSelected + '>value</option>'
	+ '			</optgroup>'
	+ '			<optgroup label="Array">'
	+ '				<option value="array-?"' + arrayUninitSelected + '>uninitialized</option>'
	+ '				<option value="array-random"' + arrayRandomSelected + '>random</option>'
	+ '				<option value="array-custom"' + arrayCustomSelected + '>custom</option>'
	+ '			</optgroup>'
	+ '		</select>'
	+ '	</div>'
	+ '</div>'
	+ '<div class="col-xs-4">'
	+ '	<div class="form-group" id="var-' + vid + '-valueField" style="margin-left: 0px; margin-bottom:0px;' + valueInvisible + '">'
	+ '		<label class="sr-only" for="var-' + vid + '-value">Initial value</label>'
	+ '		<input type="text" class="form-control" id="var-' + vid + '-value" value="' + value + '" placeholder="value">'
	+ '	</div>'
	+ '	<div class="form-group" id="var-' + vid + '-sizeField" style="margin-left: 0px; margin-bottom:0px;' + sizeInvisible + '">'
	+ '		<label class="sr-only" for="var-' + vid + '-size">Array size</label>'
	+ '		<select class="form-control" id="var-' + vid + '-size">'
	+ '			<optgroup label="Size">'
	+ '				<option' + sizeSelected[2] + '>2</option><option' + sizeSelected[3] + '>3</option>'
	+ '				<option' + sizeSelected[4] + '>4</option><option' + sizeSelected[5] + '>5</option>'
	+ '				<option' + sizeSelected[6] + '>6</option><option' + sizeSelected[7] + '>7</option>'
	+ '				<option' + sizeSelected[8] + '>8</option><option' + sizeSelected[9] + '>9</option>'
	+ '				<option' + sizeSelected[10] + '>10</option><option' + sizeSelected[11] + '>11</option>'
	+ '				<option' + sizeSelected[12] + '>12</option><option' + sizeSelected[13] + '>13</option>'
	+ '			</optgroup>'
	+ '		</select>'
	+ '	</div>'
	+ '</div>'
	+ '</td>'
	+ '<td style="width: 65pt; text-align: center;">'
	+ '<div class="btn-group btn-group-xs">'
	+ '	<button type="button" class="btn btn-default" id="btn-var-' + vid + '-check" value="' + vid + '" title="Check and add/edit variable"><span class="glyphicon glyphicon-ok"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-var-' + vid + '-cancel" value="' + vid + '" title="Discard changes"><span class="glyphicon glyphicon-remove"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-var-' + vid + '-remove" value="' + vid + '" title="Remove variable"><span class="glyphicon glyphicon-trash"></span></button>'
	+ '</div>'
	+ '</td>'
	+ '</tr>'
	+ '';
    };
    
    this.dummyRow = function() {
	return '<tr class="dummyRow" style="display: none;"><td colspan="3"></td></tr>';
    };
}

$(function() {
    $("#insertVarsHere").selectable({
	cancel: ".handle, .form-control, button"
    });
    $("#insertVarsHere").sortable({
	handle: ".handle",
	placeholder: "ui-state-highlight"
    });
    $("#btnAddVar").click(function() {
	varForm.addRow($(".varRow").last());
    });
    $("#btnAddVar").draggable({
	helper: "clone",
	revert: "invalid",
	start: function( event, ui ) {
	    $(".dummyRow").show();
	},
	stop: function( event, ui ) {
	    $(".dummyRow").hide();
	}
    });
    varForm.updatePlaceholders();
    
    // bind key strokes
    $.keyStroke( 38, function(){ varForm.moveSelectionUp("select"); });
    $.keyStroke( 38, { modKeys: ['altKey'] }, function(){ varForm.moveSelectionUp("move"); });
    $.keyStroke( 38, { modKeys: ['shiftKey'] }, function(){ varForm.moveSelectionUp("extend"); });
    $.keyStroke( 40, function(){ varForm.moveSelectionDown("select"); });
    $.keyStroke( 40, { modKeys: ['altKey'] }, function(){ varForm.moveSelectionDown("move"); });
    $.keyStroke( 40, { modKeys: ['shiftKey'] }, function(){ varForm.moveSelectionDown("extend"); });
    $.keyStroke( 46, function(){ varForm.performActionOnSelection("remove"); }); // del
    $.keyStroke( 27, function(){ varForm.performActionOnSelection("cancel"); }); // esc
    $.keyStroke( 13, function(){ varForm.performActionOnSelection("check"); }); // return
});