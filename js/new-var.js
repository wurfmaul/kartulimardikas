/**
 * This file contains every functionality concerning variable generation 
 * and management.
 */

/** Represents delimiter of list elements in customized view. */
var DELIM = ';';
/** Specifies the site, where variables are to place. */
var VARSITE = $("#placeVariablesHere");
/** The main window for adding/editing variables. */
var dataModal = new VariableModal();
/** Represents the collection of existing variables */
var vars = new Variables();
/** The main window for adding/editing instructions. */
/** Provides an interface for adding/editing registers. */
var registers = new Register();
/** Provides an interface for adding/editing lists. */
var lists = new List();

var template = new VarTemplate();

/**
 * Redraw the table of variables. Each element is displayed in a single
 * table row.
 */
function redrawVars() {
    // compute a table row for every variable
    if (vars.size() > 0) {
	var html = "";
	for (var i = 0; i < vars.size(); i++) {
	    var name = vars.get(i).name;
	    var value = vars.get(i).value;
	    if(vars.isArray(i))
		html += template.listCells(name, value);
	    else
		html += template.registerCell(name, value);
	}
	// write the content to the page's HTML content.
	VARSITE.html(html);
	// make table visible
	$("#varTable").show("slow");
	// add remove functionality to button
	$(".data-remove").click(function() {
	    vars.remove($(this).prop("value"));
	    redrawVars();
	});
	// add edit functionality to button
	$(".data-edit").click(function() {
	    dataModal.themeEdit($(this).prop("value"));
	    dataModal.show();
	});
    } else {
	VARSITE.html("");
	$("#varTable").hide("slow");
    }
    redrawInst();
}

/**
 * This class provides an interface for operations on registers. When submitting
 * the main add/edit form, the actions are evaluated here. The values are read
 * from the HTML content. The data is checked and written to the collection of
 * variables.
 */
function Register() {
    /** The name of the currently edited register. */
    this.name;
    /** The initialization method of the currently edited register. */
    this.init;
    /** The value of the currently edited register. */
    this.value;

    /** The default value of a register's name. */
    this.DEFAULTNAME = "";
    /** The default method for a register's initialization. */
    this.DEFAULTINIT = vars.UNINITIALIZED;
    /** The default value for a register's value. */
    this.DEFAULTVALUE = "";

    /** Add the register that was specified by the HTML add form. */
    this.add = function() {
	// check input fields
	if(this.check(null)) {
	    // add register to internal structure
	    vars.add(this.name, this.value, this.init);
	    // redraw data table
	    redrawVars();
	}
    };

    /**
     * Edit the register that was specified by the HTML add form.
     * 
     * @param oldName
     *                The unique name of the register that is to be edited.
     */
    this.edit = function(oldName) {
	// check input fields
	if(this.check(oldName)) {
	    // add register to internal structure
	    vars.edit(oldName, this.name, this.value, this.init);
	    // redraw data table
	    redrawVars();
	}
    };

    /**
     * Check the data that was provided by the HTML add/edit form by the
     * validator.
     * 
     * @param oldName
     *                The unique name of the register that is to be checked.
     */
    this.check = function(oldName) {
	// tell the validator that we are dealing with registers
	valid.target("#addRegisterNameField", "#alert-register");
	
	// clear all errors
	$(".has-error").removeClass("has-error");
	$(".alert").alert('close');

	// get name from input field
	this.name = $("#addRegisterName").prop("value");
	var check = valid.checkName(this.name, oldName);

	// retrieve value
	this.value = "?";
	this.init = this.DEFAULTINIT;
	if ($("#addRegisterCheck").prop("checked")) {
	    this.value = $("#addRegisterValue").prop("value");
	    this.init = vars.CUSTOMIZED;
	    valid.target("#addRegisterValueField", "#alert-register");
	    check = valid.checkValue(this.value) && check;
	}

	if(check) {
	    // all checks passed
	    dataModal.hide();
	}

	return check;
    };
}

/**
 * This class provides an interface for operations on lists. When submitting the
 * main add/edit form, the actions are evaluated here. The values are read from
 * the HTML content. The data is checked and written to the collection of variables.
 */
function List() {
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
    this.add = function() {
	// check input fields
	if(this.check(null)) {
	    // add register to internal structure
	    vars.add(this.name, this.values, this.init);
	    // redraw data table
	    redrawVars();
	}
    };

    /**
     * Edit the list that was specified by the HTML add form.
     * 
     * @param oldName
     *                The unique name of the list that is to be edited.
     */
    this.edit = function(oldName) {
	// check input fields
	if(this.check(oldName)) {
	    // add register to internal structure
	    vars.edit(oldName, this.name, this.values, this.init);
	    // redraw data table
	    redrawVars();
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
	valid.target("#addListNameField", "#alert-list");
	
	// clear errors
	$(".has-error").removeClass("has-error");
	$(".alert").alert('close');

	// get name from input field
	this.name = $("#addListName").prop("value");
	var check = valid.checkName(this.name, oldName);

	// retrieve size
	this.size = $(".btn-size.active").text();

	// retrieve values
	this.values = new Array();
	if ($("#addListUninitialized").hasClass("active")){
	    for(var i = 0; i < this.size; i++) {
		this.values.push("?");
	    }
	    this.init = vars.UNINITIALIZED;
	} else if ($("#addListRandomized").hasClass("active")) {
	    for(var i = 0; i < this.size; i++) {
		this.values.push(i);
	    }
	    shuffle(this.values);
	    this.init = vars.RANDOMIZED;
	} else if ($("#addListCustomized").hasClass("active")) {
	    var tokens = $("#addListValues").prop("value").split(DELIM);
	    if (tokens.length < this.size) {
		$("#alert-list").append(err.error("Too few values for list!"));
		$("#addListValues").addClass("has-error");
		return;
	    } else if(tokens.length > this.size) {
		$("#alert-list").append(err.warning("Too many values for list! Values were truncated!"));
	    }

	    for(var i = 0; i < this.size; i++) {
		// trim
		var value = tokens[i].replace(/ /, "");
		// check value
		valid.target("#addListValuesField", "#alert-list");
		if (valid.checkValue(value)) {
		    this.values[i] = value;
		} else {
		    this.values[i] = "?";
		    check = false;
		}
	    }
	    this.init = vars.CUSTOMIZED;
	} else {
	    $("#alert-list").append(err.error("List has to be initialized!"));
	}

	if(check) {
	    // all checks passed
	    dataModal.hide();
	}
	return check;
    };

}

/**
 * This class represents the main variable modification form. In order to
 * reduce traffic, the form for editing and adding is the same one but with
 * different "themes". The labels and functionality of certain elements is
 * replaced dynamically according to the given purpose.
 */
function VariableModal() {

    /** Hides the window. */
    this.hide = function() {
	$("#addVariableModal").modal('hide');
    };

    /** Shows the window. */
    this.show = function() {
	$("#addVariableModal").modal('show');
    };

    /**
     * This is the theme for adding new variables. The form's input fields
     * are initialized by default values.
     */
    this.themeAdd = function() {
	// default tab: very left
	$('#addVariableTab a:first').tab('show');
	$("#addVariableModalLabel").text("Add new variable");
	
	// REGISTER TAB
	// setup default values
	$("#addRegisterName").prop("value", registers.DEFAULTNAME);
	$("#addRegisterCheck").prop("checked", registers.DEFAULTINIT == vars.CUSTOMIZED);
	this.setPlaceholder();
	$("#addRegisterValue").prop("value", registers.DEFAULTVALUE);
	// setup labels
	var btnAdd = $("#addRegisterSubmit");
	btnAdd.text("Add register");
	btnAdd.off("click");
	btnAdd.click(function() {
	    registers.add();
	});

	// LIST TAB
	// setup default values
	$("#addListName").prop("value", lists.DEFAULTNAME);
	$(".btn-size").removeClass("active");
	$("#addListSizeBtn" + lists.DEFAULTSIZE).addClass("active");
	if(lists.DEFAULTINIT == vars.UNINITIALIZED) {
	    $('#addListInitTab a[href="#addListUninitialized"]').tab('show');
	} else if(lists.DEFAULTINIT == vars.RANDOMIZED) {
	    $('#addListInitTab a[href="#addListRandomized"]').tab('show');
	} else if(lists.DEFAULTINIT == vars.CUSTOMIZED) {
	    $('#addListInitTab a[href="#addListCustomized"]').tab('show');
	}
	$("#addListValues").prop("value", lists.DEFAULTVALUES);
	// setup labels
	btnAdd = $("#addListSubmit");
	btnAdd.text("Add List");
	btnAdd.off("click");
	btnAdd.click(function() {
	    lists.add();
	});
    };
    
    /**
     * This is the theme for editing variables. Given a valid name, the
     * values of the element that is to be edited are used as default values for
     * the modification form.
     * 
     * @param name
     *                The unique identifier of the variable.
     */
    this.themeEdit = function(name) {
	// tab rsgister/list
	if (vars.isArrayByName(name))
	    $('#addVariableTab a[href="#add-list"]').tab('show');
	else
	    $('#addVariableTab a[href="#add-register"]').tab('show');

	var init = vars.getByName(name).init;
	var value = vars.toString(name);

	$("#addVariableModalLabel").text("Edit variable");

	// REGISTER TAB
	// setup values
	$("#addRegisterName").prop("value", name);
	if (init == vars.CUSTOMIZED) {
	    $("#addRegisterCheck").prop("checked", true);
	    $("#addRegisterValue").prop("value", value);
	} else {
	    $("#addRegisterCheck").prop("checked", false);
	}
	this.setPlaceholder();

	// setup labels
	var btnRegSubmit = $("#addRegisterSubmit");
	btnRegSubmit.text("Save changes");
	btnRegSubmit.off("click");
	btnRegSubmit.click(function() {
	    registers.edit(name);
	});

	// LIST TAB
	// setup values
	$("#addListName").prop("value", name);
	var size = vars.getByName(name).value.length;
	if(size == 1) // if not a list
	    size = lists.DEFAULTSIZE;
	$(".btn-size").removeClass("active");
	$("#addListSizeBtn" + size).addClass("active");
	
	if(init == vars.UNINITIALIZED) {
	    $('#addListInitTab a[href="#addListUninitialized"]').tab('show');
	} else if(init == vars.RANDOMIZED) {
	    $('#addListInitTab a[href="#addListRandomized"]').tab('show');
	} else if(init == vars.CUSTOMIZED) {
	    $('#addListInitTab a[href="#addListCustomized"]').tab('show');
	    $("#addListValues").prop("value", value);
	} else {
	    console.log("init mode not yet implemented: " + init);
	}
	// setup labels
	var btnListSubmit = $("#addListSubmit");
	btnListSubmit.text("Save changes");
	btnListSubmit.off("click");
	btnListSubmit.click(function() {
	    lists.edit(name);
	});
    };

    /**
     * Enable/disable the input field for new register value
     */
    this.setPlaceholder = function() {
	var elem = $("#addRegisterValue");
	if ($("#addRegisterCheck").prop("checked")) {
	    elem.attr("placeholder", "value");
	} else {
	    elem.attr("placeholder", "uninitialized");
	}
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

    /**
     * Adds a new variable to the list.
     * 
     * @param name
     *                The name of the new variable.
     * @param value
     *                The value of the new variable.
     * @param init
     *                The initialization method of the new variable.
     */
    this.add = function(name, value, init) {
	this.vars.push(new this.Data(this.maxId++, name, value, init));
    };

    /**
     * Edits an existing variable in the list.
     * 
     * @param oldName
     *                The name that was the data strucutre's unique identifier
     *                before editing.
     * @param newName
     *                The name that will be the data strucutre's unique
     *                identifier after editing.
     * @param newValue
     *                The value that is connected with the variable.
     * @param init
     *                The initialization method that is connected to the data
     *                structure.
     */
    this.edit = function(oldName, newName, newValue, init) {
	var i = this.find(oldName);
	var id = this.get(i).id;
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

    /**
     * This method removes the element, given its name.
     * 
     * @param name
     *                The name of the variable that is to be deleted.
     */
    this.remove = function(name) {
	this.vars.splice(this.find(name), 1);
    };

    /**
     * Returns the number of currently available variables.
     */
    this.size = function() {
	return this.vars.length;
    };

    /**
     * Returns a string representation of the variable's value.
     * 
     * @param name
     *                The name of the variable.
     */
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

    /**
     * This class represents an element in the vollection. Basically it is a
     * struct, combining name, value and initialization method in one class.
     */
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

    /**
     * Returns an HTML representation of one single value cell. A list consists
     * of several such cells.
     * 
     * @param name
     *                The name of the variable
     * @param value
     *                The value of the variable.
     */
    this.defaultCell = function(name, value) {
	return '<input type="button" class="btn btn-default" id="btn-' + name + '" disabled="disabled" value="' + value + '" />';
    };

    /**
     * Returns an HTML representation of a list, consisting of several cells.
     * 
     * @param name
     *                The list's name.
     * @param values
     *                An array of values representing the list's values.
     */
    this.listCells = function(name, values) {
	var cells = "";
	for (var i = 0; i < values.length; i++) {
	    cells += this.defaultCell(name + i, values[i]);
	}
	return this.structureRow(name, cells);
    };

    /**
     * Returns an HTML representation of a table row containing only one
     * register representation.
     * 
     * @param name
     *                The register's name.
     * @param value
     *                The register's value.
     */
    this.registerCell = function(name, value) {
	return this.structureRow(name, this.defaultCell(name, value));
    };

    /**
     * Returns an HTML representation of one data strucutre in form of a table
     * row.
     * 
     * @param name
     *                The name of the variable.
     * @param cells
     *                HTML code for the memory content of the variable.
     */
    this.structureRow = function(name, cells) {
	return '<tr>' 
	+ '	<td><code>' + name + '</code></td>' 
	+ '	<td style="border-right: none;"><div class="btn-group">'
	+ cells 
	+ '	</div></td>'
	+ '	<td style="border-left: none; text-align: right;">' 
	+ '		<button type="button" class="btn btn-default data-edit" title="edit register" value="' + name + '">'
	+ '			<span class="glyphicon glyphicon-pencil"></span>' 
	+ '		</button>&nbsp;'
	+ '		<button type="button" class="btn btn-default data-remove" title="remove register" value="' + name + '">'
	+ '			<span class="glyphicon glyphicon-remove"></span>' 
	+ '		</button>' 
	+ '	</td>' 
	+ '<tr>';
    };
}