$(document).ready(function() {
  $('*[data-toggle="tooltip"]').tooltip()
  data_confirm();
  toogle_contact_message();
  auto_complete();
});

/*** Confirm dialog **/
var data_confirm = function () {
   $('a[data-confirm], button[data-confirm]').click( function () {
      var msg = $(this).data('confirm');
      return confirm(msg);
   });
};

/*
 * Show and hide contact message **/
var toogle_contact_message = function () {
   $('#contacts table tbody a.show').on('click', function (event) {
      event.preventDefault();
      var id = $(this).attr('href');
      var msg = $(this).closest('tbody').find(id);
      msg.toggleClass('hidden');
   });
};

/**
  https://github.com/devbridge/jQuery-Autocomplete
*/

var auto_complete = function() {

  var url = $('#autocomplete_clients').data('url');
  $('#autocomplete_clients').autocomplete({
    serviceUrl: url,
    minChars: 2,
    onSelect: function (suggestion) {
        $("#order_user_id").val(suggestion.data)
    }
  });

  url = $('#autocomplete_product_by_name').data('url');
  $('#autocomplete_product_by_name').autocomplete({
    serviceUrl: url,
    onSelect: function (suggestion) {
        $("#autocomplete_product_by_id").val(suggestion.data)
    }
  });


};
