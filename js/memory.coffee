class window.Memory
  constructor: (@table) ->
    @memory = new Object()
    @original = new Object()
    @table.children().not('#var-prototype').each((index, element) =>
      vid = $(element).data('vid')
      name = $(element).data('name')
      value = $(element).data('value')
      array = $(element).data('type').charAt(0) is '['
      @memory[vid] =
        vid: vid, name: name, value: value, array: array, count: 0
      @original[vid] =
        vid: vid, name: name, value: value, array: array, count: 0
    )

  count: (vid) =>
    variable = @memory[vid]
    ++variable.count

  find: (name) =>
    vid = -1 # return a not-found-value
    $.each(@memory, (index, elem) ->
      vid = elem.vid if (elem.name is name)
    )
    vid

  get: (vid) =>
    @memory[vid]

  set: (vid, value) =>
    try
      value.split(',')
      @memory[vid].array = true
    catch error
      @memory[vid].array = false
    @memory[vid].value = value

  arrayCheck: (vid, index) =>
    variable = @get(vid)
    # check if the variable is an array
    if (!variable.array)
      throw new ExecutionError('no_array_for_index', [variable.name])
    array = variable.value.split(',')
    # check if the array is long enough
    if (index < 0 or array.length <= index)
      throw new ExecutionError('index_out_of_bounds', [variable.name, index, array.length])
    array

  arrayGet: (vid, index) =>
    array = @arrayCheck(vid, index)
    value = array[index]
    if (parseInt(value) + '' is value) then parseInt(value)
    else value

  arraySet: (vid, index, value) =>
    array = @arrayCheck(vid, index)
    array[index] = value
    @set(vid, array.join(','))

  reset: =>
    $.each(@original, (index, elem) =>
      @memory[index].value = elem.value
      @memory[index].count = 0
    )