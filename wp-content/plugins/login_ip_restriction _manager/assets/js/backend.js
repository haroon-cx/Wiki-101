jQuery(document).ready(function ($) {
  // Handle form submission
  $("#cuim-add-form-user-man").on("submit", function (e) {
    e.preventDefault(); // Prevent the default form submission
    var $form = jQuery(this);
    var formData = $form.serialize();

    var nonce = cuim_ajax.nonce; // Nonce for security

    // Send the AJAX request
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "add_or_update_user",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        alert(response);
        if (response.success) {
          // Success message
          alert(response.data.message);
        } else {
          // Failure message
          alert(response.data.message);
        }
      },
      error: function (response) {
        // Error message if AJAX fails
        alert("An error occurred.");
      },
    });
  });
});
