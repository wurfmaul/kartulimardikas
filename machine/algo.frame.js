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
/** Number of write operations */
var noOfWrites = 0;
/** Number of compare operations */
var noOfCompares = 0;
/** Number of other operations */
var noOfOps = 0;
/** The id of the timer as reference */
var playTimer;
/** Indicates whether the algorithm is finished */
var done = false;
/** Manages the control panels */
var ctrl = new Controls();

/**
 * The click-event of the very left button. Sets algorithm back to the
 * beginning.
 */
function reset() {
	// disable automatic stepping
	pause();
	// reset variables
	//~VARRESET~//

	// reset counters
	noOfWrites = 0;
	noOfCompares = 0;
	noOfOps = 0;
	$("#btn-now").prop("value", "0");
	$("#btn-noc").prop("value", "0");
	$("#btn-noo").prop("value", "0");

	// reset cursor
	for (var i = 0; i < STATES; i++) {
		$(".cur-s" + i).removeClass("cursor-current");
	}
	$(".cur-s0").addClass("cursor-current");
	
	// reset state
	state = 0;
	// reset result
	done = false;
	// reset controls
	ctrl.set(ctrl.BEGIN);
}

/**
 * The click-event of the second button. Moves back by one step.
 */
function stepback() {
	pause();
	// TODO stepback
}

/**
 * The click-event of the center button if it's not currently running. Steps
 * slowly to the final state.
 */
function play() {
	// enable automatic stepping
	playTimer = setInterval(step, TIMEOUT);
	// transform play-button to pause-button
	$("#btn-play").prop("title", "Pause");
	// $("#btn-play").click(function() { pause(); });
	document.getElementById("btn-play").setAttribute("onclick", "pause()");
	$("#img-play").removeClass("glyphicon-play");
	$("#img-play").addClass("glyphicon-pause");
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
	// $("#btn-play").click(function() { play(); });
	document.getElementById("btn-play").setAttribute("onclick", "play()");
	$("#img-play").removeClass("glyphicon-pause");
	$("#img-play").addClass("glyphicon-play");
	// enable manual stepping
	ctrl.set(ctrl.PAUSE);
}

/**
 * The click-event of the fourth button. Moves on by one state.
 */
function step() {
	// store previous state
	prevState = state;

	//~STATEMACHINE~//
	stepCursor();
}

/**
 * The click-event of the very right button. Moves on to the final state.
 */
function finish() {
	while (!done) {
		step();
	}
}

/**
 * Moves the cursor forward by one state.
 */
function stepCursor() {
	// remove cursor from previous step
	$(".cur-s" + prevState).toggleClass("cursor-current");
	// add cursor to new state
	$(".cur-s" + state).toggleClass("cursor-current");
}

/**
 * Increments the write operation counter and updates the counter
 */
function countWrite() {
	$("#btn-now").prop("value", ++noOfWrites);
}

/**
 * Increments the compare operation counter and updates the counter
 */
function countCompare() {
	$("#btn-noc").prop("value", ++noOfCompares);
}

/**
 * Increments the operation counter and updates the counter
 */
function countOps() {
	$("#btn-noo").prop("value", ++noOfOps);
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
	this.END = 3;

	this.set = function(pos) {
		$("#btn-reset").prop("disabled", pos == this.BEGIN || pos == this.PLAY);
		$("#btn-stepback").prop("disabled",
				pos == this.BEGIN || pos == this.PLAY);
		$("#btn-play").prop("disabled", pos == this.END);
		$("#btn-step").prop("disabled", pos == this.END || pos == this.PLAY);
		$("#btn-finish").prop("disabled", pos == this.END || pos == this.PLAY);
	};
}