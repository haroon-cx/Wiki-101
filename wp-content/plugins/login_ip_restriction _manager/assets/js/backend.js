jQuery(document).ready(function ($) {
  // Handle form submission
  $("#cuim-add-form-user-man").on("submit", function (e) {
    e.preventDefault(); // Prevent the default form submission
    var $form = jQuery(this);
    var formData = $form.serialize();
    // alert(formData);

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

  /**
   * edit user script
   */
  $("#edit-form-user-manage").on("submit", function (e) {
    e.preventDefault(); // Prevent the default form submission
    var $form = jQuery(this);
    var formData = $form.serialize(); // Serialize the form data

    var nonce = cuim_ajax.nonce; // Nonce for security

    // Send the AJAX request
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "edit_user_manage",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        console.log(response); // Log the response to check its structure

        // Check if the response contains success
        if (response.success) {
          // If successful, show a success message
          const $successMsg = $(
            '<div class="submitted-successfully">Successfully Submitted</div>'
          );
          $form.append($successMsg);

          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
              $(this).remove();
            });
          }, 3000);

          // Find the *actual* back button
          const $btn = $(".form-header-row .back-button");
          const btn = $btn.get(0);
          if (!btn) {
            console.warn("Back button not found in DOM at success time.");
            return;
          }

          $btn.trigger("click");
          btn.click();
          btn.dispatchEvent(
            new MouseEvent("click", { bubbles: true, cancelable: true })
          );
        } else {
          // If the response is not successful, show an error message
          const $errorMsg = $(
            '<div class="submitted-unsuccessfully">' +
              response.data.message +
              "</div>"
          );
          $form.append($errorMsg);

          // Hide after 3 seconds
          setTimeout(function () {
            $errorMsg.fadeOut(400, function () {
              $(this).remove();
            });
          }, 3000);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error); // Log the error for debugging
        alert("An error occurred! Please try again later.");
      },
    });
  });
});
