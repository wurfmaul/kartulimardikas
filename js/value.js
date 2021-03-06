// Generated by CoffeeScript 1.10.0
(function() {
  var CompValue, ConstValue, IndexValue, PropValue, VarValue,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  window.Value = (function() {
    function Value() {}

    Value.prototype.execute = function(player) {
      throw new ExecutionError('could_not_execute_value', [value]);
    };

    Value.read = function(source, player) {
      var index, value, vid;
      switch (source.kind) {
        case 'index':
          index = source.index.execute(player) + 0;
          player.stats.readArrayVar(source.vid, index);
          value = player.memory.arrayGet(source.vid, index);
          break;
        case 'var':
          vid = source.vid;
          player.stats.readVar(vid);
          value = player.memory.get(vid).value;
          break;
        default:
          throw new ExecutionError('unknown_kind', [source.kind]);
      }
      if (value === window.defaults.init.no) {
        throw new ExecutionError('var_not_initialized', []);
      }
      return value;
    };

    Value.write = function(destination, value, player) {
      var index;
      switch (destination.kind) {
        case 'index':
          index = destination.index.execute(player);
          player.memory.arraySet(destination.vid, index, value);
          return player.stats.writeArrayVar(destination.vid, index, value);
        case 'var':
          player.memory.set(destination.vid, value);
          return player.stats.writeVar(destination.vid, value);
        case 'const':
          throw new ExecutionError('assign_to_const', [destination.value]);
          break;
        case 'prop':
          throw new ExecutionError('assign_to_prop', []);
          break;
        default:
          throw new ExecutionError('unknown_kind', [destination.kind]);
      }
    };

    Value.parse = function(value, memory) {
      var close, constant, inner, left, op, open, period, property, right, split, vid;
      value = $.trim(value);
      if ((constant = DataType.parse(value))) {
        return new ConstValue(constant.type, constant.value);
      }
      open = value.indexOf('[');
      close = value.lastIndexOf(']');
      if (open > -1 && close > open) {
        vid = memory.find(value.substr(0, open));
        inner = this.parse(value.substr(open + 1, close - open - 1), memory);
        if (vid > -1 && (inner != null)) {
          memory.count(vid);
          return new IndexValue(vid, inner);
        } else {
          return null;
        }
      }
      period = value.indexOf('.');
      property = value.substr(period + 1);
      if (period > -1 && /^[A-Za-z]+$/.test(property)) {
        vid = memory.find(value.substr(0, period));
        if (vid > -1) {
          memory.count(vid);
          return new PropValue(vid, property);
        } else {
          return null;
        }
      }
      if (/^[A-Za-z]+$/.test(value)) {
        vid = memory.find(value);
        if (vid > -1) {
          memory.count(vid);
          return new VarValue(vid);
        } else {
          return null;
        }
      }
      value = value.replace(/\s*/g, '');
      if (value.indexOf('(') === -1) {
        split = value.split(/(-|\+|\*|\/|%|&|\|)/i);
        if (split.length === 3) {
          left = this.parse(split[0], memory);
          right = this.parse(split[2], memory);
          if ((left != null) && (right != null) && "+-*/%&|".indexOf(split[1]) >= 0) {
            return new CompValue(left, right, split[1]);
          } else {
            return null;
          }
        }
      }
      if ((value = this.parsePars(value)) != null) {
        switch ((Object.keys(value).length)) {
          case 1:
            return this.parse(value[0], memory);
          case 3:
            left = this.parse(value[0], memory);
            right = this.parse(value[2], memory);
            op = value[1];
            if ((left != null) && (right != null) && "+-*/%&|".indexOf(value[1]) >= 0) {
              return new CompValue(left, right, op);
            }
        }
      }
      return null;
    };


    /*
      Deals with complex binary expressions within parenthesis. It goes one level
      deep (say: not recursive). Returns an object with one value if it is just
      a simple expression within parenthesis. It the expression is more complex,
      it returns an object of size 3 that contains two expressions left, right along
      with the used operator.
     */

    Value.parsePars = function(value) {
      var chunk, i, index, j, level, ref, result, split;
      level = 0;
      result = {};
      index = 0;
      split = value.split(/(-|\+|\*|\/|%|&|\||\(|\))/g);
      for (i = j = 0, ref = split.length; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
        chunk = split[i];
        if (chunk === '') {
          continue;
        }
        if (level === 0) {
          if (chunk === '(') {
            level++;
          } else if (chunk === ')') {
            level--;
          } else {
            result[index++] = chunk;
          }
        } else {
          if (chunk === '(') {
            level++;
          } else if (chunk === ')') {
            if (--level === 0) {
              index++;
              continue;
            }
          }
          if ((result[index] != null)) {
            result[index] += chunk;
          } else {
            result[index] = chunk;
          }
        }
      }
      if (level !== 0) {
        console.warn("Unbalanced expression!");
        return null;
      } else {
        return result;
      }
    };

    return Value;

  })();

  CompValue = (function(superClass) {
    extend(CompValue, superClass);

    function CompValue(left1, right1, op1) {
      this.left = left1;
      this.right = right1;
      this.op = op1;
      this.kind = 'comp';
    }

    CompValue.prototype.execute = function(player) {
      var leftVal, rightVal;
      leftVal = this.left.execute(player);
      rightVal = this.right.execute(player);
      player.stats.incArithmeticLogicOps();
      switch (this.op) {
        case '+':
          return leftVal + rightVal;
        case '-':
          return leftVal - rightVal;
        case '*':
          return leftVal * rightVal;
        case '/':
          if (rightVal === 0) {
            throw new ExecutionError('divide_by_zero', []);
          }
          return parseInt(leftVal / rightVal);
        case '%':
          return leftVal % rightVal;
        case '&':
          return leftVal && rightVal;
        case '|':
          return leftVal || rightVal;
        default:
          throw new ExecutionError('unknown_arithmetic_op', [this.op]);
      }
    };

    CompValue.prototype.toJSON = function() {
      return {
        k: 'e',
        l: this.left.toJSON(),
        r: this.right.toJSON(),
        o: this.op
      };
    };

    return CompValue;

  })(Value);

  ConstValue = (function(superClass) {
    extend(ConstValue, superClass);

    function ConstValue(type, value1) {
      this.type = type;
      this.value = value1;
      this.kind = 'const';
    }

    ConstValue.prototype.execute = function(player) {
      return this.value;
    };

    ConstValue.prototype.toJSON = function() {
      return {
        k: 'c',
        t: this.type,
        v: this.value
      };
    };

    return ConstValue;

  })(Value);

  IndexValue = (function(superClass) {
    extend(IndexValue, superClass);

    function IndexValue(vid1, index1) {
      this.vid = vid1;
      this.index = index1;
      this.kind = 'index';
    }

    IndexValue.prototype.execute = function(player) {
      var variable;
      variable = player.memory.get(this.vid);
      if (!variable.array) {
        throw new ExecutionError('no_array_for_index', [variable.name]);
      }
      return Value.read(this, player);
    };

    IndexValue.prototype.toJSON = function() {
      return {
        k: 'i',
        i: this.vid,
        x: this.index.toJSON()
      };
    };

    return IndexValue;

  })(Value);

  PropValue = (function(superClass) {
    extend(PropValue, superClass);

    function PropValue(vid1, prop) {
      this.vid = vid1;
      this.prop = prop;
      this.kind = 'prop';
      this.type = 'i';
    }

    PropValue.prototype.execute = function(player) {
      var variable;
      if (this.prop === 'length') {
        variable = player.memory.get(this.vid);
        if (variable.array) {
          return variable.value.split(',').length;
        } else {
          return 1;
        }
      } else {
        throw new ExecutionError('unknown_property', [this.prop]);
      }
    };

    PropValue.prototype.toJSON = function() {
      return {
        k: 'p',
        i: this.vid,
        p: this.prop
      };
    };

    return PropValue;

  })(Value);

  VarValue = (function(superClass) {
    extend(VarValue, superClass);

    function VarValue(vid1) {
      this.vid = vid1;
      this.kind = 'var';
    }

    VarValue.prototype.execute = function(player) {
      return Value.read(this, player);
    };

    VarValue.prototype.toJSON = function() {
      return {
        k: 'v',
        i: this.vid
      };
    };

    return VarValue;

  })(Value);

  window.DataType = (function() {
    function DataType() {}

    DataType.parse = function(value) {
      var boolVal, intVal;
      intVal = parseInt(value);
      if (intVal + "" === value) {
        return {
          type: 'i',
          value: intVal
        };
      }
      boolVal = value.toLowerCase();
      if (boolVal === 'true' || boolVal === 'false') {
        return {
          type: 'b',
          value: boolVal === 'true'
        };
      }
      return false;
    };

    return DataType;

  })();

}).call(this);
