register = ->
  $.ajax "api/register.php",
    type: 'POST'
    data:
      username: $('#in-username').val()
      email: $('#in-email').val()
      password1: $('#in-password1').val()
      password2: $('#in-password2').val()
      lang: window.current.lang
    dataType: 'json'
    success: (data) ->
      msg = data['error'] ? ""

      # check for errors
      for token in ['username', 'email', 'password1', 'password2']
        if data['error-' + token]?
          msg += data['error-' + token]
          $('#' + token + '-group').addClass('has-error');
        else
          $('#' + token + '-group').removeClass('has-error');

      if (msg isnt "")
        $('#registerAlertText').html msg
        $('#registerAlert').show('slow')
      else
        $('#registerForm').submit()
    error: (jqXHR, textStatus) ->
      $('#registerAlertText').html "AJAX Error: #{textStatus}"
      $('#registerAlert').show('slow')

$ ->
  $('#registerBtn').click -> register()

  $('#registerAlertClose').click ->
    $('#registerAlert').hide('slow')