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

$(".panel-heading").click(function() {
    $(this).find("span").toggleClass("glyphicon-chevron-right glyphicon-chevron-down");
});

$("#btn-addVar").click(function() {
    $(this).hide();
    $("#placeVariablesHere").show("slow");
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
	    varForm.themeShow(this.id);
	}
    };

    this.edit = function(id, init) {
	this.id = id;
	this.init = init;
	var oldName = vars.getById(id).name;
	// check input fields
	if(this.check(oldName)) {
	    // add register to internal structure
	    vars.edit(this.id, this.name, this.value, this.init);
	    varForm.themeShow(this.id);
	}
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
	    varForm.themeShow(this.id);
	}
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
	    varForm.themeShow(this.id);
	}
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
    this.noOfRows = 0;
    
    this.addRowBelow = function(id) {
	if (id == -1) { // first row
	    $("#placeVariablesHere").append(varTemplate.varRow(maxId));
	} else {
	    $("#var-" + id).after(varTemplate.varRow(maxId));
	}
	this.themeEdit(maxId++);
	this.noOfRows++;
    };

    this.removeRow = function(id) {
	$("#var-" + id).hide("slow");
	$("#var-" + id).remove();
	if (--this.noOfRows == 0){
	    this.addRowBelow(-1);
	}
	
    };
    
    this.moveRowUp = function(id) {
	var curRow = $("#var-" + id);
	if (curRow.prev().length != 0) {
	    curRow.prev().before(curRow.clone());
	    curRow.remove();
	    this.updateActionHandlers(id);
	}
    };
    
    this.moveRowDown = function(id) {
	var curRow = $("#var-" + id);
	if (curRow.next().length != 0) {
	    curRow.next().after(curRow.clone());
	    curRow.remove();
	    this.updateActionHandlers(id);
	}
    };

    this.checkAndCreateVar = function(id) {
	switch($("#slct-var-" + id + "-init").val()) {
	case "elem-?":
	    elementFactory.create(id, vars.UNINITIALIZED);
	    break;
	case "elem-value":
	    elementFactory.create(id, vars.CUSTOMIZED);
	    break;
	case "array-?":
	    arrayfactory.create(id, vars.UNINITIALIZED);
	    break;
	case "array-random":
	    arrayfactory.create(id, vars.RANDOMIZED);
	    break;
	case "array-custom":
	    arrayfactory.create(id, vars.CUSTOMIZED);
	    break;
	}
    };
    
    this.checkAndEditVar = function(id) {
	switch($("#slct-var-" + id + "-init").val()) {
	case "elem-?":
	    elementFactory.edit(id, vars.UNINITIALIZED);
	    break;
	case "elem-value":
	    elementFactory.edit(id, vars.CUSTOMIZED);
	    break;
	case "array-?":
	    arrayfactory.edit(id, vars.UNINITIALIZED);
	    break;
	case "array-random":
	    arrayfactory.edit(id, vars.RANDOMIZED);
	    break;
	case "array-custom":
	    arrayfactory.edit(id, vars.CUSTOMIZED);
	    break;
	}
    };
    
    this.themeShow = function(id) {
	var leftCell = $("#var-" + id + "-left");
	var rightCell = $("#var-" + id + "-right");
	
	var variable = vars.getById(id);
	leftCell.html(varTemplate.elementCell(variable.name, variable.value));
	rightCell.html(varTemplate.buttonsShow(id));
	this.updateActionHandlers(id);
    };
    
    this.themeEdit = function(id) {
	var leftCell = $("#var-" + id + "-left");
	var rightCell = $("#var-" + id + "-right");
	
	leftCell.html(varTemplate.inputEdit(id));
	rightCell.html(varTemplate.buttonsEdit(id));
	this.updateActionHandlers(id);
    };
    
    this.updateActionHandlers = function(id) {
	var curAddButton = $("#btn-var-" + id + "-add");
	var curRemoveButton = $("#btn-var-" + id + "-remove");
	var curEditButton = $("#btn-var-" + id + "-edit");
	var curCheckButton = $("#btn-var-" + id + "-check");
	var curMoveUpButton = $("#btn-var-" + id + "-up");
	var curMoveDownButton = $("#btn-var-" + id + "-down");
	var curValueSelect = $("#slct-var-" + id + "-init");
	
	curAddButton.off("click");
	curRemoveButton.off("click");
	curEditButton.off("click");
	curCheckButton.off("click");
	curMoveUpButton.off("click");
	curMoveDownButton.off("click");
	curValueSelect.off("click");
	
	curAddButton.click(function() {
	    varForm.addRowBelow(id); 
	});

	curRemoveButton.click(function() {
	    vars.removeById(id);
	    varForm.removeRow(id); 
	});

	curEditButton.click(function() {
	    varForm.themeEdit(id);
	});

	if (vars.findId(id) != -1) {
	    // var already exists
	    curCheckButton.click(function() {
		varForm.checkAndEditVar(id);
	    });
	} else {
	    curCheckButton.click(function() {
		varForm.checkAndCreateVar(id);
	    });
	}
	

	curMoveUpButton.click(function() {
	    varForm.moveRowUp(id);
	});
	
	curMoveDownButton.click(function() {
	    varForm.moveRowDown(id);
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

    this.elementCell = function(name, value) {
	return '<code class="cell">' + name + ' = ' + value + '</code>';
    };
    
    this.varRow = function(id) {
	return '<tr id="var-' + id + '">'
	+ '<td style="vertical-align: middle;" id="var-' + id + '-left"></td>'
	+ '<td style="width: 95pt; text-align: center;" id="var-' + id + '-right"></td>'
	+ '</tr>';
    };
    
    this.inputEdit = function(id) {
	var variable = vars.getById(id);

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
		if (vars.isArrayById(id)) {
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
		if (vars.isArrayById(id))
		    arrayCustomSelected = sel;
		else
		    elemValueSelected = sel;
		valueInvisible = "";
		value = variable.value;
		break;
	    }
	}
	
	return ''
	+ '<div class="col-xs-3">'
	+ '	<div class="form-group" id="var-' + id + '-nameField" style="margin-bottom:0px">'
	+ '		<label class="sr-only" for="var-' + id + '-name">Variable name</label>'
	+ '		<input type="text" class="form-control" id="var-' + id + '-name" value="' + name + '" placeholder="name">'
	+ '	</div>'
	+ '</div>'
	+ '<div class="col-xs-2" style="text-align: center;">'
	+ '	<div class="cell"><code>=</code></div>'
	+ '</div>'
	+ '<div class="col-xs-3">'
	+ '	<div class="form-group" style="margin-bottom:0px">'
	+ '		<label class="sr-only" for="var-' + id + '-init">Initialization</label>'
	+ '		<select class="form-control" id="slct-var-' + id + '-init" '
	+ '			data-options=\'{"targetVal":"#var-' + id + '-valueField", "targetSize":"#var-' + id + '-sizeField"}\'>'
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
	+ '	<div class="form-group" id="var-' + id + '-valueField" style="margin-left: 0px; margin-bottom:0px;' + valueInvisible + '">'
	+ '		<label class="sr-only" for="var-' + id + '-value">Initial value</label>'
	+ '		<input type="text" class="form-control" id="var-' + id + '-value" value="' + value + '" placeholder="value">'
	+ '	</div>'
	+ '	<div class="form-group" id="var-' + id + '-sizeField" style="margin-left: 0px; margin-bottom:0px;' + sizeInvisible + '">'
	+ '		<label class="sr-only" for="var-' + id + '-size">Array size</label>'
	+ '		<select class="form-control" id="var-' + id + '-size">'
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
	+ '';
    };
    
    this.buttonsEdit = function(id) {
	return '<div class="btn-group btn-group-xs">'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-check" value="' + id + '"><span class="glyphicon glyphicon-ok"></span></button>'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-up" value="' + id + '"><span class="glyphicon glyphicon-arrow-up"></span></button>'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-down" value="' + id + '"><span class="glyphicon glyphicon-arrow-down"></span></button>'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-add" value="' + id + '"><span class="glyphicon glyphicon-plus"></span></button>'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-remove" value="' + id + '"><span class="glyphicon glyphicon-minus"></span></button>'
	+ '</div>';
    };
    
    this.buttonsShow = function(id) {
	return '<div class="btn-group btn-group-xs">'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-edit" value="' + id + '"><span class="glyphicon glyphicon-pencil"></span></button>'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-up" value="' + id + '"><span class="glyphicon glyphicon-arrow-up"></span></button>'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-down" value="' + id + '"><span class="glyphicon glyphicon-arrow-down"></span></button>'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-add" value="' + id + '"><span class="glyphicon glyphicon-plus"></span></button>'
	+ '<button type="button" class="btn btn-default" id="btn-var-' + id + '-remove" value="' + id + '"><span class="glyphicon glyphicon-minus"></span></button>'
	+ '</div>';
    };
}