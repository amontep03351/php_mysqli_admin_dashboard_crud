/* globals Chart:false, feather:false */

(function () {
  'use strict'

  feather.replace()


})()
function load_contentadmin(conname) {
  $.ajax({
    url: conname,
    headers: {'X-Requested-With': 'XMLHttpRequest'},
    type: "post",
    beforeSend: function() {
      var textload ='<button class="btn btn-primary" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>  Loading...</button>';
      $("#load_contentadmin").html(textload);
    },
    success: function (response) {
      $("#load_contentadmin").html(response);
    },
    error: function(data) {
       console.log(data);
    }
  });
}
