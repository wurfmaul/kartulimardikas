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

updateUrl = (parameters) ->
  $.ajax "api/get-url.php",
    type: 'POST'
    data: {parameters: parameters}
    dataType: 'text'
    success: (url) =>
      # use HTML5 technology to manipulate the browser's address bar
      window.history.pushState(
        "", # state property (not used)
        "", # page title (not used)
        url # new url
      )
    error: (jqXHR, textStatus, errorThrown) => # if request failed
      @_printError("Request Error: " + errorThrown)

$ ->
  $('.panel-heading').click ->
    # change arrow
    $(this).find("span").toggleClass("glyphicon-chevron-right glyphicon-chevron-down")
    # toggle signal class
    $($(this).data("target")).toggleClass("closed")

    # prepare parameters for address bar manipulation
    section = 0
    counter = 1
    $('.panel-collapse').each(->
      section += counter if (not $(this).hasClass('closed'))
      counter *= 2
    )
    updateUrl(
      action: $('#action').data('val')
      aid: $('#aid').data('val')
      section: section
    )

  $('#generalAlertClose').click ->
    $('#generalAlert').hide('slow')

  $('#generalSuccessClose').click ->
    $('#generalSuccess').hide('slow')