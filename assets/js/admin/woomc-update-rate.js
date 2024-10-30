(function ($) {

  $.fn.woomc_update_rates = function () {

    var default_currency = $('input[name="woomc_settings[default]"]:checked').val()

    var other_currencies = (function () {
      var currencies = []

      $('input[name="woomc_settings[default]"]').each(function (index, element) {
        currencies.push($(element).val())
      })

      return currencies
    })()

    var ajaxData = {
      action: 'woomc_exchange_rate',
      default: default_currency,
      targets: other_currencies
    }

    var fetchExchanges = $.post(ajaxurl, ajaxData, function (response) {

      if (response.success == true) {

        var data = JSON.parse(response.data)

        $('input[name="woomc_settings[default]"]').each(function (index, element) {

          var row = $(element).parents('tr'),
            curr_currency = $(element).val()

          if (($(element).val() !== default_currency) && data[curr_currency] !== 'undefined') {
            $('.woomc-rate', row).val(data[curr_currency])
          }

        })

      }

    })

  }

})(jQuery)
