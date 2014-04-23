/**
 * 
 */

var SCRIPTSITE = $("#placeLinesHere");
var maxLevel = 1;
var scriptModal = new ScriptModal();
var scriptTemplate = new ScriptTemplate();
var lines = new Lines();

function addLine() {
    // setup validation environment
    var check = true;
    valid.target("#addLineInstrField", "#alert-script");
    
    var iid = $("#addLineInstr").prop("value");
    check = valid.checkExists(iid) && check;
    
    var level = $(".btn-level.active").text();
    var pos = -1;
    
    if ($("#addLineAtBeginTab").hasClass("active")) {
	pos = 0;
    } else if ($("#addLineAtEndTab").hasClass("active")) {
	pos = lines.size();
    } else if ($("#addLineAfterTab").hasClass("active")) {
	var lid = $("#addLineAfter").prop("value");
	valid.target("addLineAfterField", "#alert-script");
	if (check = valid.checkExists(lid) && check)
	    pos = lines.getById(lid);
    }
    
    if (!check)
	return;
    
    lines.add(iid, level, pos);
    redrawLines();
    scriptModal.hide();
}

function redrawLines() {
    // compute a table row for every script line
    if (lines.size() > 0) {
	var html = "";
	for (var i = 0; i < lines.size(); i++) {
	    var iid = lines.get(i).iid;
	    var level = lines.get(i).level;
	    var inst = instr.getById(iid).toString();
	    html += scriptTemplate.scriptRow(inst, i, level);
	}
	// write the content to the page's HTML content.
	SCRIPTSITE.html(html);
	// make table visible
	$("#scriptTable").show("slow");
	// add remove functionality to button
	$(".line-remove").click(function() {
	    lines.remove($(this).prop("value"));
	    redrawLines();
	});
	// add edit functionality to button
	$(".line-edit").click(function() {
	    scriptModal.themeEdit($(this).prop("value"));
	    scriptModal.show();
	});
	// add functionality to arrow buttons
	$(".line-left").click(function() {
	   lines.decLevel($(this).prop("value"));
	   redrawLines();
	});
	$(".line-right").click(function() {
	    lines.incLevel($(this).prop("value"));
	    redrawLines();
	});
	$(".line-up").click(function() {
	    lines.moveUp($(this).prop("value"));
	    redrawLines();
	});
	$(".line-down").click(function() {
	    lines.moveDown(parseInt($(this).prop("value")));
	    redrawLines();
	});
    } else {
	SCRIPTSITE.html("");
	$("#scriptTable").hide("slow");
    }
}

function ScriptModal() {
    /** Hides the window. */
    this.hide = function() {
	$("#addLineModal").modal('hide');
    };

    /** Shows the window. */
    this.show = function() {
	$("#addLineModal").modal('show');
    };

    /**
     * This is the theme for adding new script lines. The form's input fields
     * are initialized by default values.
     */
    this.themeAdd = function() {
	// prepare select fields
	updateSelects();
	
	$(".btn-level").attr("disabled", true);
	for (var i = 0; i <= maxLevel; i++) {
	    $("#addLineLevelBtn" + i).attr("disabled", false);
	}
	
	var btnAdd = $("#addLineSubmit");
	btnAdd.off("click");
	btnAdd.click(function() {
	    addLine();
	});
    };
    
    /**
     * This is the theme for editing script lines.
     */
    this.themeEdit = function(id) {
	// TODO edit script lines
    };
}

function Lines() {
    this.lines = new Array();
    
    this.add = function(iid, level, pos) {
	var line = new this.Line(iid, level);
	if (pos == this.size())
	    this.lines.push(line);
	else {
	    for (var i = this.size(); i > pos; i--) {
		this.lines[i] = this.lines[i-1];
	    }
	    this.lines[pos] = line;
	}
    };
    
    this.get = function(i) {
	return this.lines[i];
    };
    
    this.getById = function(id) {
	for (var i = 0; i < this.size(); i++) {
	    if (this.get(i).iid == id) {
		return i;
	    }
	}
	return -1;
    };
    
    this.remove = function(i) {
	this.lines.splice(i, 1);
    };
    
    this.size = function() {
	return this.lines.length;
    };
    
    this.incLevel = function(i) {
	this.lines[i].level++;
    };
    
    this.decLevel = function(i) {
	this.lines[i].level--;
    };
    
    this.moveUp = function(i) {
	var temp = this.lines[i];
	this.lines[i] = this.lines[i-1];
	this.lines[i-1] = temp;
    };
    
    this.moveDown = function(i) {
	var temp = this.lines[i];
	this.lines[i] = this.lines[i+1];
	this.lines[i+1] = temp;
    };
    
    this.Line = function(iid, level) {
	this.iid = iid;
	this.level = level;
    };
}

/**
 * This class provides several templates for HTML content that is inserted to
 * the page dynamically.
 */
function ScriptTemplate() {
    /**
     * Returns an HTML representation of one instruction in form of a table row.
     */
    this.scriptRow = function(inst, lid, level) {
	var dist = "";
	for (var i = 0; i < level; i++) {
	    dist += "&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	
	var disabledIfLvl0 = level == 0 ? " disabled" : "";
	var disabledIfLvlMax = level == 10 ? " disabled" : "";
	var disabledIfFirst = lid == 0 ? " disabled" : "";
	var disabledIfLast = lid == lines.size() - 1 ? " disabled" : "";
	
	return '<tr>' 
	+ '	<td style="border: none;">' + dist + '<code>' + inst + '</code></td>' 
	+ '	<td style="border: none; text-align: right;">' 
	+ '		<button type="button"' + disabledIfLvl0 + ' class="btn btn-default line-left" title="decrease level" value="' + lid + '">'
	+ '			<span class="glyphicon glyphicon-arrow-left"></span>' 
	+ '		</button>&nbsp;' 
	+ '		<button type="button"' + disabledIfLvlMax + ' class="btn btn-default line-right" title="increase level" value="' + lid + '">'
	+ '			<span class="glyphicon glyphicon-arrow-right"></span>' 
	+ '		</button>&nbsp;' 
	+ '		<button type="button"' + disabledIfFirst + ' class="btn btn-default line-up" title="move line up" value="' + lid + '">'
	+ '			<span class="glyphicon glyphicon-arrow-up"></span>' 
	+ '		</button>&nbsp;' 
	+ '		<button type="button"' + disabledIfLast + ' class="btn btn-default line-down" title="move line down" value="' + lid + '">'
	+ '			<span class="glyphicon glyphicon-arrow-down"></span>' 
	+ '		</button>&nbsp;' 
	+ '		<button type="button" disabled class="btn btn-default line-edit" title="edit instruction" value="' + lid + '">'
	+ '			<span class="glyphicon glyphicon-pencil"></span>' 
	+ '		</button>&nbsp;'
	+ '		<button type="button" class="btn btn-default line-remove" title="remove instruction" value="' + lid + '">'
	+ '			<span class="glyphicon glyphicon-remove"></span>' 
	+ '		</button>' 
	+ '	</td>' 
	+ '<tr>';
    };
}