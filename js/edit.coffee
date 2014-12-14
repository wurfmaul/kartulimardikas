window.printError = (msg) ->
  $('#editAlertText').html(msg)
  $('#editAlert').show('slow')

window.printSuccess = (msg) ->
  $('#editAlert').hide('slow')
  $('#saveSuccess').text(msg).show('slow', -> $(this).fadeOut(3000))

$ ->
  $(".panel-heading").click ->
    $(this).find("span").toggleClass("glyphicon-chevron-right glyphicon-chevron-down")

  $('#editAlertClose').click ->
    $('#editAlert').hide('slow')

  $('#in-long')
    .focus -> $(this).val("") if ($(this).data('placeholder') == $(this).val())
    .blur -> $(this).val($(this).data('placeholder')) if ($(this).val() == "")

  $("#in-name, #in-desc, #in-long").blur ->
    aid = $('#aid').text()
    name = $('#in-name').val()
    desc = $('#in-desc').val()
    long = $('#in-long').val()
    new Api().editInfo(aid, name, desc, long)

class Api
  editInfo: (aid, name, desc, long) ->
    $.ajax "api/edit-algorithm.php?area=info",
      type: 'POST'
      data: { aid: aid, name: name, desc: desc, long: long }
      dataType: 'json'
      success: (data) -> # if response arrived...
        if data['error']? then printError(data['error']) else printSuccess(data['success'])
        if data['aid']? then $('#aid').text(data['aid']);
      error: (jqXHR, textStatus, errorThrown) -> # if request failed
        printError("Request Error: " + errorThrown)