split = (val) ->
  val.split(/,\s*/)

extractLast = (term) ->
  split(term).pop()

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

window.initVarInput = (elem) ->
  vars = []
  $('.varRow').not('#var-prototype').each(->
    vars.push($(this).data('name'))
  )
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

window.initValueInput = (elem) ->
  input = elem.find('.value')
  # destroy old instance of auto-completion
  if (input.autocomplete("instance")?)
    input.autocomplete("destroy")
  # init auto-completion
  input.autocomplete(
    delay: 0
    minLength: 0
    source: [elem.data('random'), elem.data('uninit')]
  ).click(->
    # open search with basic options
    $(this).autocomplete("search", "")
  ).focusout(->
    # collapse search, when losing focus
    $(this).autocomplete("close")
  )