/**
 * jQUERY - MANAGE EVENT HANDLERS
 */
// FIXME: find something that works!
//$(window).bind('beforeunload', function() {
//    //warn before page is left, to prevent data loss
//    return 'Are you sure you want to leave? Unsaved changes get lost!';
//});

$("#addRegisterCheck").click(function() {
    dataModal.setPlaceholder();
});

$(".btn-size").click(function() {
    // update maximum list value
    var listSize = $(this).text();
    $("#addListMaxValue").text(listSize - 1);
});

$(".activate-input").click(function() {
    var elem = $("#" + $(this).prop("value"));
    if ($(this).prop("checked")) {
	elem.prop("disabled", false);
	elem.focus();
    } else {
	elem.prop("disabled", true);
	elem.prop("value", "");
    }
});

$("#btn-addVariable").click(function() {
    // prepare modal for adding
    dataModal.themeAdd();
    dataModal.show();
});

$("#btn-addInstruction").click(function() {
    // prepare modal for adding
    instModal.themeAdd();
    instModal.show();
});

$("#btn-addLine").click(function() {
    // prepare modal for adding
    scriptModal.themeAdd();
    scriptModal.show();
});

/** The default validator for input values. */
var valid = new Validator();
/** Provides a set of HTML templates for enhancing site content. */
var err = new ErrorTemplate();

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

    /** This is the form's input field that is to be highlighted in case of an error. */
    this.inputField;
    /** This is the area in the form where the error message should be shown. */
    this.errorLoc;
    
    this.checkExists = function(id) {
	if (id == "" || typeof id == "undefined") {
	    this.inputField.addClass("has-error");
	    this.errorLoc.append(err.error("No valid variable/intruction selected."));
	    return false;
	}
	return true;
    };
    
    this.checkIndex = function(value, maxIndex) {
	if (value.search(/^\d+$/) != 0) {
	    this.inputField.addClass("has-error");
	    this.errorLoc.append(err.error("Index '" + value + "' is not valid. Must be integer."));
	    return false;
	} else if(value > maxIndex) {
	    this.inputField.addClass("has-error");
	    this.errorLoc.append(err.error("Index '" + value + "' is out of range (max. " + maxIndex + ")"));
	    return false;
	}
	return true;
    };
    
    /**
     * This method checks whether the entered name is valid or not. It moreover
     * checks if the name does already exist.
     * 
     * @param name
     *                The entered string for the variable's name.
     * 
     * @param ignoreName
     *                Optionally a name that is ignored by the
     *                name-duplication-check.
     */
    this.checkName = function(name, ignoreName) {
	var msg = new Array();
	// check if name exists
	for (var i = 0; i < vars.size(); i++) {
	    var curName = vars.get(i).name;
	    if (curName != ignoreName && name == curName) {
		msg.push("Name '" + name + "' does already exist!");
		break;
	    }
	}

	// check validity of name
	if (name.search(/^[a-zA-Z]\w*$/) == -1) // \w = [A-Za-z0-9_]
	    msg.push("Name '" + name + "' is not valid. Allowed characters: [A-Za-z0-9_], starting with letter.");

	if (msg.length > 0) {
	    this.inputField.addClass("has-error");
	    while (msg.length > 0) {
		this.errorLoc.append(err.error(msg.pop()));
	    }
	    return false;
	}
	return true;
    };

    /**
     * This method checks whether the entered value is valid or not.
     * 
     * @param value
     *                The entered string for the variable's value.
     */
    this.checkValue = function(value) {
	// check for integer
	var check = value.search(/^-?\d+$/) == 0; // \d = [0-9]

	// check for string
	// check |= value.search(/^["'][A-ZÄÖÜa-zäöü0-9_ ]*["']$/) == 0;
	
	if (!check) {
	    this.inputField.addClass("has-error");
	    this.errorLoc.append(err.error("Value '" + value + "' is not valid. Allowed: integers"));
	    return false;
	}
	return true;
    };

    /**
     * Dependent on whether a register or a list is currently checked, the HTML
     * elements in the page differ. Therefore there has to be a mode for every
     * different variable, providing information on where input is taken
     * from and errors are written to.
     */
    this.target = function(inputField, errorLocation) {
	this.inputField = $(inputField);
	this.errorLoc = $(errorLocation);
    };
}

function ErrorTemplate() {
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