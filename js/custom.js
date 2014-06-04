// activate tooltips
//$('button').tooltip();

$(function() {
    $("#insertVarsHere").html(varTemplate.dummyRow());
    $("#insertVarsHere").sortable({
	axis : "y",
	revert : true
    });
    $("#protoRow").html(varTemplate.rowEdit(maxVarId));
    $("#protoRow").disableSelection();
    $("#protoRow").draggable({
	axis : "y",
	connectToSortable : "#insertVarsHere",
	helper : "clone",
	revert : "invalid",
	stop : function(event, ui) {
	    varForm.checkForNewRow();
	}
    });
});


/**
 * Unbiased shuffle algorithm for arrays
 * https://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle
 * 
 * @param array
 *                the unshuffled array
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