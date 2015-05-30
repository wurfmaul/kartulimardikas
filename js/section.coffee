###
  Deals with sections (used in edit/view)
###
class Section
  @init = ->
    @sectionNumberToPanels(@computeSectionNumber())

  ###
    Changes the url in the address bar according to the new parameters.
  ###
  @updateUrl = (parameters) ->
    $.ajax("api/url.php",
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

  @performToggle = (element) ->
    # collapse/expand panel
    @toggleSection(element, 'slow')
    # compute new section number
    sectionNumber = @panelsToSectionNumber()
    # change the browser's url to new section number
    @updateUrl(
      action: window.defaults.action
      aid: window.defaults.aid
      section: sectionNumber
    )
    # store the new section number to the browser's local storage
    @storeSectionNumber(sectionNumber)

  ###
    Collapses/Expands a section.
  ###
  @toggleSection = (element, speed) ->
    element.find("span").toggleClass("glyphicon-chevron-right glyphicon-chevron-down") # change arrow
    $(element.data("target")).toggle(speed) # expand/collapse body
    $(element).toggleClass('collapsed') # mark heading as collapsed

  ###
    Computes a section number from expansion state of panels.
  ###
  @panelsToSectionNumber = ->
    section = 0
    counter = 1
    $('.panel-heading').each(->
      section += counter if (not $(this).hasClass('collapsed'))
      counter *= 2
    )
    section

  ###
    Expands/collapses panels according to the section number.
  ###
  @sectionNumberToPanels = (sectionNumber) ->
    panels = $('.panel-heading')
    panelNumber = panels.length - 1
    while (sectionNumber >= 0 and panelNumber >= 0)
      code = Math.pow(2, panelNumber)
      if (sectionNumber >= code)
        sectionNumber -= code
      else
        panel = $(panels[panelNumber])
        # some panels have to be displayed briefly in order to initialize (e.g. the memory)!
        @toggleSection(panel, panel.data('speed') ? 0)
      panelNumber--

  ###
    Extract the correct section number
  ###
  @computeSectionNumber = ->
    if ((section = window.defaults.section)?)
      # first instance: section is set by parameter
      section
    else if (localStorage and (section = localStorage.getItem('section_' + window.defaults.action))?)
      # second instance: value in the browser's local storage
      section
    else
      # third instance: default value
      window.defaults.sectionDefault

  ###
    Stores a section number into the browser's local storage.
  ###
  @storeSectionNumber = (sectionNumber) ->
    localStorage.setItem('section_' + window.defaults.action, sectionNumber)

# perform collapsing according to section number
Section.init()

$ ->
  $('.panel-heading').click -> Section.performToggle($(this))