###
 @license
 This file was created 2014-2015 by https://github.com/wurfmaul
 and licensed under the GNU GENERAL PUBLIC LICENSE Version 3
 https://gnu.org/licenses/gpl-3.0.txt
###
$ ->
  $('.close').click -> $(this).parent('.alert').hide('slow')