// Generated by CoffeeScript 1.10.0
(function() {
  var extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  window.Node = (function() {
    function Node() {}


    /*
      Must be overridden by all subclasses.
     */

    Node.prototype.execute = function(player, node) {
      throw new Error('Node must override execute() method!');
    };


    /*
      Sets the cursor to the position of the node. Override to place it somewhere else!
     */

    Node.prototype.mark = function(player, node) {
      if (node === this.nid) {
        player.setCursor(this.nid, 0);
        return this.nid;
      } else {
        return -1;
      }
    };


    /*
      Must be overridden by all subclasses.
     */

    Node.prototype.toJSON = function() {
      throw new Error('Node must override toJSON() method!');
    };


    /*
      Delegates the parse function to the right subclass.
      Must be overridden by all subclasses.
     */

    Node.parse = function(node, tree, memory) {
      var type;
      type = node.data('node-type');
      switch (type) {
        case 'assign':
          return AssignNode.parse(node, tree, memory);
        case 'comment':
          return CommentNode.parse(node, tree, memory);
        case 'compare':
          return CompareNode.parse(node, tree, memory);
        case 'function':
          return FunctionNode.parse(node, tree, memory);
        case 'if':
          return IfNode.parse(node, tree, memory);
        case 'inc':
          return IncNode.parse(node, tree, memory);
        case 'return':
          return ReturnNode.parse(node, tree, memory);
        case 'swap':
          return SwapNode.parse(node, tree, memory);
        case 'value':
          return ValueNode.parse(node, tree, memory);
        case 'while':
          return WhileNode.parse(node, tree, memory);
        default:
          throw new Error("Parse error: unknown type: '" + type + "'");
      }
    };


    /*
     * For combo boxes: Inspects the given value and defines its kind
     * and properties.
     */

    Node.parseAndCheckValue = function(_class, node, memory) {
      var value;
      node = this.findSubNode(node, _class);
      value = Value.parse(node.val(), memory);
      if ((value == null) || node.val() === '') {
        node.addClass('error');
      } else {
        node.removeClass('error');
      }
      return value;
    };

    Node.parseAndCheckVar = function(_class, node, memory) {
      var value;
      node = this.findSubNode(node, _class);
      value = Value.parse(node.val(), memory);
      if ((value != null) && (value.kind === 'var' || value.kind === 'index')) {
        node.removeClass('error');
        return value;
      } else {
        node.addClass('error');
        return null;
      }
    };


    /*
     * Returns node's first sub-node of class _class.
     */

    Node.findSubNode = function(node, _class) {
      return node.find(_class + ':first');
    };

    Node.validate = function(node, check) {
      var flag;
      flag = node.find('.invalid-flag:first');
      if (check) {
        node.removeClass('invalid');
        return flag.hide();
      } else {
        node.addClass('invalid');
        return flag.show();
      }
    };

    return Node;

  })();

  window.AssignNode = (function(superClass) {
    extend(AssignNode, superClass);

    function AssignNode(nid1, to1, fromNode1, fromVal1) {
      this.nid = nid1;
      this.to = to1;
      this.fromNode = fromNode1;
      this.fromVal = fromVal1;
    }

    AssignNode.prototype.execute = function(player, node) {
      var from, value;
      if (this.fromNode.size()) {
        from = this.fromNode.execute(player, 0);
        if ((from.scope != null)) {
          return from;
        }
        value = from.value;
      } else {
        value = this.fromVal.execute(player);
      }
      Value.write(this.to, value, player);
      return {
        value: value
      };
    };

    AssignNode.prototype.mark = function(player, node) {
      if ((node === this.nid || node === this.fromNode.nid)) {
        node = this.nid;
      }
      return AssignNode.__super__.mark.call(this, player, node);
    };

    AssignNode.prototype.toJSON = function() {
      var ref, ref1;
      return {
        i: this.nid,
        n: 'as',
        t: (ref = this.to) != null ? ref.toJSON() : void 0,
        f: this.fromNode.nid,
        v: (ref1 = this.fromVal) != null ? ref1.toJSON() : void 0
      };
    };

    AssignNode.parse = function(node, tree, memory) {
      var fromNode, fromVal, nid, to;
      to = AssignNode.parseAndCheckVar('.assign-to', node, memory);
      fromNode = BlockNode.parse(AssignNode.findSubNode(node, '.assign-from'), tree, memory);
      tree.push(fromNode);
      fromVal = AssignNode.parseAndCheckValue('.assign-from-val', node, memory);
      AssignNode.validate(node, (to != null) && (fromNode.size() || (fromVal != null)));
      nid = tree.length;
      return new AssignNode(nid, to, fromNode, fromVal);
    };

    return AssignNode;

  })(Node);

  window.BlockNode = (function(superClass) {
    extend(BlockNode, superClass);

    function BlockNode(nid1, nodes1) {
      this.nid = nid1;
      this.nodes = nodes1;
      this.curNode = 0;
    }

    BlockNode.prototype.execute = function(player, node) {
      var curNode, i, j, len, n, ref, value;
      curNode = null;
      ref = this.nodes;
      for (i = j = 0, len = ref.length; j < len; i = ++j) {
        n = ref[i];
        if (node <= n) {
          curNode = player.tree.get(n).execute(player, node);
          break;
        }
      }
      if (((curNode != null ? curNode.value : void 0) != null)) {
        value = curNode.value;
      } else {
        value = false;
      }
      if (((curNode != null ? curNode.scope : void 0) != null)) {
        return curNode;
      } else if (((curNode != null ? curNode.next : void 0) != null)) {
        return {
          next: curNode.next,
          value: value
        };
      } else if (curNode === -1) {
        return {
          next: -1,
          value: value
        };
      } else if (this.nodes.length > i + 1) {
        return {
          next: this.nodes[i + 1],
          value: value
        };
      } else {
        return {
          value: value
        };
      }
    };

    BlockNode.prototype.executeAll = function(player, node, combine) {
      var all, curValue, i, j, len, n, next, ref, value;
      all = combine === window.defaults.execute.all;
      value = all;
      next = -1;
      ref = this.nodes;
      for (i = j = 0, len = ref.length; j < len; i = ++j) {
        n = ref[i];
        if (node < n) {
          next = n;
          break;
        } else {
          curValue = player.tree.get(n).execute(player, n).value;
          if (all) {
            value = value && curValue;
          } else {
            value = value || curValue;
          }
          if (window.defaults.shortCircuit && !value) {
            break;
          }
        }
      }
      return {
        value: value,
        next: next
      };
    };


    /*
    Compute the values of all the contained nodes and store them into an array.
     */

    BlockNode.prototype.evaluateAll = function(player) {
      var curValue, j, len, n, ref, value;
      value = [];
      ref = this.nodes;
      for (j = 0, len = ref.length; j < len; j++) {
        n = ref[j];
        curValue = player.tree.get(n).execute(player, n).value;
        value.push(curValue);
      }
      return value;
    };

    BlockNode.prototype.mark = function(player, node) {
      var i, j, k, len, len1, marked, n, ref, ref1;
      if (node === this.nid) {
        ref = this.nodes;
        for (i = j = 0, len = ref.length; j < len; i = ++j) {
          n = ref[i];
          marked = player.tree.get(n).mark(player, n);
          if (marked > -1) {
            return marked;
          }
        }
        return -1;
      } else {
        ref1 = this.nodes;
        for (i = k = 0, len1 = ref1.length; k < len1; i = ++k) {
          n = ref1[i];
          if (node <= n) {
            marked = player.tree.get(n).mark(player, node);
            if (marked > -1) {
              return marked;
            } else {
              node = n + 1;
            }
          }
        }
      }
      return -1;
    };

    BlockNode.prototype.size = function() {
      return this.nodes.length;
    };

    BlockNode.prototype.toJSON = function() {
      return {
        i: this.nid,
        n: 'bk',
        c: this.nodes
      };
    };

    BlockNode.parse = function(node, tree, memory) {
      var nid, nodes;
      nodes = [];
      node.children('li.node').each(function(index, element) {
        var child;
        child = Node.parse($(element), tree, memory);
        tree.push(child);
        return nodes[index] = child.nid;
      });
      nid = tree.length;
      return new BlockNode(nid, nodes);
    };

    return BlockNode;

  })(Node);

  window.CommentNode = (function(superClass) {
    extend(CommentNode, superClass);

    function CommentNode(nid1, comment1) {
      this.nid = nid1;
      this.comment = comment1;
    }

    CommentNode.prototype.execute = function(player, node) {
      return {
        value: false
      };
    };

    CommentNode.prototype.mark = function(player, node) {
      return -1;
    };

    CommentNode.prototype.toJSON = function() {
      return {
        i: this.nid,
        n: 'cm',
        c: this.comment
      };
    };

    CommentNode.parse = function(node, tree, memory) {
      var comment, nid;
      comment = CommentNode.findSubNode(node, '.comment-text').val();
      nid = tree.length;
      return new CommentNode(nid, comment);
    };

    return CommentNode;

  })(Node);

  window.CompareNode = (function(superClass) {
    extend(CompareNode, superClass);

    function CompareNode(nid1, left1, right1, operator1) {
      this.nid = nid1;
      this.left = left1;
      this.right = right1;
      this.operator = operator1;
    }

    CompareNode.prototype.execute = function(player, node) {
      var leftVal, rightVal;
      leftVal = this.left.execute(player);
      rightVal = this.right.execute(player);
      player.stats.incCompareOps();
      switch (this.operator) {
        case 'le':
          return {
            value: leftVal <= rightVal
          };
        case 'lt':
          return {
            value: leftVal < rightVal
          };
        case 'eq':
          return {
            value: leftVal === rightVal
          };
        case 'gt':
          return {
            value: leftVal > rightVal
          };
        case 'ge':
          return {
            value: leftVal >= rightVal
          };
        case 'ne':
          return {
            value: leftVal !== rightVal
          };
        default:
          throw new Error("CompareNode: unknown operator: '" + this.operator + "'!");
      }
    };

    CompareNode.prototype.toJSON = function() {
      var ref, ref1;
      return {
        i: this.nid,
        n: 'cp',
        l: (ref = this.left) != null ? ref.toJSON() : void 0,
        r: (ref1 = this.right) != null ? ref1.toJSON() : void 0,
        o: this.operator
      };
    };

    CompareNode.parse = function(node, tree, memory) {
      var left, nid, operator, right;
      left = CompareNode.parseAndCheckValue('.compare-left', node, memory);
      right = CompareNode.parseAndCheckValue('.compare-right', node, memory);
      CompareNode.validate(node, (left != null) && (right != null));
      operator = CompareNode.findSubNode(node, '.compare-operation').val();
      nid = tree.length;
      return new CompareNode(nid, left, right, operator);
    };

    return CompareNode;

  })(Node);

  window.FunctionNode = (function(superClass) {
    extend(FunctionNode, superClass);

    function FunctionNode(nid1, callee1, paramsLine1, params1) {
      this.nid = nid1;
      this.callee = callee1;
      this.paramsLine = paramsLine1;
      this.params = params1;
    }

    FunctionNode.prototype.execute = function(player, node) {
      var curNode, params, scope, value;
      scope = player.scope;
      curNode = $('#scope-' + scope + ' .node_' + this.nid);
      if ((curNode.data('return-value') != null)) {
        value = Value.parse(curNode.data('return-value'), player.memory);
        curNode.removeData('return-value');
        return value;
      }
      if (this.params.size()) {
        params = this.params.evaluateAll(player);
      } else if ((this.paramsLine != null)) {
        params = [this.paramsLine.execute(player)];
      } else {
        params = [];
      }
      throw {
        type: 'function-call',
        node: this.nid,
        callee: this.callee,
        scope: player.scope + 1,
        params: params
      };
    };

    FunctionNode.prototype.mark = function(player, node) {
      if ((node === this.nid || node === this.params.nid)) {
        node = this.nid;
      }
      return FunctionNode.__super__.mark.call(this, player, node);
    };

    FunctionNode.prototype.toJSON = function() {
      var ref;
      return {
        i: this.nid,
        n: 'ft',
        c: this.callee,
        l: (ref = this.paramsLine) != null ? ref.toJSON() : void 0,
        p: this.params.nid
      };
    };

    FunctionNode.parse = function(node, tree, memory) {
      var callee, nid, par, params, paramsLine, paramsLineError, paramsRaw;
      callee = node.data('callee-id');
      paramsLine = null;
      paramsRaw = FunctionNode.findSubNode(node, '.act-pars-line').val();
      paramsLineError = false;
      if (paramsRaw !== '') {
        par = Value.parse(paramsRaw, memory);
        if (par == null) {
          paramsLineError = true;
        } else {
          paramsLine = par;
        }
      }
      params = BlockNode.parse(FunctionNode.findSubNode(node, '.act-pars'), tree, memory);
      tree.push(params);
      FunctionNode.validate(node, callee > 0 && !paramsLineError);
      nid = tree.length;
      return new FunctionNode(nid, callee, paramsLine, params);
    };

    return FunctionNode;

  })(Node);

  window.IfNode = (function(superClass) {
    extend(IfNode, superClass);

    function IfNode(nid1, condition1, ifBody1, elseBody1, op1) {
      this.nid = nid1;
      this.condition = condition1;
      this.ifBody = ifBody1;
      this.elseBody = elseBody1;
      this.op = op1;
    }

    IfNode.prototype.execute = function(player, node) {
      var cond, condRetVal, size, value;
      if (node <= this.condition) {
        cond = player.tree.get(this.condition);
        size = cond.size();
        if (size === 1) {
          condRetVal = cond.execute(player, node);
        } else if (size > 1) {
          condRetVal = cond.executeAll(player, node, this.op);
        } else {
          throw new ExecutionError('no_condition', []);
        }
        value = condRetVal.value + 0 !== 0;
        if (condRetVal.next > -1) {
          return {
            next: condRetVal.next,
            value: value
          };
        } else if (value) {
          return {
            next: this.ifBody,
            value: value
          };
        } else {
          return {
            next: this.elseBody,
            value: value
          };
        }
      } else if (node <= this.ifBody) {
        return player.tree.get(this.ifBody).execute(player, node);
      } else {
        return player.tree.get(this.elseBody).execute(player, node);
      }
    };

    IfNode.prototype.mark = function(player, node) {
      if (node === this.nid) {
        return player.tree.get(this.condition).mark(player, this.condition);
      } else if (node <= this.condition) {
        return player.tree.get(this.condition).mark(player, node);
      } else if (node <= this.ifBody) {
        return player.tree.get(this.ifBody).mark(player, node);
      } else {
        return player.tree.get(this.elseBody).mark(player, node);
      }
    };

    IfNode.prototype.toJSON = function() {
      return {
        i: this.nid,
        n: 'if',
        c: this.condition,
        b: this.ifBody,
        e: this.elseBody,
        o: this.op
      };
    };

    IfNode.parse = function(node, tree, memory) {
      var condition, elseBody, ifBody, nid, op, size;
      condition = BlockNode.parse(IfNode.findSubNode(node, '.if-condition'), tree, memory);
      tree.push(condition);
      ifBody = BlockNode.parse(IfNode.findSubNode(node, '.if-body'), tree, memory);
      tree.push(ifBody);
      elseBody = BlockNode.parse(IfNode.findSubNode(node, '.if-else'), tree, memory);
      tree.push(elseBody);
      size = condition.size();
      IfNode.validate(node, size > 0);
      op = IfNode.findSubNode(node, '.if-operator');
      if (size > 1) {
        op.show();
      } else {
        op.hide();
      }
      nid = tree.length;
      return new IfNode(nid, condition.nid, ifBody.nid, elseBody.nid, op.val());
    };

    return IfNode;

  })(Node);

  window.IncNode = (function(superClass) {
    extend(IncNode, superClass);

    function IncNode(nid1, variable1, operator1) {
      this.nid = nid1;
      this.variable = variable1;
      this.operator = operator1;
    }

    IncNode.prototype.execute = function(player, node) {
      var index, newValue, value, vid;
      vid = this.variable.vid;
      value = this.variable.execute(player);
      switch (this.operator) {
        case 'i':
          newValue = value + 1;
          break;
        case 'd':
          newValue = value - 1;
          break;
        default:
          throw new Error('IncNode: invalid operator ' + this.operator);
      }
      if (this.variable.kind === 'index') {
        index = this.variable.index.execute(player);
        player.memory.arraySet(vid, index, newValue);
        player.stats.writeArrayVar(vid, index, newValue);
      } else {
        player.memory.set(vid, newValue);
        player.stats.writeVar(vid, newValue);
      }
      return {
        value: value
      };
    };

    IncNode.prototype.toJSON = function() {
      var ref;
      return {
        i: this.nid,
        n: 'ic',
        v: (ref = this.variable) != null ? ref.toJSON() : void 0,
        o: this.operator
      };
    };

    IncNode.parse = function(node, tree, memory) {
      var nid, operator, variable;
      variable = IncNode.parseAndCheckVar('.inc-var', node, memory);
      IncNode.validate(node, variable != null);
      operator = IncNode.findSubNode(node, '.inc-operation').val();
      nid = tree.length;
      return new IncNode(nid, variable, operator);
    };

    return IncNode;

  })(Node);

  window.ReturnNode = (function(superClass) {
    extend(ReturnNode, superClass);

    function ReturnNode(nid1, retVal1, retNode1) {
      this.nid = nid1;
      this.retVal = retVal1;
      this.retNode = retNode1;
    }

    ReturnNode.prototype.execute = function(player, node) {
      var ret, value;
      if (this.retNode.size()) {
        ret = this.retNode.execute(player, 0);
        if ((ret.scope != null)) {
          return ret;
        }
        value = ret.value;
      } else {
        value = this.retVal.execute(player);
      }
      $('#scope-' + player.scope + ' .return-value').val(value).focus();
      return -1;
    };

    ReturnNode.prototype.mark = function(player, node) {
      if ((node === this.nid || node === this.retNode.nid)) {
        node = this.nid;
      }
      return ReturnNode.__super__.mark.call(this, player, node);
    };

    ReturnNode.prototype.toJSON = function() {
      var ref;
      return {
        i: this.nid,
        n: 'rt',
        v: (ref = this.retVal) != null ? ref.toJSON() : void 0,
        r: this.retNode.nid
      };
    };

    ReturnNode.parse = function(node, tree, memory) {
      var nid, retNode, retVal;
      retVal = ReturnNode.parseAndCheckValue('.return-val', node, memory);
      retNode = BlockNode.parse(ReturnNode.findSubNode(node, '.return-value-node'), tree, memory);
      tree.push(retNode);
      ReturnNode.validate(node, (retVal != null) || retNode.size());
      nid = tree.length;
      return new ReturnNode(nid, retVal, retNode);
    };

    return ReturnNode;

  })(Node);

  window.SwapNode = (function(superClass) {
    extend(SwapNode, superClass);

    function SwapNode(nid1, left1, right1) {
      this.nid = nid1;
      this.left = left1;
      this.right = right1;
    }

    SwapNode.prototype.execute = function(player, node) {
      var leftVal, rightVal;
      leftVal = this.left.execute(player);
      rightVal = this.right.execute(player);
      Value.write(this.left, rightVal, player);
      Value.write(this.right, leftVal, player);
      return {
        value: leftVal !== rightVal
      };
    };

    SwapNode.prototype.toJSON = function() {
      var ref, ref1;
      return {
        i: this.nid,
        n: 'sw',
        l: (ref = this.left) != null ? ref.toJSON() : void 0,
        r: (ref1 = this.right) != null ? ref1.toJSON() : void 0
      };
    };

    SwapNode.parse = function(node, tree, memory) {
      var left, nid, right;
      left = SwapNode.parseAndCheckValue('.swap-left', node, memory);
      right = SwapNode.parseAndCheckValue('.swap-right', node, memory);
      SwapNode.validate(node, (left != null) && (right != null));
      nid = tree.length;
      return new SwapNode(nid, left, right);
    };

    return SwapNode;

  })(Node);

  window.ValueNode = (function(superClass) {
    extend(ValueNode, superClass);

    function ValueNode(nid1, value1) {
      this.nid = nid1;
      this.value = value1;
    }

    ValueNode.prototype.execute = function(player, node) {
      return {
        value: this.value.execute(player)
      };
    };

    ValueNode.prototype.toJSON = function() {
      var ref;
      return {
        i: this.nid,
        n: 'vl',
        v: (ref = this.value) != null ? ref.toJSON() : void 0
      };
    };

    ValueNode.parse = function(node, tree, memory) {
      var nid, value;
      value = ValueNode.parseAndCheckValue('.value-var', node, memory);
      ValueNode.validate(node, value != null);
      nid = tree.length;
      return new ValueNode(nid, value);
    };

    return ValueNode;

  })(Node);

  window.WhileNode = (function(superClass) {
    extend(WhileNode, superClass);

    function WhileNode(nid1, condition1, body1, op1) {
      this.nid = nid1;
      this.condition = condition1;
      this.body = body1;
      this.op = op1;
    }

    WhileNode.prototype.execute = function(player, node) {
      var bodyValue, cond, condValue, size;
      if (node <= this.condition) {
        cond = player.tree.get(this.condition);
        size = cond.size();
        if (size === 1) {
          condValue = cond.execute(player, node);
        } else if (size > 1) {
          condValue = cond.executeAll(player, node, this.op);
        } else {
          throw new ExecutionError('no_condition', []);
        }
        if (condValue.next > -1) {
          return {
            next: condValue.next
          };
        } else if (condValue.value) {
          return {
            next: this.body
          };
        } else {
          return {};
        }
      } else if (node <= this.body) {
        bodyValue = player.tree.get(this.body).execute(player, node);
        if ((bodyValue.scope != null)) {
          return bodyValue;
        } else if (bodyValue.next != null) {
          return {
            next: bodyValue.next
          };
        } else {
          return {
            next: this.condition
          };
        }
      }
    };

    WhileNode.prototype.mark = function(player, node) {
      var body, condition;
      condition = player.tree.get(this.condition);
      if (node === this.nid) {
        return condition.mark(player, this.condition);
      } else if (node <= this.condition) {
        return condition.mark(player, node);
      } else {
        body = player.tree.get(this.body);
        if (body.size()) {
          return body.mark(player, node);
        } else {
          return condition.mark(player, this.condition);
        }
      }
    };

    WhileNode.prototype.toJSON = function() {
      return {
        i: this.nid,
        n: 'wl',
        c: this.condition,
        b: this.body,
        o: this.op
      };
    };

    WhileNode.parse = function(node, tree, memory) {
      var body, condition, nid, op, size;
      condition = BlockNode.parse(WhileNode.findSubNode(node, '.while-condition'), tree, memory);
      tree.push(condition);
      body = BlockNode.parse(WhileNode.findSubNode(node, '.while-body'), tree, memory);
      tree.push(body);
      size = condition.size();
      WhileNode.validate(node, size > 0);
      op = WhileNode.findSubNode(node, '.while-operator');
      if (size > 1) {
        op.show();
      } else {
        op.hide();
      }
      nid = tree.length;
      return new WhileNode(nid, condition.nid, body.nid, op.val());
    };

    return WhileNode;

  })(Node);

}).call(this);