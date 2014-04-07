/**
 * This is the empty frame that should be filled by the machine.
 */

// constants
var TIMEOUT = 200;
//~NOOFSTATES~//

// variables
//~VARDECL~//

// internals
/** Stores the current state */
var state = 0;
/** Stores the previous state */
var prevState;
/** The id of the timer as reference */
var playTimer;
/** Indicates whether the algorithm is finished */
var done = false;

/** Manages the control panels */
var ctrl = new Controls();
/** Manages all other display elements */
var display = new Display();

/**
 * The click-event of the very left button. Sets algorithm back to the
 * beginning.
 */
function reset() {
	// reset variables
	//~VARRESET~//

	// reset state
	state = 0;
	// reset cursor
	stepCursor();
	// reset result
	done = false;
	// reset controls
	pause();
	ctrl.set(ctrl.BEGIN);
	display.reset();
}

/**
 * The click-event of the second button. Moves back by one step.
 */
function stepback() {
	alert("Not supported operation!");
}

/**
 * The click-event of the center button if it's not currently running. Steps
 * slowly to the final state.
 */
function play() {
	// enable automatic stepping
	playTimer = setInterval(performStep, TIMEOUT);
	// transform play-button to pause-button
	$("#btn-play").prop("title", "Pause");
	document.getElementById("btn-play").setAttribute("onclick", "pause()"); // TODO jQuery somehow
	$("#img-play").removeClass("glyphicon-play").addClass("glyphicon-pause");
	// disable manual stepping
	ctrl.set(ctrl.PLAY);
}

/**
 * The click-event of the center button if it's currently running. Stops
 * automatic stepping.
 */
function pause() {
	// disable automatic stepping
	clearInterval(playTimer);
	// transform pause-button to play-button
	$("#btn-play").prop("title", "Play");
	document.getElementById("btn-play").setAttribute("onclick", "play()");
	$("#img-play").removeClass("glyphicon-pause").addClass("glyphicon-play");
	// enable manual stepping
	ctrl.set(ctrl.PAUSE);
}

/**
 * The click-event of the fourth button. Moves on by one state.
 */
function step() {
	//step by one
	performStep();
	// enable stepping back
	ctrl.set(ctrl.MIDDLE);
}

/**
 * The click-event of the very right button. Moves on to the final state.
 */
function finish() {
	while (!done) {
		performStep();
	}
}

function performStep() {
	// store previous state
	prevState = state;

	//~STATEMACHINE~//
	stepCursor();
}

/**
 * Moves the cursor forward by one state.
 */
function stepCursor() {
	$(".cursor-current").removeClass("cursor-current");
	$(".cur-s" + state).addClass("cursor-current");
}

/**
 * Unbiased shuffle algorithm for arrays
 * https://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle
 * 
 * @param array
 *            the unshuffled array
 * @returns the shuffled array
 */
function shuffle(array) {
	var currentIndex = array.length;
	var temporaryValue;
	var randomIndex;

	// While there remain elements to shuffle...
	while (0 !== currentIndex) {

		// Pick a remaining element...
		randomIndex = Math.floor(Math.random() * currentIndex);
		currentIndex -= 1;

		// And swap it with the current element.
		temporaryValue = array[currentIndex];
		array[currentIndex] = array[randomIndex];
		array[randomIndex] = temporaryValue;
	}

	return array;
}

/**
 * Manages "disabled" attribute of the control buttons.
 */
function Controls() {
	this.BEGIN = 0;
	this.PLAY = 1;
	this.PAUSE = 2;
	this.MIDDLE = 4;
	this.END = 8;

	this.set = function(pos) {
		$("#btn-reset").prop("disabled", pos == this.BEGIN || pos == this.PLAY);
//		$("#btn-stepback").prop("disabled",
//				pos == this.BEGIN || pos == this.PLAY);
		$("#btn-play").prop("disabled", pos == this.END);
		$("#btn-step").prop("disabled", pos == this.END || pos == this.PLAY);
		$("#btn-finish").prop("disabled", pos == this.END || pos == this.PLAY);
	};
}

function Display() {
	/** Number of write operations */
	this.noOfWrites = 0;
	/** Number of compare operations */
	this.noOfCompares = 0;
	/** Number of other operations */
	this.noOfOps = 0;
	
	this.setValue = function(varName, value) {
		this.unHighlight();
		// update button value and color
		var btn = $("#btn-" + varName);
		btn.toggleClass("btn-default highlight-write");
		btn.prop("value", value);
		// update stats
		this.countWrite();
	};
	
	this.compare = function(varName1, varName2) {
		this.unHighlight();
		// update button value and color
		$("#btn-" + varName1).toggleClass("btn-default highlight-compare");
		$("#btn-" + varName2).toggleClass("btn-default highlight-compare");
		// update stats
		this.countCompare();
	};
	
	this.reset = function() {
		// delete highlights
		this.unHighlight();
		// reset counters
		this.noOfWrites = 0;
		this.noOfCompares = 0;
		this.noOfOps = 0;
		$("#btn-now").prop("value", "0");
		$("#btn-noc").prop("value", "0");
		$("#btn-noo").prop("value", "0");
	};
	
	// unhighlight currently active memory cell
	this.unHighlight = function() {
		$(".highlight-write").removeClass("highlight-write").addClass("btn-default");
		$(".highlight-compare").removeClass("highlight-compare").addClass("btn-default");
	};
	
	/**
	 * Increments the write operation counter and updates the counter
	 */
	this.countWrite = function() {
		var btn=$("#btn-now");
		btn.prop("value", ++this.noOfWrites);
		btn.toggleClass("btn-default highlight-write");
	};

	/**
	 * Increments the compare operation counter and updates the counter
	 */
	this.countCompare = function() {
		var btn=$("#btn-noc");
		btn.prop("value", ++this.noOfCompares);
		btn.toggleClass("btn-default highlight-write");
	};

	/**
	 * Increments the operation counter and updates the counter
	 */
	this.countOps = function() {
		var btn=$("#btn-noo");
		btn.prop("value", ++this.noOfOps);
		btn.toggleClass("btn-default highlight-write");
	};
}