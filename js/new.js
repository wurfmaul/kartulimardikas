/**
 * jQUERY - MANAGE EVENT HANDLERS
 */
// FIXME: find something that works!
//$(window).bind('beforeunload', function() {
//    //warn before page is left, to prevent data loss
//    return 'Are you sure you want to leave? Unsaved changes get lost!';
//});

$("#addRegisterCheck").click(function() {
    modal.setDisability();
});

$(".btn-size").click(function() {
    // update maximum list value
    var listSize = $(this).text();
    $("#addListMaxValue").text(listSize - 1);
});

$("#btn-addDataStructure").click(function() {
    // prepare modal for adding
    modal.themeAdd();
    modal.show();
});

/**
 * ADD NEW DATA STRUCTURES
 */

/** Represents delimiter of list elements in customized view. */
var DELIM = ';';
/** Specifies the site, where data structures are to place. */
var SITE = $("#placeStructuresHere");

/** The main window for adding/editing data structures. */
var modal = new DataStructureModal();
/** The default validator for input values. */
var valid = new Validator();
/** Provides a set of HTML templates for enhancing site content. */
var template = new Template();
/** Represents the collection of existing data structures */
var struc = new Structure();
/** Provides an interface for adding/editing registers. */
var registers = new Register();
/** Provides an interface for adding/editing lists. */
var lists = new List();

/**
 * Redraw the table of data structures. Each element is displayed in a single
 * table row.
 */
function reDraw() {
    // compute a table row for every data structure
    var html = "";
    for (var i = 0; i < struc.size(); i++) {
	var name = struc.getName(i);
	var value = struc.getValue(i);
	if($.isArray(value))
	    html += template.listCells(name, value);
	else
	    html += template.registerCell(name, value);
    }
    // write the content to the page's HTML content.
    SITE.html(html);

    // add remove functionality to button
    $(".data-remove").click(function() {
	removeStructure($(this).prop("value"));
    });

    // add edit functionality to button
    $(".data-edit").click(function() {
	modal.themeEdit($(this).prop("value"));
	modal.show();
    });
}

/**
 * Remove data structure, identified by unique name
 * 
 * @param name Identifier of element.
 */
function removeStructure(name) {
    struc.remove(name);
    reDraw();
}

/**
 * This class provides an interface for operations on registers. When submitting
 * the main add/edit form, the actions are evaluated here. The values are read
 * from the HTML content. The data is checked and written to the collection of
 * data structures.
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
    this.DEFAULTINIT = struc.UNINITIALIZED;
    /** The default value for a register's value. */
    this.DEFAULTVALUE = "";

    /** Add the register that was specified by the HTML add form. */
    this.add = function() {
	// check input fields
	if(this.check(null)) {
	    // add register to internal structure
	    struc.add(this.name, this.value, this.init);
	    // redraw data table
	    reDraw();
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
	    struc.edit(oldName, this.name, this.value, this.init);
	    // redraw data table
	    reDraw();
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
	valid.target(valid.REGISTER);

	// get name from input field
	this.name = $("#addRegisterName").prop("value");
	var check = valid.checkName(this.name, oldName);

	// retrieve value
	this.value = "?";
	this.init = this.DEFAULTINIT;
	if ($("#addRegisterCheck").prop("checked")) {
	    this.value = $("#addRegisterValue").prop("value");
	    check = valid.checkValue(this.value) && check;
	    this.init = struc.CUSTOMIZED;
	}

	if(check) {
	    // all checks passed
	    $(".has-error").removeClass("has-error");
	    $(".alert").alert('close');
	    modal.hide();
	}

	return check;
    };
}

/**
 * This class provides an interface for operations on lists. When submitting the
 * main add/edit form, the actions are evaluated here. The values are read from
 * the HTML content. The data is checked and written to the collection of data
 * structures.
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
    this.DEFAULTINIT = struc.UNINITIALIZED;
    /** The default value of a list's values. */
    this.DEFAULTVALUES = "";

    /** Add the list that was specified by the HTML add form. */
    this.add = function() {
	// check input fields
	if(this.check(null)) {
	    // add register to internal structure
	    struc.add(this.name, this.values, this.init);
	    // redraw data table
	    reDraw();
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
	    struc.edit(oldName, this.name, this.values, this.init);
	    // redraw data table
	    reDraw();
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
	valid.target(valid.LIST);

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
	    this.init = struc.UNINITIALIZED;
	} else if ($("#addListRandomized").hasClass("active")) {
	    for(var i = 0; i < this.size; i++) {
		this.values.push(i);
	    }
	    shuffle(this.values);
	    this.init = struc.RANDOMIZED;
	} else if ($("#addListCustomized").hasClass("active")) {
	    var tokens = $("#addListValues").prop("value").split(DELIM);
	    if (tokens.length < this.size) {
		$("#alert-list").append(template.error("Too few values for list!"));
		$("#addListValues").addClass("has-error");
		return;
	    } else if(tokens.length > this.size) {
		$("#alert-list").append(template.warning("Too many values for list! Values were truncated!"));
	    }

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
	    this.init = struc.CUSTOMIZED;
	} else {
	    $("#alert-list").append(template.error("List has to be initialized!"));
	}

	if(check) {
	    // all checks passed
	    $(".has-error").removeClass("has-error");
	    $(".alert").alert('close');
	    modal.hide();
	}
	return check;
    };

}

/**
 * This class represents the main data-structure modification form. In order to
 * reduce traffic, the form for editing and adding is the same one but with
 * different "themes". The labels and functionality of certain elements is
 * replaced dynamically according to the given purpose.
 */
function DataStructureModal() {

    /** Hides the window. */
    this.hide = function() {
	$("#addStructureModal").modal('hide');
    };

    /** Shows the window. */
    this.show = function() {
	$("#addStructureModal").modal('show');
    };

    /**
     * This is the theme for editing data structures. Given a valid name, the
     * values of the element that is to be edited are used as default values for
     * the modification form.
     * 
     * @param name
     *                The unique identifier of the data structure.
     */
    this.themeEdit = function(name) {
	// tab rsgister/list
	if ($.isArray(struc.getValueByName(name)))
	    $('#addStructureTab a[href="#add-list"]').tab('show');
	else
	    $('#addStructureTab a[href="#add-register"]').tab('show');

	var init = struc.getInitByName(name);
	var value = struc.toString(name);

	$("#addStructureModalLabel").text("Edit data structure");

	// REGISTER TAB
	// setup values
	$("#addRegisterName").prop("value", name);
	if (init == struc.CUSTOMIZED) {
	    $("#addRegisterCheck").prop("checked", true);
	    $("#addRegisterValue").prop("value", value);
	} else {
	    $("#addRegisterCheck").prop("checked", false);
	}
	this.setDisability();

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
	var size = struc.getValueByName(name).length;
	$(".btn-size").removeClass("active");
	$("#addListSizeBtn" + size).addClass("active");

	if(init == struc.UNINITIALIZED) {
	    $('#addListInitTab a[href="#addListUninitialized"]').tab('show');
	} else if(init == struc.RANDOMIZED) {
	    $('#addListInitTab a[href="#addListRandomized"]').tab('show');
	} else if(init == struc.CUSTOMIZED) {
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
     * This is the theme for adding new data structures. The form's input fields
     * are initialized by default values.
     */
    this.themeAdd = function() {
	// default tab: very left
	$('#addStructureTab a:first').tab('show');

	$("#addStructureModalLabel").text("Add new data structure");
	// REGISTER TAB
	// setup default values
	$("#addRegisterName").prop("value", registers.DEFAULTNAME);
	$("#addRegisterCheck").prop("checked", registers.DEFAULTINIT == struc.CUSTOMIZED);
	this.setDisability();
	$("#addRegisterValue").prop("value", registers.DEFAULTVALUE);
	// setup labels
	$("#addRegisterSubmit").text("Add register");
	$("#addRegisterSubmit").off("click");
	$("#addRegisterSubmit").click(function() {
	    registers.add();
	});

	// LIST TAB
	// setup default values
	$("#addListName").prop("value", lists.DEFAULTNAME);
	$(".btn-size").removeClass("active");
	$("#addListSizeBtn" + lists.DEFAULTSIZE).addClass("active");
	if(lists.DEFAULTINIT == struc.UNINITIALIZED) {
	    $('#addListInitTab a[href="#addListUninitialized"]').tab('show');
	} else if(lists.DEFAULTINIT == struc.RANDOMIZED) {
	    $('#addListInitTab a[href="#addListRandomized"]').tab('show');
	} else if(lists.DEFAULTINIT == struc.CUSTOMIZED) {
	    $('#addListInitTab a[href="#addListCustomized"]').tab('show');
	}
	$("#addListValues").prop("value", lists.DEFAULTVALUES);

	// setup labels
	$("#addListSubmit").text("Add List");
	$("#addListSubmit").off("click");
	$("#addListSubmit").click(function() {
	    lists.add();
	});
    };

    /**
     * Enable/disable the input field for new register value
     */
    this.setDisability = function() {
	var elem = $("#addRegisterValue");
	if ($("#addRegisterCheck").prop("checked")) {
	    elem.prop("disabled", false);
	    elem.attr("placeholder", "value");
	} else {
	    elem.prop("disabled", true);
	    elem.prop("value", "");
	    elem.attr("placeholder", "uninitialized");
	}
    };
}

/**
 * This class provides a validator for the client-provided text input. It offers
 * the possibillity to check the correctness of the input fields of the main
 * form.
 */
function Validator() {
    /** Enum value for registers (see this.target). */
    this.REGISTER = 0;
    /** Enum value for lists. */
    this.LIST = 1;

    /** This is the form's input field for the data structure's name. */
    this.nameField;
    /** This is the form's input field for the data structure's value. */
    this.valueField;
    /** This is the area in the form where the error message should be shown. */
    this.errorField;

    /**
     * This method checks whether the entered name is valid or not. It moreover
     * checks if the name does already exist.
     * 
     * @param name
     *                The entered string for the data structure's name.
     * 
     * @param ignoreName
     *                Optionally a name that is ignored by the
     *                name-duplication-check.
     */
    this.checkName = function(name, ignoreName) {
	var msg = new Array();
	// check if name exists
	for (var i = 0; i < struc.size(); i++) {
	    var curName = struc.getName(i);
	    if (curName != ignoreName && name == curName) {
		msg.push("Name '" + name + "' does already exist!");
		break;
	    }
	}

	// check validity of name
	if (name.search(/^[a-zA-Z]\w*$/) == -1) // \w = [A-Za-z0-9_]
	    msg.push("Name '" + name + "' is not valid. Allowed characters: [A-Za-z0-9_], starting with letter.");

	if (msg.length > 0) {
	    this.nameField.addClass("has-error");
	    while (msg.length > 0) {
		this.errorField.append(template.error(msg.pop()));
	    }
	    return false;
	}
	return true;
    };

    /**
     * This method checks whether the entered value is valid or not.
     * 
     * @param value
     *                The entered string for the data structure's value.
     */
    this.checkValue = function(value) {
	// check for integer
	var check = value.search(/^-?\d+$/) == 0; // \d = [0-9]

	// check for string
	// check |= value.search(/^["'][A-ZÄÖÜa-zäöü0-9_ ]*["']$/) == 0;
	
	if (!check) {
	    this.valueField.addClass("has-error");
	    this.errorField.append(template.error("Value '" + value + "' is not valid. Allowed: integers"));
	    return false;
	}
	return true;
    };

    /**
     * Dependent on whether a register or a list is currently checked, the HTML
     * elements in the page differ. Therefore there has to be a mode for every
     * different data structure, providing information on where input is taken
     * from and errors are written to.
     */
    this.target = function(mode) {
	switch (mode) {
	case this.REGISTER:
	    this.nameField = $("#addRegisterNameField");
	    this.valueField = $("#addRegisterValueField");
	    this.errorField = $("#alert-register");
	    break;

	case this.LIST:
	    this.nameField = $("#addListNameField");
	    this.valueField = $("#addListValuesField"); // FIXME does not work
	    this.errorField = $("#alert-list");
	    break;

	default:
	    console.log("No valid target chosen!");
	break;
	}
    };
}

/**
 * This class is the central collection of all available data structures. It is
 * simply an implementation of a collection of key-value pairs.
 */
function Structure() {
    /** Initialization method number 1: no initialization. */
    this.UNINITIALIZED = 0;
    /** Initialization method number 2: random initialization. */
    this.RANDOMIZED = 1;
    /** Initialization method number 3: custom initialization. */
    this.CUSTOMIZED = 2;

    /** The basic array to store all the values. */
    this.structures = new Array();

    /**
     * Adds a new data structure to the list.
     * 
     * @param name
     *                The name of the new data structure.
     * @param value
     *                The value of the new data structure.
     * @param init
     *                The initialization method of the new data structure.
     */
    this.add = function(name, value, init) {
	this.structures[this.size()] = new this.Data(name, value, init);
    };

    /**
     * Edits an existing data structure in the list.
     * 
     * @param oldName
     *                The name that was the data strucutre's unique identifier
     *                before editing.
     * @param newName
     *                The name that will be the data strucutre's unique
     *                identifier after editing.
     * @param newValue
     *                The value that is connected with the data structure.
     * @param init
     *                The initialization method that is connected to the data
     *                structure.
     * @returns True if the element was found and edited, false otherwise.
     */
    this.edit = function(oldName, newName, newValue, init) {
	for (var i = 0; i < this.size(); i++) {
	    if (this.structures[i].name == oldName) {
		this.structures[i] = new this.Data(newName, newValue, init);
		return true;
	    }
	}
	return false;
    };
    
    /**
     * This method finds the i<sup>th</sup> element and returns its
     * initialization method.
     */
    this.getInit = function(i) {
	return this.structures[i].init;
    };
    
    /**
     * Get the initialization method for a data structure, identified by a
     * given name.
     * 
     * @param name
     *                The data structure's name.
     */
    this.getInitByName = function(name) {
	for (var i = 0; i < this.size(); i++) {
	    if (name == this.structures[i].name) {
		return this.structures[i].init;
	    }
	}
	return -1;
    };

    /**
     * This method finds the i<sup>th</sup> element and returns its name.
     */
    this.getName = function(i) {
	return this.structures[i].name;
    };

    /**
     * This method finds the i<sup>th</sup> element and returns its value.
     */
    this.getValue = function(i) {
	return this.structures[i].value;
    };
    
    /**
     * Get the value for a data structure, identified bygiven name.
     * 
     * @param name
     *                The data structure's name.
     */
    this.getValueByName = function(name) {
	for (var i = 0; i < this.size(); i++) {
	    if (this.structures[i].name == name) {
		return this.structures[i].value;
	    }
	}
	console.log("Name '" + name + "' could not be found!");
    };

    /**
     * This method removes the element, given its name.
     * 
     * @param name
     *                The name of the data structure that is to be deleted.
     * @returns True if the element was found and removed, false otherwise.
     */
    this.remove = function(name) {
	for (var i = 0; i < this.size(); i++) {
	    if (name == this.structures[i].name) {
		this.structures.splice(i, 1);
		return true;
	    }
	}
	return false;
    };

    /**
     * Returns the number of currently available data structures.
     */
    this.size = function() {
	return this.structures.length;
    };

    /**
     * Returns a string representation of the data structure's value.
     * 
     * @param name
     *                The name of the data structure.
     */
    this.toString = function(name) {
	var value = this.getValueByName(name);
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
    this.Data = function(name, value, init) {
	this.name = name;
	this.value = value;
	this.init = init;
    };
}

/**
 * This class provides several templates for HTML content that is inserted to
 * the page dynamically.
 */
function Template() {

    /**
     * Returns an HTML representation of one single value cell. A list consists
     * of several such cells.
     * 
     * @param name
     *                The name of the data structure
     * @param value
     *                The value of the data structure.
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
     *                The name of the data structure.
     * @param cells
     *                HTML code for the memory content of the data structure.
     */
    this.structureRow = function(name, cells) {
	return '<tr>' 
	+ '	<td><code>' + name + '</code></td>' 
	+ '	<td><div class="btn-group">'
	+ cells 
	+ '	</div></td>'
	+ '	<td>' 
	+ '		<button type="button" class="btn btn-default data-edit" title="edit register" value="' + name + '">'
	+ '			<span class="glyphicon glyphicon-pencil"></span>' 
	+ '		</button>&nbsp;'
	+ '		<button type="button" class="btn btn-default data-remove" title="remove register" value="' + name + '">'
	+ '			<span class="glyphicon glyphicon-remove"></span>' 
	+ '		</button>' 
	+ '	</td>' 
	+ '<tr>';
    };

    /**
     * Returns an HTML representation of an error message.
     * 
     * @param message
     *                The message that is to be displayed.
     */
    this.error = function(message) {
	return '<div class="alert alert-danger alert-dismissable">'
	+ '	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
	+ '	<strong>Error!</strong> ' + message 
	+ '</div>';
    };
    
    /**
     * Returns an HTML representation of a warning message.
     * 
     * @param message
     *                The message that is to be displayed.
     */
    this.warning = function(message) {
	return '<div class="alert alert-warning alert-dismissable">'
	+ '	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
	+ '	<strong>Warning!</strong> ' + message 
	+ '</div>';
    };
}