/**
 * This file contains every functionality concerning instruction 
 * generation and management.
 */

var INSTSITE = $("#placeInstructionsHere");
var instModal = new InstructionModal();
var instTemplate = new InstTemplate();
var instr = new Instructions();
var maxId = 0;

var assignFactory = new AssignFactory();

function redrawInst() {
    if(instr.size() > 0) {
	var html = "";
	for (var i = 0; i < instr.size(); i++) {
	    var inst = instr.get(i).toString();
	    var iid = instr.get(i).id;
	    html += instTemplate.instRow(inst, iid);
	}
	INSTSITE.html(html);
	// show table
	$("#instTable").show("slow");
	// add remove functionality to button
	$(".inst-remove").click(function() {
	    removeInstruction($(this).prop("value"));
	});
	// add edit functionality to button
	$(".inst-edit").click(function() {
	    instModal.themeEdit($(this).prop("value"));
	    instModal.show();
	});
    } else {
	INSTSITE.html("");
	$("#instTable").hide("slow");
    }
}

function removeInstruction(iid) {
    instr.remove(iid);
    redrawInst();
}

function InstructionModal() {
    /** Hides the window. */
    this.hide = function() {
	$("#addInstructionModal").modal('hide');
    };

    /** Shows the window. */
    this.show = function() {
	$("#addInstructionModal").modal('show');
    };
    
    this.themeAdd = function() {
	var options = "";
	for (var i = 0; i < vars.size(); i++) {
	    options += "<option>" + vars.get(i).name + "</option>";
	}
	
	if(vars.size() >= 1 && vars.isArray(0)) {
	    // show index fields
	    $("#addAssignTargetIndexField").show();
	    $("#addAssignVarIndexField").show();
	}
	
	$(".slct-allVars").html(options);
	$(".slct-allInsts").html("<option>not implemented yet!</option>"); // TODO options for all insts
	$(".slct-allBools").html("<option>not implemented yet!</option>"); // TODO options for all bools
	
	var btnAddAssign = $("#addAssignSubmit");
	btnAddAssign.off("click");
	btnAddAssign.click(function() {
	    assignFactory.create();
	});
    };
    
    this.themeEdit = function(iid) {
	// TODO themeEdit
    };
}

function Instructions() {
    this.REFVAR = 0;
    this.REFINST = 1;
    this.VALUE = 2;
    
    this.insts = new Array();
    
    this.add = function(inst) {
	this.insts.push(inst);
    };
    
    this.get = function(i) {
	return this.insts[i];
    };
    
    this.remove = function(id) {
	for (var i = 0; i < this.size(); i++) {
	    if (this.get(i).id == id) {
		this.insts.splice(i, 1);
		return true;
	    }
	}
	return false;
    };
    
    this.size = function() {
	return this.insts.length;
    };
}

function AssignFactory() {
    this.create = function() {
	// set up validation environment
	var check = true;
	$(".has-error").removeClass("has-error");
	$(".alert").alert('close');
	
	// get inputs
	var target = vars.getByName($("#addAssignTarget").prop("value"));
	var index = -1;
	if ($("#addAssignTargetIndexCheck").prop("checked")) {
	    index = $("#addAssignTargetIndex").prop("value");
	    // check index
	    valid.target("#addAssignTargetIndexField", "#alert-assign");
	    check = valid.checkIndex(index, target.value.length - 1);
	}
	
	var inst = null;
	if($("#addAssignValueTab").hasClass("active")) {
	    // tab "value"
	    valid.target("#addAssignValueField", "#alert-assign");
	    var value = $("#addAssignValue").prop("value");
	    check = valid.checkValue(value) && check;
	    inst = new this.Assign(instr.REFVAR, target.id, index, instr.VALUE, value, -1);
	} else if($("#addAssignVarTab").hasClass("active")) {
	    // tab "var"
	    var source = vars.getByName($("#addAssignVar").prop("value"));
	    var sourceIdx = -1;
	    if ($("#addAssignVarIndexCheck").prop("checked")) {
		sourceIdx = $("#addAssignVarIndex").prop("value");
		valid.target("#addAssignVarIndexField", "#alert-assign");
		check = valid.checkIndex(sourceIdx, source.value.length - 1) && check;
	    }
	    inst = new this.Assign(instr.REFVAR, target.id, index, instr.REFVAR, source.id, sourceIdx);
	} else if($("#addAssignInstTab").hasClass("active")) {
	    // tab "inst"
	    // TODO inst support
	} else {
	    console.log("No tab selected for assign value!");
	}
	
	if(!check || inst == null)
	    return;
	
	instr.add(inst);
	redrawInst();
	instModal.hide();
    };
    
    this.Assign = function(targetType, target, targetIndex, valueType, value, valueIndex) {
	this.id = maxId++;
	this.targetType = targetType;
	this.target = target;
	this.targetIndex = targetIndex;
	this.valueType = valueType;
	this.value = value;
	this.valueIndex = valueIndex;
	
	this.toString = function() {
	    // generate string representation
	    var ret = vars.getById(this.target).name;
	    if(this.targetIndex != -1)
		ret += "[" + this.targetIndex + "]";
	    ret += " = ";
	    
	    switch (this.valueType) {
	    case instr.REFVAR:
		ret += vars.getById(this.value).name;
		if(this.valueIndex != -1)
		    ret += "[" + this.valueIndex + "]";
		break;

	    case instr.REFINST:
		// TODO inst support
		ret += "<INST>";
		break;

	    case instr.VALUE:
		ret += this.value;
		break;

	    default:
		console.log("unknown valueType: " + this.valueType);
	    }
	    return ret;
	};
    };
}

/**
 * This class provides several templates for HTML content that is inserted to
 * the page dynamically.
 */
function InstTemplate() {
    /**
     * Returns an HTML representation of one instruction in form of a table row.
     */
    this.instRow = function(inst, iid) {
	return '<tr>' 
	+ '	<td><code>' + inst + '</code></td>' 
	+ '	<td style="text-align: right;">' 
	+ '		<button type="button" class="btn btn-default inst-edit" title="edit instruction" value="' + iid + '">'
	+ '			<span class="glyphicon glyphicon-pencil"></span>' 
	+ '		</button>&nbsp;'
	+ '		<button type="button" class="btn btn-default inst-remove" title="remove instruction" value="' + iid + '">'
	+ '			<span class="glyphicon glyphicon-remove"></span>' 
	+ '		</button>' 
	+ '	</td>' 
	+ '<tr>';
    };
}