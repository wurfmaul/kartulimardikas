deleteUser = (uid, callback) ->
  $.ajax "api/user.php",
    type: 'POST'
    data:
      remove: uid
      lang: window.defaults.lang
    dataType: 'json'
    success: (data, textStatus, jqXHR) ->
      msg = data['error'] ? ""

      if (msg isnt "")
        $('#adminAlertText').html msg
        $('#adminAlert').show('slow')
        $('#adminSuccess').hide('slow')
      else
        $('#adminSuccessText').html data['success']
        $('#adminSuccess').show('slow')
        $('#adminAlert').hide('slow')
        callback()
    error: (jqXHR, textStatus, errorThrown) ->
      $('#adminAlertText').html "AJAX Error: #{textStatus}"
      $('#adminAlert').show('slow')

eraseUser = (uid, callback) ->
  $.ajax "api/user.php",
    type: 'POST'
    data:
      erase: uid
      lang: window.defaults.lang
    dataType: 'json'
    success: (data, textStatus, jqXHR) ->
      msg = data['error'] ? ""

      if (msg isnt "")
        $('#adminAlertText').html msg
        $('#adminAlert').show('slow')
        $('#adminSuccess').hide('slow')
      else
        $('#adminSuccessText').html data['success']
        $('#adminSuccess').show('slow')
        $('#adminAlert').hide('slow')
        callback()
    error: (jqXHR, textStatus, errorThrown) ->
      $('#adminAlertText').html "AJAX Error: #{textStatus}"
      $('#adminAlert').show('slow')

unDeleteUser = (uid, callback) ->
  $.ajax "api/user.php",
    type: 'POST'
    data:
      resurrect: uid
      lang: window.defaults.lang
    dataType: 'json'
    success: (data, textStatus, jqXHR) ->
      msg = data['error'] ? ""

      if (msg isnt "")
        $('#adminAlertText').html msg
        $('#adminAlert').show('slow')
        $('#adminSuccess').hide('slow')
      else
        $('#adminSuccessText').html data['success']
        $('#adminSuccess').show('slow')
        $('#adminAlert').hide('slow')
        callback()
    error: (jqXHR, textStatus, errorThrown) ->
      $('#adminAlertText').html "AJAX Error: #{textStatus}"
      $('#adminAlert').show('slow')

toggleAdmin = (uid, callback) ->
  $.ajax "api/user.php",
    type: 'POST'
    data:
      admin: uid
      lang: window.defaults.lang
    dataType: 'json'
    success: (data, textStatus, jqXHR) ->
      msg = data['error'] ? ""

      if (msg isnt "")
        $('#adminAlertText').html msg
        $('#adminAlert').show('slow')
        $('#adminSuccess').hide('slow')
      else
        $('#adminAlert').hide('slow')
        callback()
    error: (jqXHR, textStatus, errorThrown) ->
      $('#adminAlertText').html "AJAX Error: #{textStatus}"
      $('#adminAlert').show('slow')

$ ->
  $('.delete-user').click(->
    uid = $(this).data('uid')
    row = $(this).closest('tr')
    deleteUser(uid, -> row.addClass('deleted')) if(confirm($(this).data('warning')))
  )

  $('.erase-user').click(->
    uid = $(this).data('uid')
    row = $(this).closest('tr')
    eraseUser(uid, -> row.remove()) if(confirm($(this).data('warning')))
  )

  $('.un-delete-user').click(->
    uid = $(this).data('uid')
    row = $(this).closest('tr')
    unDeleteUser(uid, -> row.removeClass('deleted'))
  )

  $('.adminify-user').click(->
    uid = $(this).data('uid')
    username = $(this).closest('tr').find('.username')
    toggleAdmin(uid, -> username.toggleClass('admin'))
  )

  $('#adminSuccessClose').click(-> $('#adminSuccess').hide('slow'))
  $('#adminAlertClose').click(-> $('#adminAlert').hide('slow'))

  # initialize sortable table
  $.tablesorter.themes.bootstrap =
    table        : 'table table-bordered table-striped'
    caption      : 'caption',
    header       : 'bootstrap-header',
    sortNone     : '',
    sortAsc      : '',
    sortDesc     : '',
    active       : '',
    hover        : '',
    icons        : '',
    iconSortNone : 'bootstrap-icon-unsorted',
    iconSortAsc  : 'glyphicon glyphicon-chevron-up',
    iconSortDesc : 'glyphicon glyphicon-chevron-down',
    filterRow    : '',
    footerRow    : '',
    footerCells  : '',
    even         : '',
    odd          : ''
  $('.users').tablesorter(
    theme : "bootstrap"
    widthFixed: true
    headerTemplate : '{content} {icon}'
    widgets : [ "uitheme" ]
  )