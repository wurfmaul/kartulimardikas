// Generated by CoffeeScript 1.10.0
(function() {
  var Api;

  Api = (function() {
    function Api() {}

    Api.setVisibility = function(status) {
      return $.ajax("api/algorithm.php?area=settings", {
        type: 'POST',
        data: {
          aid: window.current.aid,
          status: status,
          lang: window.current.lang
        },
        dataType: 'json',
        success: (function(_this) {
          return function(data) {
            if (data['error'] != null) {
              return _this._printError(data['error']);
            } else {
              _this._printSuccess(data['success']);
              return $('.settings-visibility').toggle();
            }
          };
        })(this),
        error: (function(_this) {
          return function(jqXHR, textStatus, errorThrown) {
            return _this._printError("Storage Error: " + errorThrown);
          };
        })(this)
      });
    };

    Api["delete"] = function() {
      return $.ajax("api/algorithm.php?area=delete", {
        type: 'POST',
        data: {
          aid: window.current.aid,
          lang: window.current.lang
        },
        dataType: 'json',
        success: (function(_this) {
          return function(data) {
            if (data['error'] != null) {
              return _this._printError(data['error']);
            } else {
              $('#delete-msg').val(data['success']);
              return $('#delete-form').submit();
            }
          };
        })(this),
        error: (function(_this) {
          return function(jqXHR, textStatus, errorThrown) {
            return _this._printError("Storage Error: " + errorThrown);
          };
        })(this)
      });
    };

    Api._printError = function(msg) {
      $('#alertText').html(msg);
      return $('#alert').show('slow');
    };

    Api._printSuccess = function(msg) {
      $('#alert').hide('slow');
      return $('#saveSuccess:hidden').text(msg).show('slow', function() {
        return $(this).fadeOut(3000);
      });
    };

    return Api;

  })();

  $(function() {
    $('#btn-public').click(function() {
      return Api.setVisibility('public');
    });
    $('#btn-private').click(function() {
      return Api.setVisibility('private');
    });
    return $('#btn-delete').click(function() {
      if (confirm($(this).data('warning'))) {
        return Api["delete"]();
      }
    });
  });

}).call(this);
