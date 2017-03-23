jQuery(function($) {
  $(document).ready(function() {
    if (!$('#able_module').is(":checked")) {
      $('.section:not(.able_module_setting)').hide();
    }
  });

  $('#able_module').click(function() {
    if (!$('#able_module').is(":checked")) {
      $('.section:not(.able_module_setting)').hide();
    } else {
      $('.section:not(.able_module_setting)').show();
    }
  });
});