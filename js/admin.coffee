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

$ ->
  $('.delete-user').click(->
    uid = $(this).data('uid')
    row = $(this).closest('tr')
    deleteUser(uid, -> row.addClass('deleted')) if(confirm($(this).data('warning')))
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