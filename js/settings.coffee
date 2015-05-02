class Api
  @setVisibility = (status) ->
    aid = $('#aid').data('val')
    $.ajax "api/edit-algorithm.php?area=settings",
      type: 'POST'
      data: {aid: aid, status: status}
      dataType: 'json'
      success: (data) =>
        if data['error']? then @_printError(data['error'])
        else
          @_printSuccess(data['success'])
          $('.settings-visibility').toggle()
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Storage Error: " + errorThrown)

  @delete = ->
    aid = $('#aid').data('val')
    $.ajax "api/edit-algorithm.php?area=delete",
      type: 'POST'
      data: {aid: aid}
      dataType: 'json'
      success: (data) =>
        if data['error']? then @_printError(data['error'])
        else
          $('#delete-msg').val(data['success'])
          $('#delete-form').submit()
      error: (jqXHR, textStatus, errorThrown) => # if request failed
        @_printError("Storage Error: " + errorThrown)

  @_printError: (msg) ->
    $('#editAlertText').html(msg)
    $('#editAlert').show('slow')

  @_printSuccess: (msg) ->
    $('#editAlert').hide('slow')
    $('#saveSuccess:hidden').text(msg).show('slow', -> $(this).fadeOut(3000))

$ ->
  $('#btn-public').click -> Api.setVisibility('public')
  $('#btn-private').click -> Api.setVisibility('private')
  $('#btn-delete').click -> Api.delete() if (confirm($(this).data('warning')))