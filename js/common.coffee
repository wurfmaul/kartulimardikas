###
Unbiased shuffle algorithm for arrays
https://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle

  @param array the unshuffled array
  @returns the shuffled array
###
window.shuffle = (array) ->
  currentIndex = array.length

  # While there remain elements to shuffle...
  while (currentIndex isnt 0)
    # Pick a remaining element...
    randomIndex = Math.floor(Math.random() * currentIndex--)

    # And swap it with the current element.
    [array[currentIndex], array[randomIndex]] = [array[randomIndex], array[currentIndex]]
  array

$ ->
  $('#generalAlertClose').click ->
    $('#generalAlert').hide('slow')

  $('#generalSuccessClose').click ->
    $('#generalSuccess').hide('slow')