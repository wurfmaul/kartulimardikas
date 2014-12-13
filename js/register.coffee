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