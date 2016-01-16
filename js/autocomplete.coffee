split = (val) ->
  val.split(/,\s*/)

extractLast = (term) ->
  split(term).pop()

###
  Prepare combo boxes for tags.
###
window.initTagInput = (elem) ->
  availableTags = [];
  $(elem).autocomplete(
    minLength: 0
    source: (request, response) ->
      if (availableTags.length is 0)
        $.getJSON("api/tag.php", ( data, status, xhr ) ->
          availableTags = data.tags
          response($.ui.autocomplete.filter(
            availableTags, extractLast(request.term)
          ))
        )
      else
        # delegate back to autocomplete, but extract the last term
        response($.ui.autocomplete.filter(
          availableTags, extractLast(request.term)
        ))
    focus: ->
      # prevent value inserted on focus
      false
    select: (event, ui) ->
      terms = split(@value)
      # remove the current input
      terms.pop()
      # add the selected item
      terms.push(ui.item.value)
      # add placeholder to get the comma-and-space at the end
      terms.push("")
      @value = terms.join(", ")
      false
  );

###
  Prepare combo boxes for function search
###
window.initFuncInput = (elem) ->
  if (elem.autocomplete("instance")?)
    elem.autocomplete("destroy")
  elem.autocomplete(
    delay: 0
    minLength: 1
    source: (request, response) ->
      $.getJSON( "api/list.php?query=" + request.term, {
        term: extractLast( request.term )
      }, response );
    select: (event, ui) ->
      $(this).val(ui.item.name)
      $(this).closest('.function-node').data('callee-id', ui.item.value)
      false
  ).focusout(->
    # collapse search, when losing focus
    $(this).autocomplete("close")
  )

###
  Prepare combo boxes for variable selection
###
window.initVarSearch = () ->
  # get all available variables
  vars = []
  $('.varRow').not('#var-prototype').each(->
    vars.push($(this).data('name'))
  )
  # the three options for variables: variable, array or property selection
  properties = ["", "[*]", ".length"]

  window.varSearch = (request, response, elem) ->
    cursor = elem[0].selectionStart
    toCursor = request.term.substr(0, cursor)
    fromCursor = request.term.substr(cursor)

    # the entered search term split up
    start = toCursor.search(/\w+$/)
    toStart = request.term.substr(0, start)
    term = toCursor.match(/\w+$/)?[0] ? ''
    console.log('term: ' + term)

    if ($.inArray(term, vars) > -1)
      # use var operations if a variable name was typed/selected
      newSrc = []
      $.each(properties, (i, elem) ->
        newSrc.push(
          value: toStart + term + elem + fromCursor
          label: term + elem
          variable: term
        )
      )
      @src = newSrc
    else
      # use var names for other terms
      newSrc = []
      $.each(vars, (i, elem) ->
        newSrc.push(
          value: toStart + elem + fromCursor
          label: elem
          variable: elem
        )
      )
      @src = newSrc

    # try to find a match in the array of possible matches
    matcher = new RegExp($.ui.autocomplete.escapeRegex(term), "i")
    response($.grep(@src, (value)->
      value = value.label || value.value || value
      matcher.test(value)
    ))
window.initVarInput = (elem) ->
  # deactivate old combo box
  if (elem.autocomplete("instance")?)
    elem.autocomplete("destroy")
  # create new combo box
  elem.autocomplete(
    delay: 0
    minLength: 0
    source: (request, response) ->
      window.varSearch(request, response, elem)
    select: (event, ui) ->
      val = ui.item.variable ? ui.item.label
      $(this).autocomplete("search", val)
  ).click( (e) ->
    # open search with basic options
    e.preventDefault()
    $(this).autocomplete("search", $(this).val())
  ).focusout(->
    # collapse search, when losing focus
    $(this).autocomplete("close")
  )

###
  Prepare combo boxes for variable initialization
###
window.initValueInput = (elem) ->
  init = elem.find('.init')
  input = elem.find('.value')
  # compute drop-down values
  source = []
  init.find('.combo-box').each(->
    source.push(
      value: $(this).val()
      label: $(this).text()
      target: $(this).data('target')
    )
  )
  input.blur(->
    # select right initialization type in select .init
    text = $(this).val()
    comboVal = false
    target = null
    $.each(source, (i, elem) ->
      if (text is elem.label)
        init.val(elem.value)
        comboVal = true
        target = elem.target
    )
    if (!comboVal)
      init.val(window.defaults.init.custom)

    # show/hide type selection
    type = elem.find('.type-group')
    size = elem.find('.size-group')
    if (target? and target is '.type')
      # show type option
      typeInput = type.show('slow').find('.type').focus()
      # deal with size option
      if (typeInput.val().charAt(0) is window.defaults.type.array)
        size.show('slow')
      else
        size.hide('slow')
    else
      # hide type and size option
      type.hide('slow')
      size.hide('slow')
      input.focusout()
  )
  # destroy old instance of auto-completion
  if (input.autocomplete("instance")?)
    input.autocomplete("destroy")
  # init auto-completion
  input.autocomplete(
    delay: 0
    minLength: 0
    source: source
    select: (event, ui) ->
      event.preventDefault()
      input.val(ui.item.label)
      input.blur()
  ).click( (e) ->
    # open search with basic options
    e.preventDefault()
    $(this).autocomplete("search", "")
  ).focusout(->
    # collapse search, when losing focus
    $(this).autocomplete("close")
  )