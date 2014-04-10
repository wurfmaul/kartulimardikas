/**
 * jQUERY - ADD EVENT HANDLERS
 */

// warn to prevent data loss
//$(window).bind('beforeunload', function() {
//  return 'Are you sure you want to leave? Unsaved changes may get lost!';
//});

$("#addRegisterCheck").click(function() {
    toggleDisability();
});

$("#addRegisterSubmit").click(function() {
    addRegister();
});

$(".btn-size").click(function() {
    // update maximum list value
    var listSize = $(this).text();
    $("#addListMaxValue").text(listSize - 1);
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
    html = "";
    for (var i = 0; i < struc.length; i++) {
	name = struc[i].name;
	value = struc[i].value;
	html += templateRegisterRow(name, value);
    }
    site.html(html);

    // add remove functionality
    $(".data-remove").click(function() {
	removeStructure($(this).prop("value"));
    });
}

/**
 * Add new register
 */
function addRegister() {
    var name = $("#addRegisterName").prop("value");
    if (!checkName(name))
	return;
    
    // all checks passed
    $("#addRegisterNameField").removeClass("has-error");
    $("#addStructureModal").modal('hide');

    // retrieve value
    var value = "?";
    if ($("#addRegisterCheck").prop("checked"))
	value = $("#addRegisterValue").prop("value");

    // add register to internal structure
    struc[struc.length] = new Structure(name, value);

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
    $("#alert-dataStructureDoesNotExist").html(templateWarning("Data structure with name '" + name + "' does not exist any more!"));
}

/**
 * Template of one table row. The row represents one single data structure.
 * 
 * @param name
 * @param value
 * @returns {String} HTML code of table row
 */
function templateRegisterRow(name, value) {
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
}

function templateWarning(message) {
    return '<div class="alert alert-warning alert-dismissable" id="alert-dataDoesNotExist">'
    + '	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
    + '	<strong>Warning!</strong> ' + message
    + '</div>';    
}

/**
 * Enable/disable the input field for new register value
 */
function toggleDisability() {
    var elem = $("#addRegisterValue");
    if ($("#addRegisterCheck").prop("checked")) {
	elem.prop("disabled", false);
	elem.attr("placeholder", "value");
    } else {
	elem.prop("disabled", true);
	elem.attr("placeholder", "uninitialized");
    }
}

function checkName(name) {
    var msg = new Array();
    // check if name exists
    for (var i = 0; i < struc.length; i++) {
	if (name == struc[i].name) {
	    msg.push("Name '" + name + "' does already exist!");
	    break;
	}
    }
    
    // check validity of name
    if (name.search(/^\w+$/) == -1)
	msg.push("Name '" + name + "' is not valid. Allowed characters: [A-Za-z0-9_]");
    
    if (msg.length > 0) {
	$("#addRegisterNameField").addClass("has-error");
	while(msg.length > 0) {
	    $("#alert-dataStructures").html(templateWarning(msg.pop()));
	}
	return false;
    }
    
    return true;
}

function Structure(name, value) {
    this.name = name;
    this.value = value;
}