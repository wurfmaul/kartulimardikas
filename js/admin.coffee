class Api
  @deleteUser: (uid, callback) ->
    $.ajax "api/user.php",
      type: 'POST'
      data:
        remove: uid
        lang: window.defaults.lang
      dataType: 'json'
      success: (data, textStatus, jqXHR) =>
        msg = data['error'] ? ""
        if (msg isnt "") then @_printError(msg)
        else
          @_printSuccess(data['success'])
          callback(data['status'])
      error: (jqXHR, textStatus, errorThrown) =>
        @_printError("AJAX Error: #{textStatus}")

  @eraseUser: (uid, callback) ->
    $.ajax "api/user.php",
      type: 'POST'
      data:
        erase: uid
        lang: window.defaults.lang
      dataType: 'json'
      success: (data, textStatus, jqXHR) =>
        msg = data['error'] ? ""
        if (msg isnt "") then @_printError(msg)
        else
          @_printSuccess(data['success'])
          callback()
      error: (jqXHR, textStatus, errorThrown) =>
        @_printError("AJAX Error: #{textStatus}")

  @unDeleteUser: (uid, callback) ->
    $.ajax "api/user.php",
      type: 'POST'
      data:
        resurrect: uid
        lang: window.defaults.lang
      dataType: 'json'
      success: (data, textStatus, jqXHR) =>
        msg = data['error'] ? ""
        if (msg isnt "") then @_printError(msg)
        else
          @_printSuccess(data['success'])
          callback(data['status'])
      error: (jqXHR, textStatus, errorThrown) =>
        @_printError("AJAX Error: #{textStatus}")

  @toggleAdmin: (uid, callback) ->
    $.ajax "api/user.php",
      type: 'POST'
      data:
        admin: uid
        lang: window.defaults.lang
      dataType: 'json'
      success: (data, textStatus, jqXHR) =>
        msg = data['error'] ? ""
        if (msg isnt "") then @_printError(msg)
        else
          @_printSuccess(data['success'])
          callback()
      error: (jqXHR, textStatus, errorThrown) =>
        @_printError("AJAX Error: #{textStatus}")

  @deleteAlgorithm: (aid, callback) ->
    $.ajax "api/algorithm.php?area=admin",
      type: 'POST'
      data:
        aid: aid
        action: 'remove'
        lang: window.defaults.lang
      dataType: 'json'
      success: (data, textStatus, jqXHR) =>
        msg = data['error'] ? ""
        if (msg isnt "") then @_printError(msg)
        else
          @_printSuccess(data['success'])
          callback(data['status'])
      error: (jqXHR, textStatus, errorThrown) =>
        @_printError("AJAX Error: #{textStatus}")

  @eraseAlgorithm: (aid, callback) ->
    $.ajax "api/algorithm.php?area=admin",
      type: 'POST'
      data:
        aid: aid
        action: 'erase'
        lang: window.defaults.lang
      dataType: 'json'
      success: (data, textStatus, jqXHR) =>
        msg = data['error'] ? ""
        if (msg isnt "") then @_printError(msg)
        else
          @_printSuccess(data['success'])
          callback()
      error: (jqXHR, textStatus, errorThrown) =>
        @_printError("AJAX Error: #{textStatus}")

  @unDeleteAlgorithm: (aid, callback) ->
    $.ajax "api/algorithm.php?area=admin",
      type: 'POST'
      data:
        aid: aid
        action: 'resurrect'
        lang: window.defaults.lang
      dataType: 'json'
      success: (data, textStatus, jqXHR) =>
        msg = data['error'] ? ""
        if (msg isnt "") then @_printError(msg)
        else
          @_printSuccess(data['success'])
          callback(data['status'])
      error: (jqXHR, textStatus, errorThrown) =>
        @_printError("AJAX Error: #{textStatus}")

  @_printSuccess: (msg) ->
    $('#adminSuccessText').html msg
    $('#adminSuccess').show('slow')
    $('#adminAlert').hide('slow')

  @_printError: (msg) ->
    $('#adminAlertText').html msg
    $('#adminAlert').show('slow')
    $('#adminSuccess').hide('slow')

$ ->
  $('.filter').click(->
    col = $(this).data('column')
    txt = $(this).data('filter')
    target = $(this).data('target')
    $(target).find('.tablesorter-filter').val('').eq(col).val(txt)
    $(target).trigger('search', false)
    false
  )

  $('.delete-user').click(->
    row = $(this).closest('tr')
    uid = row.data('uid')
    Api.deleteUser(uid, (status) ->
      row.find('.status').text(status)
      row.addClass('deleted')
    )
  )

  $('.erase-user').click(->
    row = $(this).closest('tr')
    uid = row.data('uid')
    Api.eraseUser(uid, -> row.remove()) if(confirm($(this).data('warning')))
  )

  $('.un-delete-user').click(->
    row = $(this).closest('tr')
    uid = row.data('uid')
    Api.unDeleteUser(uid, (status) ->
      row.find('.status').text(status)
      row.removeClass('deleted')
    )
  )

  $('.adminify-user').click(->
    row = $(this).closest('tr')
    uid = row.data('uid')
    username = row.find('.username')
    Api.toggleAdmin(uid, -> username.toggleClass('admin'))
  )

  $('.delete-algorithm').click(->
    row = $(this).closest('tr')
    aid = row.data('aid')
    Api.deleteAlgorithm(aid, (status) ->
      row.find('.status').text(status)
      row.addClass('deleted')
    )
  )

  $('.erase-algorithm').click(->
    row = $(this).closest('tr')
    aid = row.data('aid')
    Api.eraseAlgorithm(aid, -> row.remove()) if(confirm($(this).data('warning')))
  )

  $('.un-delete-algorithm').click(->
    row = $(this).closest('tr')
    aid = row.data('aid')
    Api.unDeleteAlgorithm(aid, (status) ->
      row.removeClass('deleted')
      row.find('.status').text(status)
    )
  )

  $('#adminSuccessClose').click(-> $('#adminSuccess').hide('slow'))
  $('#adminAlertClose').click(-> $('#adminAlert').hide('slow'))