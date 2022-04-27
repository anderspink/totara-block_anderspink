$(function() {
  var radioTarget = 'briefing';
  const checkedRadioName = $('input[name="config_source"]:checked').data('target');

  $('input[name="name"]').val($('select[name="config_' + checkedRadioName + '"]').find(':selected').html());

  $('select[name="config_briefing"], select[name="config_board"]').change(function() {
    const _this = $(this);
    const nameInputEle = $('input[name="name"]');

    if (_this.attr('name') == 'config_' + radioTarget) {
      nameInputEle.val(_this.find(':selected').html());
    }
  });

  $('input[name="config_source"]').change(function() {
    const _this = $(this);
    radioTarget = _this.data('target');

    $('input[name="name"]').val($('select[name="config_' + radioTarget + '"]').find(':selected').html());
  });

  var element = setInterval(function() {
    if ($('.form-autocomplete-selection').length) {
      $('.form-autocomplete-selection').attr('style', 'min-height: 0 !important;');
      clearInterval(element);
    }
  }, 100);
});