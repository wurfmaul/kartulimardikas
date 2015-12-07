class window.Value
  execute: (player) ->
    throw new ExecutionError('could_not_execute_value', [value])

  @read: (source, player) ->
    switch (source.kind)
      when 'index'
        index = source.index.execute(player) + 0
        player.stats.readArrayVar(source.vid, index)
        value = player.memory.arrayGet(source.vid, index)
      when 'var'
        vid = source.vid
        player.stats.readVar(vid) # tell the stats, that a variable has been read
        value = player.memory.get(vid).value # return the current value of the variable
      else throw new ExecutionError('unknown_kind', [source.kind])

    if (value is window.defaults.init.no)
      throw new ExecutionError('var_not_initialized', [])

    value

  @write: (destination, value, player) ->
    switch (destination.kind)
      when 'index'
        index = destination.index.execute(player)
        player.memory.arraySet(destination.vid, index, value)
        player.stats.writeArrayVar(destination.vid, index, value)
      when 'var'
        player.memory.set(destination.vid, value)
        player.stats.writeVar(destination.vid, value)
      when 'const' then throw new ExecutionError('assign_to_const', [destination.value])
      when 'prop' then throw new ExecutionError('assign_to_prop', [])
      else throw new ExecutionError('unknown_kind', [destination.kind])

  @parse: (value, memory) ->
    value = $.trim(value)
    # check for constants
    if (constant = DataType.parse(value))
      return new ConstValue(constant.type, constant.value)

    # check for array ([])
    open = value.indexOf('[')
    close = value.lastIndexOf(']')
    if (open > -1 and close > open)
      vid = memory.find(value.substr(0, open))
      inner = @parse(value.substr(open + 1, close - open - 1), memory)
      if vid > -1 and inner?
        memory.count(vid)
        return new IndexValue(vid, inner)
      else return null

    # check for property (.length)
    period = value.indexOf('.')
    property = value.substr(period + 1)
    if (period > -1 and /^[A-Za-z]+$/.test(property))
      vid = memory.find(value.substr(0, period))
      if (vid > -1)
        memory.count(vid)
        return new PropValue(vid, property)
      else return null

    # check for variable name
    if (/^[A-Za-z]+$/.test(value))
      vid = memory.find(value)
      if (vid > -1)
        memory.count(vid)
        return new VarValue(vid)
      else return null

    value = value.replace(/\s*/g, '') # remove white spaces
    # check for simple computations (e.g. i+1)
    if (value.indexOf('(') is -1)
      split = value.split(/(-|\+|\*|\/|%|&|\|)/i)
      if (split.length is 3) # e.g. "i-1"
        left = @parse(split[0], memory)
        right = @parse(split[2], memory)
        if (left? and right? and "+-*/%&|".indexOf(split[1]) >= 0)
          return new CompValue(left, right, split[1])
        else return null

    # check for complex computations (using parenthesis)
    if (value = @parsePars(value))?
      switch (Object.keys(value).length)
        when 1 # unnecessary pars
          return @parse(value[0], memory)
        when 3 # binary
          left = @parse(value[0], memory)
          right = @parse(value[2], memory)
          op = value[1]
          if (left? and right? and "+-*/%&|".indexOf(value[1]) >= 0)
            return new CompValue(left, right, op)

    # return null, if value is not valid
    null

  ###
    Deals with complex binary expressions within parenthesis. It goes one level
    deep (say: not recursive). Returns an object with one value if it is just
    a simple expression within parenthesis. It the expression is more complex,
    it returns an object of size 3 that contains two expressions left, right along
    with the used operator.
  ###
  @parsePars: (value) ->
    level = 0
    result = {}
    index = 0
    split = value.split(/(-|\+|\*|\/|%|&|\||\(|\))/g)
    for i in [0...split.length]
      chunk = split[i]
      if (chunk is '') then continue

      if (level is 0)
        if (chunk is '(') then level++
        else if (chunk is ')') then level--
        else result[index++] = chunk
      else # level > 0
        if (chunk is '(') then level++
        else if (chunk is ')')
          if (--level is 0)
            index++
            continue
        if (result[index]?) then result[index] += chunk
        else result[index] = chunk

    if (level isnt 0)
      console.warn("Unbalanced expression!")
      null
    else
      result

class CompValue extends Value
  constructor: (@left, @right, @op) ->
    @kind = 'comp'

  execute: (player) ->
    leftVal = @left.execute(player)
    rightVal = @right.execute(player)
    player.stats.incArithmeticLogicOps()
    switch @op
      when '+' then leftVal + rightVal
      when '-' then leftVal - rightVal
      when '*' then leftVal * rightVal
      when '/'
        throw new ExecutionError('divide_by_zero', []) if (rightVal is 0)
        parseInt(leftVal / rightVal)
      when '%' then leftVal % rightVal
      when '&' then leftVal and rightVal
      when '|' then leftVal or rightVal
      else
        throw new ExecutionError('unknown_arithmetic_op', [@op])

  toJSON: -> {k:'e', l:@left.toJSON(), r:@right.toJSON(), o:@op}

class ConstValue extends Value
  constructor: (@type, @value) ->
    @kind = 'const'

  execute: (player) ->
    @value

  toJSON: -> {k:'c', t:@type, v:@value}

class IndexValue extends Value
  constructor: (@vid, @index) ->
    @kind = 'index'

  execute: (player) ->
    variable = player.memory.get(@vid)
    if (!variable.array)
      throw new ExecutionError('no_array_for_index', [variable.name])
    Value.read(@, player)

  toJSON: -> {k:'i', i:@vid, x:@index.toJSON()}

class PropValue extends Value
  constructor: (@vid, @prop) ->
    @kind = 'prop'
    @type = 'i'

  execute: (player) ->
    if (@prop is 'length')
      variable = player.memory.get(@vid)
      if (variable.array) then variable.value.split(',').length
      else 1
    else
      throw new ExecutionError('unknown_property', [@prop])

  toJSON: -> {k:'p', i:@vid, p:@prop}

class VarValue extends Value
  constructor: (@vid) ->
    @kind = 'var'

  execute: (player) ->
    Value.read(@, player)

  toJSON: -> {k: 'v', i:@vid}

class window.DataType
  @parse: (value) ->
# check for number
    intVal = parseInt(value)
    if (intVal + "" is value)
      return {type: 'i', value: intVal}

    # check for boolean
    boolVal = value.toLowerCase()
    if (boolVal is 'true' or boolVal is 'false')
      return {type: 'b', value: boolVal is 'true'}

    # check for character TODO: data-type char
    # if (/^'[A-Za-z]'$/.test(value))
    #   return {kind: 'const', type: 'char', value: value}

    false