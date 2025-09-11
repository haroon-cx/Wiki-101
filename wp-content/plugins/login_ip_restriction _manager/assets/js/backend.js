jQuery(document).ready(function ($) {
  $("#submit_user_form").on("submit", function (e) {
    e.preventDefault();
    alert("hello");
    // Get the form data
    var data = {
      action: "add_or_update_user",
      account: $("#account").val(),
      new_password: $("#new_password").val(),
      confirm_password: $("#confirm_password").val(),
      state: $("#state").val(),
      user_role: $("#user_role").val(),
      company_name: $("#company_name").val(),
      email: $("#email").val(),
      custom_labels: [
        $("#custom_label_1").val(),
        $("#custom_label_2").val(),
        $("#custom_label_3").val(),
        $("#custom_label_4").val(),
      ],
      custom_fields: [
        $("#custom_field_1").val(),
        $("#custom_field_2").val(),
        $("#custom_field_3").val(),
        $("#custom_field_4").val(),
      ],
      user_id: $("#user_id").val(), // Leave empty if adding a new user
    };
    var nonce = cuim_nonce;
    // Send the AJAX request
    $.ajax({
      url: ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "add_or_update_user",
        form_data: formData,
        faq_id: faqId,
        nonce: nonce,
      },
      success: function (response) {
        if (response.success) {
          alert(response.data.message);
        } else {
          alert(response.data.message);
        }
      },
      error: function () {
        alert("An error occurred.");
      },
    });
  });
});
