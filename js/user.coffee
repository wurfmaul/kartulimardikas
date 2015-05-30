change = (params, callback) ->
  $.ajax "api/user.php",
    type: 'POST'
    data: params
    dataType: 'json'
    success: (data, textStatus, jqXHR) ->
      msg = data['error'] ? ""
      # check for errors
      for token in ['username', 'email', 'password1', 'password2', 'password']
        if data['error-' + token]?
          msg += data['error-' + token]
          $('#' + token + '-group').addClass('has-error');
        else
          $('#' + token + '-group').removeClass('has-error');

      if (msg isnt "")
        $('#userAlertText').html msg
        $('#userAlert').show('slow')
        $('#userSuccess').hide('slow')
      else
        $('#userSuccessText').html data['success']
        $('#userSuccess').show('slow')
        $('#userAlert').hide('slow')
        callback(data['success'])
    error: (jqXHR, textStatus, errorThrown) ->
      $('#userAlertText').html "AJAX Error: #{textStatus}"
      $('#userAlert').show('slow')

$ ->
  lang = window.defaults.lang
  $('#usernameBtn').click ->
    username = $('#in-username').val()
    change({username: username, lang: lang}, (msg) ->
      $('#usernameMsg').val(msg)
      $('#usernameForm').submit()
    )
  $('#emailBtn').click ->
    email = $('#in-email').val()
    change({email: email, lang: lang}, ->)
  $('#passwordBtn').click ->
    password1 = $('#in-password1').val()
    password2 = $('#in-password2').val()
    change({password1: password1, password2: password2, lang: lang}, (msg) ->
      $('#passwordMsg').val(msg)
      $('#passwordForm').submit()
    )
  $('#deleteBtn').click ->
    password = $('#delete-password').val()
    change({password: password}, (msg) ->
      $('#deleteMsg').val(msg)
      $('#deleteForm').submit()
    ) if (confirm($(this).data('warning')))

  $('#userAlertClose').click ->
    $('#userAlert').hide('slow')

  $('#userSuccessClose').click ->
    $('#userSuccess').hide('slow')