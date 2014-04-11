/**
 * jQUERY - ADD EVENT HANDLERS
 */
var modal = new DataStructureModal();
var valid = new Validator();
var template = new Template();

// warn to prevent data loss
// $(window).bind('beforeunload', function() {
// return 'Are you sure you want to leave? Unsaved changes may get lost!';
// });
$("#addRegisterCheck").click(function() {
    modal.toggleDisability();
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

/** represents the collection and the current index of structures */
var struc = new Array();
/** specifies the site, where data structures are to place. */
var site = $("#placeStructuresHere");

/**
 * Redraw the table of data structures
 */
function reDraw() {
    var html = "";
    for (var i = 0; i < struc.length; i++) {
	var name = struc[i].name;
	var value = struc[i].value;
	html += template.registerRow(name, value);
    }
    site.html(html);

    // add remove functionality
    $(".data-remove").click(function() {
	removeStructure($(this).prop("value"));
    });

    // add edit functionality
    $(".data-edit").click(function() {
	modal.themeEdit($(this).prop("value"));
	modal.show();
    });
}

/**
 * Add new register
 */
function addRegister() {
    // get name from input field
    var name = $("#addRegisterName").prop("value");
    var check = valid.checkName(name);

    // retrieve value
    var value = "?";
    if ($("#addRegisterCheck").prop("checked")) {
	value = $("#addRegisterValue").prop("value");
	check = valid.checkValue(value) && check;
    }
    if(!check)
	return;
    
    // all checks passed
    $(".has-error").removeClass("has-error");
    modal.hide();


    // add register to internal structure
    struc[struc.length] = new Structure(name, value);

    // redraw data table
    reDraw();
}

function editRegister(oldName) {
    // get name from input field
    var name = $("#addRegisterName").prop("value");
    var check = valid.checkName(name, oldName);

    // retrieve value
    var value = "?";
    if ($("#addRegisterCheck").prop("checked")) {
	value = $("#addRegisterValue").prop("value");
	check = valid.checkValue(value) && check;
    }
    if (!check)
	return;
    
    // all checks passed
    $(".has-error").removeClass("has-error");
    modal.hide();

    // add register to internal structure
    for (var i = 0; i < struc.length; i++) {
	if (struc[i].name == oldName) {
	    struc[i] = new Structure(name, value);
	}
    }

    // redraw data table
    reDraw();
}

/**
 * Remove data structure, identified by unique name
 * 
 * @param name
 */
function removeStructure(name) {
    for (var i = 0; i < struc.length; i++) {
	if (name == struc[i].name) {
	    struc.splice(i, 1);
	    reDraw();
	    return;
	}
    }
    $("#alert-dataStructureDoesNotExist").html(template.warning("Data structure with name '" + name + "' does not exist any more!"));
}

function DataStructureModal() {
    
    this.hide = function() {
	$("#addStructureModal").modal('hide');
    };
    
    this.show = function() {
	$("#addStructureModal").modal('show');
    };
    
    this.themeEdit = function(name) {
	$("#addStructureModalLabel").text("Edit data structure");
	$("#addRegisterName").prop("value", name);
	$("#addRegisterSubmit").text("Save changes");
	$("#addRegisterSubmit").off("click");
	$("#addRegisterSubmit").click(function() {
	    editRegister(name);
	});
	$("#addListName").prop("value", name);
    };
    
    this.themeAdd = function() {
	$("#addStructureModalLabel").text("Add new data structure");
	$("#addRegisterSubmit").text("Add register");
	$("#addRegisterSubmit").off("click");
	$("#addRegisterSubmit").click(function() {
	    addRegister();
	});
    };
    
    /**
     * Enable/disable the input field for new register value
     */
    this.toggleDisability = function() {
	var elem = $("#addRegisterValue");
	if ($("#addRegisterCheck").prop("checked")) {
	    elem.prop("disabled", false);
	    elem.attr("placeholder", "value");
	} else {
	    elem.prop("disabled", true);
	    elem.attr("placeholder", "uninitialized");
	}
    };
}

function Validator() {

    this.checkName = function(name) {
        return checkName(name, "");
    };

    this.checkName = function(name, ignoreName) {
        var msg = new Array();
        // check if name exists
	for (var i = 0; i < struc.length; i++) {
	    if (struc[i].name != ignoreName && name == struc[i].name) {
		msg.push("Name '" + name + "' does already exist!");
		break;
	    }
	}
    
        // check validity of name
        if (name.search(/^[a-zA-Z]\w*$/) == -1) // \w = [A-Za-z0-9_]
    	    msg.push("Name '" + name + "' is not valid. Allowed characters: [A-Za-z0-9_], starting with letter.");
        
        if (msg.length > 0) {
            $("#addRegisterNameField").addClass("has-error");
            while (msg.length > 0) {
        	$("#alert-dataStructures").html(template.warning(msg.pop()));
            }
            return false;
        }
        return true;
    };
    
    this.checkValue = function(value) {
        var msg = new Array();
        
        // check for string
	var check = value.search(/^["'][A-ZÄÖÜa-zäöü0-9_ ]*["']$/) == 0;
	// check for integer
	check |= value.search(/^\d+$/) == 0; // [0-9]
	
	
        if (!check)
            msg.push("Value '" + value + "' is not valid. Allowed: strings and ints");
    
        if (msg.length > 0) {
            $("#addRegisterValueField").addClass("has-error");
            while (msg.length > 0) {
        	$("#alert-dataStructures").html(template.warning(msg.pop()));
            }
            return false;
        }
        return true;
    };
}

function Structure(name, value) {
    this.name = name;
    this.value = value;
}

/**
 * Template of one table row. The row represents one single data structure.
 */
function Template() {
    this.registerRow = function(name, value) {
        //@formatter:off
	return '<tr>' 
	+ '	<td><code>' + name + '</code></td>' 
	+ '	<td><div class="btn-group">'
	+ '		<input type="button" class="btn btn-default" id="btn-len" disabled="disabled" value="' + value + '" />' 
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
	// @formatter:on
    };

    this.warning = function(message) {
	return '<div class="alert alert-warning alert-dismissable" id="alert-dataDoesNotExist">'
	+ '	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
	+ '	<strong>Warning!</strong> ' + message 
	+ '</div>';
    };
}