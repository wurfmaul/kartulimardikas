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
    )

  @performToggle = (element) ->
    # collapse/expand panel
    @toggleSection(element, 'slow')
    # compute new section number
    sectionNumber = @panelsToSectionNumber(element)
    # broadcast to other scopes
    @sectionNumberToPanels(sectionNumber)
    # change the browser's url to new section number
    @updateUrl(
      params: window.current.parameters
      section: sectionNumber
    )
    # store the new section number to the browser's local storage
    @storeSectionNumber(sectionNumber)

  ###
    Collapses a section
  ###
  @hideSection = (element, speed) ->
    # change arrow
    element.find(".glyphicon").removeClass("glyphicon-chevron-down").addClass('glyphicon-chevron-right')
    # expand/collapse body
    element.siblings('.panel-collapse').hide(speed)
    # mark heading as collapsed
    $(element).addClass('collapsed')

  ###
   Expands a section
  ###
  @showSection = (element, speed) ->
    # change arrow
    element.find(".glyphicon").removeClass("glyphicon-chevron-right").addClass('glyphicon-chevron-down')
    # expand/collapse body
    element.siblings('.panel-collapse').show(speed)
    # mark heading as collapsed
    $(element).removeClass('collapsed')

  ###
    Collapses/Expands a section.
  ###
  @toggleSection = (element, speed) ->
    # change arrow
    element.find(".glyphicon").toggleClass("glyphicon-chevron-right glyphicon-chevron-down")
    # expand/collapse body
    element.siblings('.panel-collapse').toggle(speed)
    # mark heading as collapsed
    $(element).toggleClass('collapsed')

  ###
    Computes a section number from expansion state of panels.
  ###
  @panelsToSectionNumber = (element) ->
    section = 0
    counter = 1
    element.closest('.scope').find('.panel-heading').each(->
      section += counter if (not $(this).hasClass('collapsed'))
      counter *= 2
    )
    section

  ###
    Expands/collapses panels according to the section number.
  ###
  @sectionNumberToPanels = (sectionNumber) ->
    $('.scope').each(->
      panels = $(this).find('.panel-heading')
      panelNumber = panels.length - 1
      sec = sectionNumber
      # show all sections
      Section.showSection(panels, 0)
      while (sec >= 0 and panelNumber >= 0)
        code = Math.pow(2, panelNumber)
        if (sec >= code)
          sec -= code
        else
          panel = $(panels[panelNumber])
          Section.hideSection(panel, 0)
        panelNumber--
    )

  ###
    Extract the correct section number
  ###
  @computeSectionNumber = ->
    if ((section = window.current.section)?)
      # first priority: section is set by parameter
      section
    else if (localStorage and (section = localStorage.getItem('section_' + window.current.action))?)
      # second priority: value in the browser's local storage
      section
    else
      # third priority: default value
      window.defaults.section

  ###
    Stores a section number into the browser's local storage.
  ###
  @storeSectionNumber = (sectionNumber) ->
    localStorage.setItem('section_' + window.current.action, sectionNumber)

# perform collapsing according to section number
Section.init()

$ ->
  $('.panel-heading').click -> Section.performToggle($(this))