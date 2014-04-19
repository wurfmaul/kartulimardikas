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
var incFactory = new IncFactory();
var cmpFactory = new CompareFactory();

$("#addAssignTarget").click(function() {
    var elem = $(this).prop("value");
    if(elem != '' && vars.isArrayByName(elem))
	$("#addAssignTargetIndexField").show("slow");
    else
	$("#addAssignTargetIndexField").hide("slow");
});

$("#addAssignVar").click(function() {
    var elem = $(this).prop("value");
    if(elem != '' && vars.isArrayByName(elem))
	$("#addAssignVarIndexField").show("slow");
    else
	$("#addAssignVarIndexField").hide("slow");
});

$("#addIncrementVar").click(function() {
    var elem = $(this).prop("value");
    if(elem != '' && vars.isArrayByName(elem))
	$("#addIncVarIndexField").show("slow");
    else
	$("#addIncVarIndexField").hide("slow");
});

$("#addCompareLeftVar").click(function() {
    var elem = $(this).prop("value");
    if(elem != '' && vars.isArrayByName(elem))
	$("#addCompareLeftVarIndexField").show("slow");
    else
	$("#addCompareLeftVarIndexField").hide("slow");
});

$("#addCompareRightVar").click(function() {
    var elem = $(this).prop("value");
    if(elem != '' && vars.isArrayByName(elem))
	$("#addCompareRightVarIndexField").show("slow");
    else
	$("#addCompareRightVarIndexField").hide("slow");
});

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
	    $(".index-field").show();
	}
	
	$(".slct-allVars").html(options);
	$(".slct-allInsts").html("<option>not implemented yet!</option>"); // TODO options for all insts
	$(".slct-allBools").html("<option>not implemented yet!</option>"); // TODO options for all bools
	
	var btnAddAssign = $("#addAssignSubmit");
	btnAddAssign.off("click");
	btnAddAssign.click(function() {
	    assignFactory.create();
	});
	
	var btnAddInc = $("#addIncrementSubmit");
	btnAddInc.off("click");
	btnAddInc.click(function() {
	    incFactory.create();
	});
	
	var btnAddCmp = $("#addCompareSubmit");
	btnAddCmp.off("click");
	btnAddCmp.click(function() {
	    cmpFactory.create();
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
    this.NULL = 3;
    
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

function CompareFactory() {
    this.create = function() {
	// set up validation environment
	var check = true;
	$(".has-error").removeClass("has-error");
	$(".alert").alert('close');
	
	// get left operand
	var leftType = null;
	var left = null;
	var leftIndex = -1;
	
	if($("#addCompareLeftValueTab").hasClass("active")) {
	    // tab "value"
	    leftType = instr.VALUE;
	    valid.target("#addCompareLeftValueField", "#alert-compare");
	    left = $("#addCompareLeftValue").prop("value");
	    check = valid.checkValue(left) && check;
	} else if($("#addCompareLeftVarTab").hasClass("active")) {
	    // tab "var"
	    leftType = instr.REFVAR;
	    var varLeft = vars.getByName($("#addCompareLeftVar").prop("value"));
	    if (vars.isArrayByName(varLeft.name) && $("#addCompareLeftVarIndexCheck").prop("checked")) {
		leftIndex = $("#addCompareLeftVarIndex").prop("value");
		valid.target("#addCompareLeftVarIndexField", "#alert-compare");
		check = valid.checkIndex(leftIndex, varLeft.value.length - 1) && check;
	    }
	    left = varLeft.id;
	} else if($("#addCompareLeftNullTab").hasClass("active")) {
	    // tab "null"
	    leftType = instr.NULL;
	} else {
	    console.log("No tab selected for left compare operand!");
	}
	
	// get operator
	var op;
	switch ($(".btn-cmpOp.active").prop("id")) {
	case "addCompareOpLt": op = "<"; break;
	case "addCompareOpLeq": op = "<="; break;
	case "addCompareOpEq": op = "=="; break;
	case "addCompareOpNeq": op = "!="; break;
	case "addCompareOpGeq": op = ">="; break;
	case "addCompareOgGt": op = ">"; break;
	default:
	    console.log("No operator chosen!");
	    check = false;
	    op = "";
	}
	
	// get right operand
	var rightType = null;
	var right = null;
	var rightIndex = -1;
	
	if($("#addCompareRightValueTab").hasClass("active")) {
	    // tab "value"
	    rightType = instr.VALUE;
	    valid.target("#addCompareRightValueField", "#alert-compare");
	    right = $("#addCompareRightValue").prop("value");
	    check = valid.checkValue(right) && check;
	} else if($("#addCompareRightVarTab").hasClass("active")) {
	    // tab "var"
	    rightType = instr.REFVAR;
	    var varRight = vars.getByName($("#addCompareRightVar").prop("value"));
	    if (vars.isArrayByName(varRight.name) && $("#addCompareRightVarIndexCheck").prop("checked")) {
		rightIndex = $("#addCompareRightVarIndex").prop("value");
		valid.target("#addCompareRightVarIndexField", "#alert-compare");
		check = valid.checkIndex(rightIndex, varRight.value.length - 1) && check;
	    }
	} else if($("#addCompareRightNullTab").hasClass("active")) {
	    // tab "null"
	    rightType = instr.NULL;
	} else {
	    console.log("No tab selected for right compare operand!");
	}
	
	if(!check)
	    return;
	
	var inst = new this.Compare(leftType, left, leftIndex, rightType, right, rightIndex, op);
	instr.add(inst);
	redrawInst();
	instModal.hide();
    };
    
    this.Compare = function(leftType, left, leftIndex, rightType, right, rightIndex, op) {
	this.id = maxId++;
	this.leftType = leftType;
	this.left = left;
	this.leftIndex = leftIndex;
	this.rightType = rightType;
	this.right = right;
	this.rightIndex = rightIndex;
	this.op = op;
	
	this.toString = function() {
	    // generate string representation
	    var ret = this.left + " " + this.op + " " + this.right;
	    // FIXME references!
	    return ret;
	};
    };
}

function IncFactory() {
    this.create = function() {
	// set up validation environment
	var check = true;
	$(".has-error").removeClass("has-error");
	$(".alert").alert('close');
	
	// get inputs
	var target = vars.getByName($("#addIncrementVar").prop("value"));
	var index = -1;
	if (vars.isArrayByName(target.name)) {
	    index = $("#addIncVarIndex").prop("value");
	    // check index
	    valid.target("#addIncVarIndexField", "#alert-inc");
	    check = valid.checkIndex(index, target.value.length - 1);
	}
	
	if(!check)
	    return;
	
	var inst = new this.Inc(instr.REFVAR, target.id, index, $("#addIncBtn").hasClass("active"));
	instr.add(inst);
	redrawInst();
	instModal.hide();
    };
    
    this.Inc = function(targetType, target, targetIndex, inc) {
	this.id = maxId++;
	this.targetType = targetType;
	this.target = target;
	this.targetIndex = targetIndex;
	this.inc = inc;
	
	this.toString = function() {
	    // generate string representation
	    var ret = vars.getById(this.target).name;
	    if(this.targetIndex != -1)
		ret += "[" + this.targetIndex + "]";
	    
	    if(this.inc)
		ret += "++";
	    else
		ret += "--";
	    
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