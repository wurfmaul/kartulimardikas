// Generated by CoffeeScript 1.10.0
(function() {
  var extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  window.Tree = (function() {
    function Tree(scope) {
      this.scope = scope;
      this.memory = new Memory($('#scope-' + this.scope + ' .variables>tbody'));
      this.reset();
    }

    Tree.prototype.execute = function(player, node) {
      return this.get(this.root).execute(player, node);
    };

    Tree.prototype.mark = function(player, node) {
      return this.get(this.root).mark(player, node);
    };

    Tree.prototype.get = function(nid) {
      return this.nodes[nid];
    };

    Tree.prototype.reset = function() {
      var rootNode;
      this.memory.reset();
      this.nodes = [];
      rootNode = BlockNode.parse($('#scope-' + this.scope + ' .node_root'), this.nodes, this.memory);
      this.root = this.nodes.length;
      return this.nodes.push(rootNode);
    };

    Tree.prototype.toJSON = function() {
      var i, j, json, len, node, ref;
      json = [];
      ref = this.nodes;
      for (i = j = 0, len = ref.length; j < len; i = ++j) {
        node = ref[i];
        json[i] = node.toJSON();
      }
      return json;
    };

    Tree.toJSON = function() {
      return new this(0).toJSON();
    };

    return Tree;

  })();

  window.ExecutionError = (function(superClass) {
    extend(ExecutionError, superClass);

    function ExecutionError(message, parts) {
      this.message = message;
      this.parts = parts;
    }

    return ExecutionError;

  })(Error);

}).call(this);