$(document).ready(function() {
  // $('*[data-toggle="tooltip"]').tooltip();
  data_confirm();
  toogle_contact_message();
  auto_complete();
  close_flash_message();
  mask_money();
  dropdown();
  inputmasks();
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
https://github.com/plentz/jquery-maskmoney/
*/

var mask_money = function() {
  if($("#product_price").length == 0) return false;
  $("#product_price").maskMoney({
    prefix:'R$',
    thousands:'.',
    decimal:','
  });
  $("#product_price").maskMoney('mask');
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

  url = $('#autocomplete_product_by_id').data('url');
  $('#autocomplete_product_by_id').autocomplete({
    serviceUrl: url,
    onSelect: function (suggestion) {
      $("#autocomplete_product_by_name").val(suggestion.data)
    }
  });


};

var close_flash_message = function() {
    $('.message').transition({
      duration   : '1s'
    });

    $('.message .close').on('click', function() {
      $(this).closest('.message').fadeOut();
    });
};


var dropdown = function() {
    $('.ui.menu .ui.dropdown').dropdown({
      on: 'hover'
    });

    $('.ui.menu a.item').on('click', function() {
      $(this).addClass('active').siblings().removeClass('active');
    });
};

var inputmasks = function() {
    $("#client_phone").mask("(000) 0000-00000");
    $("#client_cpf").mask("999.999.999-99");
    $('#client_cep').mask('00000-000');
}
