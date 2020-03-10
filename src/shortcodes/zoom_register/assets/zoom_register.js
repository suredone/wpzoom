(function($) {

  $('.zoom-register-form form').on('submit', function(e) {

    e.preventDefault()
    console.log('form submit...')

    // validate form
    var formErrors = []
    var firstNameField = $('#zoom-field-first-name')
    if( !validateRequired( firstNameField )) {
      formErrors.push('First name is a required field.')
    }

    // show form errors if present and stop processing

    // send validated form to ajax hook

    // handle response from ajax hook process

  })

  function validateRequired( el ) {
    if( el.val() === '' ) {
      return false
    }
    return true
  }

})( jQuery );
