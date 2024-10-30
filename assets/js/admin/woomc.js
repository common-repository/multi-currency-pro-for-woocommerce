(function ($) {

  $(document).ready(function () {

    var clone_dataset = new Array()

    // Make set of indexes to check for appending the data.
    $('.woomc-clone-wrap tr').each(function (index, element) {
      clone_dataset.push(index)
    });

    $('.woomc-clone-wrap').data( 'woomcindex', clone_dataset );

    $('input[name="woomc_settings[default]"]:checked').trigger('change');

    $('#woomc-currency-table tbody').sortable();
  });

  /**
   * Clone the currency row.
   */
  $('#woomc-currency-table').on('click', '.woomc-clone', function (e) {

    e.preventDefault()

    var clone_elem = $('input[name="woomc_settings[default]"]:checked').parents('tr').clone(true, true),
      original_element = $('input[name="woomc_settings[default]"]:checked').parents('tr')

    $('.woomc-clone-wrap').trigger('before_clone', clone_elem)
    $('.woomc-clone-wrap').append(clone_elem)
    $('input[type="radio"]', original_element).prop('checked', true)

  });

  /**
   * Delete the currency form row.
   */
  $('#woomc-currency-table').on('click', '.woomc-delete', function (e) {

    e.preventDefault()

    var element_to_delete = $(this).parents('tr'),
      is_checked = $('input[name="woomc_settings[default]"]', element_to_delete).is(':checked')

    if ($('.woomc-clone-wrap tr').length > 1) {

      $(element_to_delete).remove()

      //$('#woomc-currency-table').trigger( 'reindex', clone_elem );

      if (is_checked) {
        var first_row = $('.woomc-clone-wrap tr:first-child')
        $('input[type="radio"]', first_row).prop('checked', true)
      }

    }
  });

  /**
   * Re-index elements after cloning or deleting rows.
   */
  $('.woomc-clone-wrap').on('before_clone', function (event, element) {

    var index_dataset = $('.woomc-clone-wrap').data('woomcindex')

    var get_random_index = function () {

      var newIndex = Math.floor((Math.random() * 100) + 1)

      if (index_dataset.indexOf(newIndex) !== -1) {
        get_random_index()
      }

      index_dataset.push(newIndex)
      return newIndex

    }

    $(element).html($(element).html().replace(/woomc_settings\[currencies\]\[\d\]/g, 'woomc_settings[currencies][' + get_random_index().toString() + ']'));
    $( '.woomc-rate', element).removeAttr('disabled');
    $( '.woomc-exchange', element).removeAttr('disabled');
  });

  /**
   * Before form submission actions.
   */
  $("#wooc-settings-form").submit(function(e){

    var selected_row  = $('input[name="default"]:checked').parents('tr'),
      selectedVal = $( 'select', selected_row ).val();

    $('input[name="default"]', selected_row).val(selectedVal);

    return true;
  });

  /**
   * Change rate and exchange rate on default currency select.
   */
  $('#woomc-currency-table').on('change', '.woomc-toclone input[type="radio"]', function () {

    if ($(this).is(':checked')) {

      var row = $(this).parents('tr');

      $('.woomc-rate, .woomc-exchange').each(function(){
        $(this).removeAttr('disabled');
      });

      $('.woomc-rate', row).val('1').attr( 'disabled', 'disabled' );
      $('.woomc-exchange', row).val('0').attr( 'disabled', 'disabled' );
    }

  });

  /**
   * Select2 input.
   */
  $('.woomc-currency-by-country').select2({
    width: '250px'
  });

  /**
   * Tab selection in admin.
   */
  $( '#woomc-admin-tabs a.nav-link' ).on( 'click', function(){
    $('#woomctab').val( $(this).data('tab') );
  });

  /**
   * Select all, remove all for location table.
   */
  $('#woomc-location-table .select-all').on( 'click', function(e){

    e.preventDefault();
    $( '.woomc-currency-by-country > option', $(this).parents('tr') ).prop( 'selected', 'selected' );
    $( '.woomc-currency-by-country', $(this).parents('tr') ).trigger('change');

  });
  $('#woomc-location-table .remove-all').on( 'click', function(e){

    e.preventDefault();
    $( '.woomc-currency-by-country > option', $(this).parents('tr') ).removeAttr( 'selected' );
    $( '.woomc-currency-by-country', $(this).parents('tr') ).trigger('change');

  });

  /**
   * Update rates
   */
  $('.woomc-update-rates').on('click', function (e) {

    e.preventDefault();
    $(this).woomc_update_rates();
  })

})(jQuery)
