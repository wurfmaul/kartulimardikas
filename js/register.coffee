$ ->
  $('#registerBtn').click ->
    $.ajax "api/register.php",
      type: 'POST'
      data:
        username: $('#in-username').val()
        email: $('#in-email').val()
        password1: $('#in-password1').val()
        password2: $('#in-password2').val()
      dataType: 'json'
      success: (data, textStatus, jqXHR) ->
        msg = data['error'] ? ""
        # check username
        if data['errorUsername']?
          msg += data['errorUsername']
          $('#username-group').addClass('has-error');
        else
          $('#username-group').removeClass('has-error');
        # check email
        if data['errorEmail']?
          msg += data['errorEmail']
          $('#email-group').addClass('has-error');
        else
          $('#email-group').removeClass('has-error');
        # check first password
        if data['errorPassword1']?
          msg += data['errorPassword1']
          $('#password1-group').addClass('has-error');
        else
          $('#password1-group').removeClass('has-error');
        # check second password
        if data['errorPassword2']?
          msg += data['errorPassword2']
          $('#password2-group').addClass('has-error');
        else
          $('#password2-group').removeClass('has-error');

        if (msg isnt "")
          $('#registerAlertText').html msg
          $('#registerAlert').show('slow')
          $('#registerSuccess').hide('slow')
        else
          $('#registerSuccessText').html data['success']
          $('#registerSuccess').show('slow')
          $('#registerAlert').hide('slow')
      error: (jqXHR, textStatus, errorThrown) ->
        $('#registerAlertText').html "AJAX Error: #{textStatus}"
        $('#registerAlert').show('slow')

  $('#registerAlertClose').click ->
    $('#registerAlert').hide('slow')

  $('#registerSuccessClose').click ->
    $('#registerSuccess').hide('slow')