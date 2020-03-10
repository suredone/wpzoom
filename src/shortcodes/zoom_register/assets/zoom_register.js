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

    var emailField = $('#zoom-field-email')
    if( !validateEmail( emailField )) {
      formErrors.push('Enter a valid email address.')
    }

    console.log( formErrors )

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

  function validateEmail( el ) {
    let email = el.val()
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }

})( jQuery );
