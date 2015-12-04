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
window.initVarInput = (elem) ->
  # get all available variables
  vars = []
  $('.varRow').not('#var-prototype').each(->
    vars.push($(this).data('name'))
  )
  # the three options for variables: variable, array or property selection
  properties = ["", "[*]", ".length"]
  if (elem.autocomplete("instance")?)
    elem.autocomplete("destroy")
  elem.autocomplete(
    delay: 0
    minLength: 0
    source: (request, response) ->
      # the entered search term
      val = request.term
      if (val is "")
        # use var names for empty term
        @src = vars
      else if ($.inArray(val, vars) > -1)
        # use var operations if a variable name was typed/selected
        newSrc = []
        $.each(properties, (i, elem) ->
          newSrc.push(
            value: val + elem
            label: val + elem
            variable: val
          )
        )
        @src = newSrc
      # try to find a match in the array of possible matches
      matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i")
      response($.grep(@src, (value)->
        value = value.label || value.value || value
        matcher.test(value)
      ))
    select: (event, ui) ->
      val = ui.item.variable ? ui.item.label
      $(this).autocomplete("search", val)
  ).click(->
    # open search with basic options
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
      init.val('C')

    # show/hide type selection
    type = elem.find('.type-group')
    size = elem.find('.size-group')
    if (target? and target is '.type')
      # show type option
      typeInput = type.show('slow').find('.type').focus()
      # deal with size option
      if (typeInput.val().charAt(0) is '[') then size.show('slow')
      else size.hide('slow')
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
  ).click(->
    # open search with basic options
    $(this).autocomplete("search", "")
  ).focusout(->
    # collapse search, when losing focus
    $(this).autocomplete("close")
  )