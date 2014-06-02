/**
 * This file contains every functionality concerning instruction 
 * generation and management.
 */

var STEPSITE = $("#placeStepsHere");
var stepForm = new StepForm();
var stepTemplate = new StepTemplate();
var steps = new Steps();
var maxStepId = 0;

var assignFactory = new AssignFactory();
var cmpFactory = new CompareFactory();
var condFactory = new ConditionFactory();
var incFactory = new IncFactory();
var loopFactory = new LoopFactory();

$("#btn-addStep").click(function() {
    $(this).hide();
    $("#placeStepsHere").show();
    stepForm.addRowBelow(-1);
});

function StepForm() {    
    this.noOfRows = 0;
    
    this.addRowBelow = function(id) {
	if (id == -1) { // first row
	    STEPSITE.html(stepTemplate.stepRow(maxStepId));
	} else {
	    $("#step-" + id).after(stepTemplate.stepRow(maxStepId));
	}
	this.themeEdit(maxStepId++);
	this.noOfRows++;
    };

    this.removeRow = function(id) {
	$("#step-" + id).hide("slow");
	$("#step-" + id).remove();
	if (--this.noOfRows == 0){
	    this.addRowBelow(-1);
	}
	
    };
    
    this.moveRowUp = function(id) {
	var curRow = $("#step-" + id);
	if (curRow.prev().length != 0) {
	    curRow.prev().before(curRow.clone());
	    curRow.remove();
	    this.updateActionHandlers(id);
	}
    };
    
    this.moveRowDown = function(id) {
	var curRow = $("#step-" + id);
	if (curRow.next().length != 0) {
	    curRow.next().after(curRow.clone());
	    curRow.remove();
	    this.updateActionHandlers(id);
	}
    };

    this.checkAndCreateStep = function(id) { // TODO
	switch($("#slct-step-" + id + "-type").val()) {
	case "assignVarToVal":
	    assignFactory.create(id);
	    break;
	case "assignVarToVar":
	    assignFactory.create(id);
	    break;
	default:
	    console.log("not yet supported: " + $("#slct-step-" + id + "-type").val());
	}
    };
    
    this.checkAndEditStep = function(id) { // TODO
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
	var leftCell = $("#step-" + id + "-left");
	var rightCell = $("#step-" + id + "-right");
	
	var step = steps.getById(id);
	leftCell.html(stepTemplate.stepCell(step.toString()));
	rightCell.html(stepTemplate.buttonsShow(id));
	this.updateActionHandlers(id);
    };
    
    this.themeEdit = function(id) {
	var leftCell = $("#step-" + id + "-left");
	var rightCell = $("#step-" + id + "-right");
	
	leftCell.html(stepTemplate.inputEdit(id));
	rightCell.html(stepTemplate.buttonsEdit(id));
	this.updateActionHandlers(id);
    };
    
    this.updateActionHandlers = function(id) {
	var curAddButton = $("#btn-step-" + id + "-add");
	var curRemoveButton = $("#btn-step-" + id + "-remove");
	var curEditButton = $("#btn-step-" + id + "-edit");
	var curCheckButton = $("#btn-step-" + id + "-check");
	var curMoveUpButton = $("#btn-step-" + id + "-up");
	var curMoveDownButton = $("#btn-step-" + id + "-down");
	var curTypeSelect = $("#slct-step-" + id + "-type");
	var curTargetSelect = $("#slct-step-" + id + "-targetVar");
	
	curAddButton.off("click");
	curRemoveButton.off("click");
	curEditButton.off("click");
	curCheckButton.off("click");
	curMoveUpButton.off("click");
	curMoveDownButton.off("click");
	curTypeSelect.off("click");
	
	curAddButton.click(function() {
	    stepForm.addRowBelow(id); 
	});

	curRemoveButton.click(function() {
	    steps.removeById(id);
	    stepForm.removeRow(id); 
	});

	curEditButton.click(function() {
	    stepForm.themeEdit(id);
	});

	if (steps.findId(id) != -1) {
	    // step already exists
	    curCheckButton.click(function() {
		stepForm.checkAndEditStep(id);
	    });
	} else {
	    curCheckButton.click(function() {
		stepForm.checkAndCreateStep(id);
	    });
	}
	

	curMoveUpButton.click(function() {
	    stepForm.moveRowUp(id);
	});
	
	curMoveDownButton.click(function() {
	    stepForm.moveRowDown(id);
	});
	
	var targetVar = $("#step-" + id + "-targetVarField");
	var targetIdx = $("#step-" + id + "-targetIdxField");
	var sourceVal = $("#step-" + id + "-sourceValField");
	var sourceVar = $("#step-" + id + "-sourceVarField");
	var sourceIdx = $("#step-" + id + "-sourceIdxField");
	
	curTypeSelect.click(function() {
	    var value = $(this).val();
	    
	    switch (value) {
	    case "assignValToVar":
		targetVar.show();
		targetIdx.hide();
		sourceVal.show();
		sourceVar.hide();
		sourceIdx.hide();
		break;
	    case "assignVarToVar":
		targetVar.show();
		targetIdx.hide();
		sourceVal.hide();
		sourceVar.show();
		sourceIdx.hide();
	    default:
	    }
	});
	
	curTargetSelect.click(function() {
	    var vid = $(this).val();
	    if (vid != null && vars.isArrayById(vid)) {
		targetIdx.show();
	    } else {
		targetIdx.hide();
	    }
	    //XXX
	});
    };
}

function Steps() {
    this.REFVAR = 0;
    this.REFINST = 1;
    this.VALUE = 2;
    this.NULL = 4;
    
    this.RETVOID = 32;
    this.RETBOOL = 64;
    this.RETDIV = 128;
    
    this.insts = new Array();
    
    this.add = function(inst) {
	this.insts.push(inst);
    };
    
    this.get = function(i) {
	return this.insts[i];
    };
    
    this.getById = function(id) {
	var index = this.findId(id);
	if (index != -1)
	    return this.get(index);
	return null;
    };
    
    this.findId = function(id) {
	for (var i = 0; i < this.size(); i++) {
	    if (this.get(i).id == id)
		return i;
	}
	return -1;
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
	valid.target("#addAssignVarField", "#alert-assign");
	$(".has-error").removeClass("has-error");
	$(".alert").alert('close');
	
	// get inputs
	var target = vars.getByName($("#addAssignTarget").prop("value")); //XXX
	check = valid.checkExists(target) && check;
	
	var index = -1;
	if (check && vars.isArrayByName(target.name) && $("#addAssignTargetIndexCheck").prop("checked")) {
	    index = $("#addAssignTargetIndex").prop("value");
	    // check index
	    valid.target("#addAssignTargetIndexField", "#alert-assign");
	    check = valid.checkIndex(index, target.value.length - 1);
	}
	
	var inst = null;
	valid.target("#addAssignValueField", "#alert-assign");
	if($("#addAssignValueTab").hasClass("active")) {
	    // tab "value"
	    var value = $("#addAssignValue").prop("value");
	    if (check = valid.checkValue(value) && check)
		inst = new this.Assign(steps.REFVAR, target.id, index, steps.VALUE, value, -1);
	} else if($("#addAssignVarTab").hasClass("active")) {
	    // tab "var"
	    var source = vars.getByName($("#addAssignVar").prop("value"));
	    if (check = valid.checkExists(source) && check) {
		var sourceIdx = -1;
		if (vars.isArrayByName(source.name) && $("#addAssignVarIndexCheck").prop("checked")) {
		    sourceIdx = $("#addAssignVarIndex").prop("value");
		    valid.target("#addAssignVarIndexField", "#alert-assign");
		    check = valid.checkIndex(sourceIdx, source.value.length - 1) && check;
		}
		inst = new this.Assign(steps.REFVAR, target.id, index, steps.REFVAR, source.id, sourceIdx);
	    }
	} else if($("#addAssignInstTab").hasClass("active")) {
	    // tab "inst"
	    var sourceId = $("#addAssignInst").prop("value");
	    if (check = valid.checkExists(sourceId) && check)
		inst = new this.Assign(steps.REFVAR, target.id, index, steps.REFINST, sourceId, -1);
	} else {
	    console.log("No tab selected for assign value!");
	}
	
	if(!check || inst == null)
	    return;
	
	steps.add(inst);
	redrawInst();
	stepForm.hide();
    };
    
    this.Assign = function(targetType, target, targetIndex, valueType, value, valueIndex) {
	this.id = maxStepId++;
	this.targetType = targetType;
	this.target = target;
	this.targetIndex = targetIndex;
	this.valueType = valueType;
	this.value = value;
	this.valueIndex = valueIndex;
	this.retType = steps.RETBOOL;
	
	this.toString = function() {
	    // generate string representation
	    var ret = vars.getById(this.target).name;
	    if(this.targetIndex != -1)
		ret += "[" + this.targetIndex + "]";
	    ret += " = ";
	    
	    switch (this.valueType) {
	    case steps.REFVAR:
		ret += vars.getById(this.value).name;
		if(this.valueIndex != -1)
		    ret += "[" + this.valueIndex + "]";
		break;

	    case steps.REFINST:
		ret += "(" + steps.getById(this.value).toString() + ")";
		break;

	    case steps.VALUE:
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
	valid.target("#addCompareLeftField", "#alert-compare");
	
	// get left operand
	var leftType = null;
	var left = null;
	var leftIndex = -1;
	
	if($("#addCompareLeftValueTab").hasClass("active")) {
	    // tab "value"
	    leftType = steps.VALUE;
	    left = $("#addCompareLeftValue").prop("value");
	    check = valid.checkValue(left) && check;
	} else if($("#addCompareLeftVarTab").hasClass("active")) {
	    // tab "var"
	    leftType = steps.REFVAR;
	    var varLeft = vars.getByName($("#addCompareLeftVar").prop("value"));
	    if (check = valid.checkExists(varLeft) && check) {
		if (vars.isArrayByName(varLeft.name) && $("#addCompareLeftVarIndexCheck").prop("checked")) {
		    leftIndex = $("#addCompareLeftVarIndex").prop("value");
		    valid.target("#addCompareLeftVarIndexField", "#alert-compare");
		    check = valid.checkIndex(leftIndex, varLeft.value.length - 1) && check;
		}
		left = varLeft.id;
	    }
	} else if($("#addCompareLeftNullTab").hasClass("active")) {
	    // tab "null"
	    leftType = steps.NULL;
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
	valid.target("#addCompareRightField", "#alert-compare");
	
	if($("#addCompareRightValueTab").hasClass("active")) {
	    // tab "value"
	    rightType = steps.VALUE;
	    right = $("#addCompareRightValue").prop("value");
	    check = valid.checkValue(right) && check;
	} else if($("#addCompareRightVarTab").hasClass("active")) {
	    // tab "var"
	    rightType = steps.REFVAR;
	    var varRight = vars.getByName($("#addCompareRightVar").prop("value"));
	    if (check = valid.checkExists(varRight) && check) {
		if (vars.isArrayByName(varRight.name) && $("#addCompareRightVarIndexCheck").prop("checked")) {
		    rightIndex = $("#addCompareRightVarIndex").prop("value");
		    valid.target("#addCompareRightVarIndexField", "#alert-compare");
		    check = valid.checkIndex(rightIndex, varRight.value.length - 1) && check;
		}
		right = varRight.id;
	    }
	} else if($("#addCompareRightNullTab").hasClass("active")) {
	    // tab "null"
	    rightType = steps.NULL;
	} else {
	    console.log("No tab selected for right compare operand!");
	}
	
	if(!check)
	    return;
	
	var inst = new this.Compare(leftType, left, leftIndex, rightType, right, rightIndex, op);
	steps.add(inst);
	redrawInst();
	stepForm.hide();
    };
    
    this.Compare = function(leftType, left, leftIndex, rightType, right, rightIndex, op) {
	this.id = maxStepId++;
	this.leftType = leftType;
	this.left = left;
	this.leftIndex = leftIndex;
	this.rightType = rightType;
	this.right = right;
	this.rightIndex = rightIndex;
	this.op = op;
	this.retType = steps.RETBOOL;
	
	this.toString = function() {
	    // left operand
	    var ret = "";
	    switch (this.leftType) {
	    case steps.REFVAR:
		ret += vars.getById(this.left).name;
		if (this.leftIndex != -1)
		    ret += "[" + this.leftIndex + "]";
		break;
		
	    case steps.VALUE:
		ret += this.left;
		break;
		
	    case steps.NULL:
		ret += "null";
		break;
		
	    default:
		console.log("Error: wrong type!");
	    }
	    
	    // operator
	    ret += " " + this.op + " ";

	    // right operand
	    switch (this.rightType) {
	    case steps.REFVAR:
		ret += vars.getById(this.right).name;
		if (this.rightIndex != -1)
		    ret += "[" + this.rightIndex + "]";
		break;
		
	    case steps.VALUE:
		ret += this.right;
		break;
		
	    case steps.NULL:
		ret += "null";
		break;
		
	    default:
		console.log("Error: wrong type!");
	    }
	    return ret;
	};
    };
}

function ConditionFactory() {
    var IF = 0;
    var ELSIF = 1;
    var ELSE = 2;
    
    this.create = function() {
	// set up validation environment
	var check = true;
	$(".has-error").removeClass("has-error");
	$(".alert").alert('close');
	
	var inst = null;
	if ($("#addIfTab").hasClass("active")) {
	    // tab if
	    var id = $("#addIfCond").prop("value");
	    var type;
	    if (id.charAt(0) == 'v') // var
		type = steps.REFVAR;
	    else
		type = steps.REFINST;
	    valid.target("#addIfCondField", "#alert-cond");
	    check = valid.checkExists(id);
	    id = id.substring(1, id.length - 1);
	    inst = new this.Condition(IF, type, id);
	} else if ($("#addElseIfTab").hasClass("active")) {
	    // tab elsif
	    var id = $("#addElseIfCond").prop("value");
	    var type;
	    if (id.charAt(0) == 'v') // var
		type = steps.REFVAR;
	    else
		type = steps.REFINST;
	    valid.target("#addElseIfCondField", "#alert-cond");
	    check = valid.checkExists(id);
	    id = id.substring(1, id.length - 1);
	    inst = new this.Condition(ELSIF, type, id);	    
	} else if ($("#addElseTab").hasClass("active")) {
	    // tab else
	    inst = new this.Condition(ELSE, -1, -1);
	} else {
	    console.log("No tab chosen!");
	}
	
	if(!check || inst == null)
	    return;
	
	steps.add(inst);
	redrawInst();
	stepForm.hide();
    };
    
    this.Condition = function(kind, condType, condId) {
	this.id = maxStepId++;
	this.kind = kind;
	this.condType = condType;
	this.condId = condId;
	this.retType = steps.RETVOID;
	
	this.toString = function() {
	    var ret = "";
	    if (this.kind == ELSE)
		ret += "else";
	    else if (this.kind == ELSIF)
		ret += "else ";
	    
	    if (this.kind == IF || this.kind == ELSIF) {
		ret += "if (";
		if (this.condType == steps.REFVAR)
		    ret += vars.getById(this.condId).toString();
		else if (this.condType == steps.REFINST)
		    ret += steps.getById(this.condId).toString();
		ret += ")";
	    }
		
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
	valid.target("#addIncrementVarField", "#alert-inc");
	
	// get inputs
	var target = vars.getByName($("#addIncrementVar").prop("value"));
	var index = -1;
	if (check = valid.checkExists(target)) {
	    if (vars.isArrayByName(target.name)) {
		index = $("#addIncVarIndex").prop("value");
		// check index
		valid.target("#addIncVarIndexField", "#alert-inc");
		check = valid.checkIndex(index, target.value.length - 1);
	    }
	}
	
	if(!check)
	    return;
	
	var inc = $("#addPostIncBtn").hasClass("active") || $("#addPreIncBtn").hasClass("active");
	var pre = $("#addPreIncBtn").hasClass("active") || $("#addPreDecBtn").hasClass("active");
	
	var inst = new this.Inc(steps.REFVAR, target.id, index, inc, pre);
	steps.add(inst);
	redrawInst();
	stepForm.hide();
    };
    
    this.Inc = function(targetType, target, targetIndex, inc, pre) {
	this.id = maxStepId++;
	this.targetType = targetType;
	this.target = target;
	this.targetIndex = targetIndex;
	this.inc = inc;
	this.pre = pre;
	this.retType = steps.RETBOOL;
	
	this.toString = function() {
	    // generate string representation
	    var ret = "";
	    if (this.pre) {
		if(this.inc)
		    ret += "++";
		else
		    ret += "--";
	    }
	    
	    ret += vars.getById(this.target).name;
	    if(this.targetIndex != -1)
		ret += "[" + this.targetIndex + "]";
	    
	    if (!this.pre) {
		if(this.inc)
		    ret += "++";
		else
		    ret += "--";
	    }
	    
	    return ret;
	};
    };
}

function LoopFactory() {
    var WHILE = 0;
    var FOR = 1;
    
    this.create = function() {
	// set up validation environment
	var check = true;
	$(".has-error").removeClass("has-error");
	$(".alert").alert('close');
	valid.target("#addLoopCondField", "#alert-loop");
	
	var id = $("#addLoopCond").prop("value");
	var type;
	if (id.charAt(0) == 'v') // var
	    type = steps.REFVAR;
	else
	    type = steps.REFINST;
	check = valid.checkExists(id);
	id = id.substring(1, id.length - 1);
	
	var inst = null;
	if ($("#addWhileLoopTab").hasClass("active")) {
	    // tab if
	    inst = new this.Loop(WHILE, type, id, -1, -1);
	} else if ($("#addForLoopTab").hasClass("active")) {
	    // tab for-loop
	    valid.target("#addForLoopInitField", "#alert-loop");
	    var initId = $("#addForLoopInit").prop("value");
	    check = valid.checkExists(initId) && check;
	    
	    valid.target("#addForLoopAfterField", "#alert-loop");
	    var afterId = $("#addForLoopInit").prop("value");
	    check = valid.checkExists(afterId) && check;
	    
	    inst = new this.Loop(FOR, type, id, initId, afterId);
	} else {
	    console.log("No loop tab chosen!");
	}
	
	if(!check || inst == null)
	    return;
	
	steps.add(inst);
	redrawInst();
	stepForm.hide();
    };
    
    this.Loop = function(kind, condType, condId, initInst, afterInst) {
	this.id = maxStepId++;
	this.kind = kind;
	this.condType = condType;
	this.condId = condId;
	this.initInst = initInst;
	this.afterInst = afterInst;
	this.retType = steps.RETVOID;
	
	this.toString = function() {
	    var ret = "";
	    if (this.kind == WHILE) {
		ret += "while (";
	    } else if (this.kind == FOR) {
		ret += "for (" + steps.getById(this.initInst).toString() + "; ";
	    }
	    
	    if (this.condType == steps.REFVAR)
		ret += vars.getById(this.condId).toString();
	    else if (this.condType == steps.REFINST)
		ret += steps.getById(this.condId).toString();
	    
	    if (this.kind == WHILE) {
		ret += ")";
	    } else if (this.kind == FOR) {
		ret += "; " + steps.getById(this.afterInst).toString() + ")";
	    }
		
	    return ret;
	};
    };
}

/**
 * This class provides several templates for HTML content that is inserted to
 * the page dynamically.
 */
function StepTemplate() {
    /**
     * Returns an HTML representation of one instruction in form of a table row.
     */
    this.stepRow = function(id) {
	return '<tr id="step-' + id + '">'
	+ '<td style="vertical-align: middle;" id="step-' + id + '-left"></td>'
	+ '<td style="width: 80pt; text-align: center;" id="step-' + id + '-right"></td>'
	+ '</tr>';
    };
    
    this.inputEdit = function(id) {
	var valueInvisible = " display: none;";
	var targetVarInvisible = "";
	var targetIdxInvisible = " display: none;";
	var sourceVarInvisible = " display: none;";
	var sourceIdxInvisible = " display: none;";
	var sourceValInvisible = "";
	
	var step = steps.getById(id);
	if (typeof step != "undefined") {
	    //TODO edit template
	}
	
	var allVars = "";
	for (var i = 0; i < vars.vars.length; i++) {
	    var v = vars.vars[i];
	    allVars += '<option value="' + v.id + '">' + v.name + '</option>';
	}
	
	return ''
	+ '<div class="col-xs-3">'
	+ '	<div class="form-group" style="margin-bottom:0px">'
	+ '		<label class="sr-only" for="slct-step-' + id + '-type">Step type</label>'
	+ '		<select class="form-control" id="slct-step-' + id + '-type">'
	+ '			<optgroup label="Assignment">'
	+ '				<option value="assignValToVar">variable = value</option>'
	+ '				<option value="assignVarToVar">variable = variable</option>'
	+ '			</optgroup>'
	+ '			<optgroup label="Increment">'
	+ '				<option value="inc">Increment</option>'
	+ '				<option value="dec">Decrement</option>'
	+ '			</optgroup>'
	+ '			<optgroup label="Branch">'
	+ '				<option value="if">If</option>'
	+ '				<option value="elsif">Else If</option>'
	+ '				<option value="else">Else</option>'
	+ '			</optgroup>'
	+ '			<optgroup label="Loop">'
	+ '				<option value="while">While</option>'
	+ '				<option value="for">For</option>'
	+ '			</optgroup>'
	+ '		</select>'
	+ '	</div>'
	+ '</div>'
	+ '<div class="col-xs-2">'
	+ '	<div class="form-group" id="step-' + id + '-targetVarField" style="margin-bottom:0px;' + targetVarInvisible + '">'
	+ '		<label class="sr-only" for="slct-step-' + id + '-var">Target variable</label>'
	+ '		<select class="form-control" id="slct-step-' + id + '-targetVar">'
	+ '			' + allVars 
	+ '		</select>'
	+ '	</div>'
	+ '</div>'
	+ '<div class="col-xs-2">'
	+ '	<div class="form-group" id="step-' + id + '-targetIdxField" style="margin-bottom:0px;' + targetIdxInvisible + '">'
	+ '		<label class="sr-only" for="slct-step-' + id + '-targetIdx">Target variable index</label>'
	+ '		<select class="form-control" id="slct-step-' + id + '-targetIdx"></select>'
	+ '	</div>'
	+ '</div>'
	+ '<div class="col-xs-1">'
	+ '	<div class="cell"><code>&larr;</code></div>'
	+ '</div>'
	+ '<div class="col-xs-2">'
	+ '	<div class="form-group" id="step-' + id + '-sourceVarField" style="margin-bottom:0px;' + sourceVarInvisible + '">'
	+ '		<label class="sr-only" for="slct-step-' + id + '-var">Source variable</label>'
	+ '		<select class="form-control" id="slct-step-' + id + '-sourceVar">'
	+ '			' + allVars 
	+ '		</select>'
	+ '	</div>'
	+ '</div>'
	+ '<div class="col-xs-2">'
	+ '	<div class="form-group" id="step-' + id + '-sourceIdxField" style="margin-bottom:0px;' + sourceIdxInvisible + '">'
	+ '		<label class="sr-only" for="slct-step-' + id + '-sourceIdx">Target variable index</label>'
	+ '		<select class="form-control" id="slct-step-' + id + '-sourceIdx"></select>'
	+ '	</div>'
	+ '</div>'	
	+ '<div class="col-xs-4">'
	+ '	<div class="form-group" id="step-' + id + '-sourceValField" style="margin-bottom:0px;' + sourceValInvisible + '">'
	+ '		<label class="sr-only" for="step-' + id + '-sourceVal">Value</label>'
	+ '		<input type="text" class="form-control" id="step-' + id + '-sourceVal" value="" placeholder="value">'
	+ '	</div>'
	+ '	<div class="form-group" id="step-' + id + '-sizeField" style="margin-bottom:0px;' + valueInvisible + '">'
	+ '		<label class="sr-only" for="slct-step-' + id + '-size">Array size</label>'
	+ '		<select class="form-control" id="slct-step-' + id + '-size">'
	+ '			<optgroup label="Size">'
	+ '			</optgroup>'
	+ '		</select>'
	+ '	</div>'
	+ '</div>'
	+ '';
    };
    
    this.buttonsEdit = function(id) {
	return '<div class="btn-group btn-group-xs">'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-check" value="' + id + '"><span class="glyphicon glyphicon-ok"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-cancel" value="' + id + '"><span class="glyphicon glyphicon-remove"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-add" value="' + id + '"><span class="glyphicon glyphicon-plus"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-remove" value="' + id + '"><span class="glyphicon glyphicon-minus"></span></button>'
	+ '</div>'
	+ '<div class="btn-group btn-group-xs" style="margin-top: 2pt;">'
	+ '	<button type="button" class="btn btn-default" id="btm-step-' + id + '-left" value="' + id + '"><span class="glyphicon glyphicon-arrow-left"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-right" value="' + id + '"><span class="glyphicon glyphicon-arrow-right"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-up" value="' + id + '"><span class="glyphicon glyphicon-arrow-up"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-down" value="' + id + '"><span class="glyphicon glyphicon-arrow-down"></span></button>'
	+ '</div>';
    };
    
    this.buttonsShow = function(id) {
	return '<div class="btn-group btn-group-xs">'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-edit" value="' + id + '"><span class="glyphicon glyphicon-pencil"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-up" value="' + id + '"><span class="glyphicon glyphicon-arrow-up"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-down" value="' + id + '"><span class="glyphicon glyphicon-arrow-down"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-add" value="' + id + '"><span class="glyphicon glyphicon-plus"></span></button>'
	+ '	<button type="button" class="btn btn-default" id="btn-step-' + id + '-remove" value="' + id + '"><span class="glyphicon glyphicon-minus"></span></button>'
	+ '</div>';
    };
}