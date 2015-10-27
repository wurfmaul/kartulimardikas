change = (params, callback) ->
  $.ajax("api/user.php",
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
        $('#alertText').html msg
        $('#alert').show('slow')
        $('#success').hide('slow')
      else
        $('#successText').html data['success']
        $('#success').show('slow')
        $('#alert').hide('slow')
        callback(data['success'])
    error: (jqXHR, textStatus, errorThrown) ->
      $('#alertText').html "AJAX Error: #{textStatus}"
      $('#alert').show('slow')
  )

renameMe = ->
  username = $('#in-username').val()
  change({username: username, lang: window.current.lang}, (msg) ->
    $('#usernameMsg').val(msg)
    $('#usernameForm').submit()
  )
  username

deleteMe = ->
  password = $('#delete-password').val()
  change({password: password}, (msg) ->
    $('#deleteMsg').val(msg)
    $('#deleteForm').submit()
  ) if (confirm($('#deleteBtn').data('warning')))

changeEmail = ->
  email = $('#in-email').val()
  change({email: email, lang: window.current.lang}, ->)
  email

changePassword = ->
  password1 = $('#in-password1').val()
  password2 = $('#in-password2').val()
  change({password1: password1, password2: password2, lang: window.current.lang}, (msg) ->
    $('#passwordMsg').val(msg)
    $('#passwordForm').submit()
  )

$ ->
  $('#usernameBtn').click -> renameMe()
  $('#emailBtn').click -> changeEmail()
  $('#passwordBtn').click -> changePassword()
  $('#deleteBtn').click -> deleteMe()

  $('#in-username').keypress((event) -> renameMe() if (event.which is 13))
  $('#in-email').keypress((event) -> changeEmail() if (event.which is 13))
  $('#in-password1, #in-password2').keypress((event) -> changePassword() if (event.which is 13))
  $('#delete-password').keypress((event) -> deleteMe() if (event.which is 13))