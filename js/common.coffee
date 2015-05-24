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
  $.ajax("api/get-url.php",
    type: 'GET'
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
  )

toggleSection = (element, speed) ->
  # change arrow
  element.find("span").toggleClass("glyphicon-chevron-right glyphicon-chevron-down")
  $(element.data("target")).toggle(speed)

updateSectionUrl = ->
  # prepare parameters for address bar manipulation
  section = 0
  counter = 1
  $('.panel-heading').each(->
    section += counter if (not $(this).hasClass('collapsed'))
    counter *= 2
  )
  updateUrl(
    action: $('#action').data('val')
    aid: $('#aid').data('val')
    section: section
  )

$ ->
  $('.panel-heading').click ->
    $(this).toggleClass('collapsed')
    toggleSection($(this), 'slow')
    updateSectionUrl()
  $('.collapsed').each(->
    # the panels have to be displayed briefly in order to initialize the memory!
    toggleSection($(this), $(this).data('speed') ? 0)
  )
  $('#generalAlertClose').click -> $('#generalAlert').hide('slow')
  $('#generalSuccessClose').click -> $('#generalSuccess').hide('slow')