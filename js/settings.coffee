class Api
  @setVisibility = (status) ->
    $.ajax "api/algorithm.php?area=settings",
      type: 'POST'
      data:
        aid: window.current.aid
        status: status
        lang: window.current.lang
      dataType: 'json'
      success: (data) =>
        if data['error']? then @_printError(data['error'])
        else
          @_printSuccess(data['success'])
          $('.settings-visibility').toggle()
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Storage Error: " + errorThrown)

  @delete = ->
    $.ajax "api/algorithm.php?area=delete",
      type: 'POST'
      data:
        aid: window.current.aid
        lang: window.current.lang
      dataType: 'json'
      success: (data) =>
        if data['error']? then @_printError(data['error'])
        else
          $('#delete-msg').val(data['success'])
          $('#delete-form').submit()
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Storage Error: " + errorThrown)

  @_printError: (msg) ->
    $('#alertText').html(msg)
    $('#alert').show('slow')

  @_printSuccess: (msg) ->
    $('#alert').hide('slow')
    $('#saveSuccess:hidden').text(msg).show('slow', -> $(this).fadeOut(3000))

$ ->
  $('#btn-public').click -> Api.setVisibility('public')
  $('#btn-private').click -> Api.setVisibility('private')
  $('#btn-delete').click -> Api.delete() if (confirm($(this).data('warning')))