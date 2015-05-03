change = (params, callback) ->
  $.ajax "api/edit-user.php",
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
  $('#usernameBtn').click -> change({username: $('#in-username').val()}, (msg) ->
    $('#usernameMsg').val(msg)
    $('#usernameForm').submit()
  )
  $('#emailBtn').click -> change({email: $('#in-email').val()}, ->)
  $('#passwordBtn').click -> change({password1: $('#in-password1').val(), password2: $('#in-password2').val()}, (msg) ->
    $('#passwordMsg').val(msg)
    $('#passwordForm').submit()
  )
  $('#deleteBtn').click -> change({password: $('#delete-password').val()}, (msg) ->
    $('#deleteMsg').val(msg)
    $('#deleteForm').submit()
  ) if (confirm($(this).data('warning')))

  $('#userAlertClose').click ->
    $('#userAlert').hide('slow')

  $('#userSuccessClose').click ->
    $('#userSuccess').hide('slow')