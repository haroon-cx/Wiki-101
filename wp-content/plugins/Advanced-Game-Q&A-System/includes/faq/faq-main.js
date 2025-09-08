jQuery(document).ready(function ($) {
  // ADD FAQ FORM
  jQuery("#add-form-faq").submit("submit", function () {
    var $form = jQuery(this);
    var formData = $form.serialize();
    // Create an object to store form data values
    var formDataObject = {};
    let isValid = true;
    // Check if all required fields are filled
    $form.find("[required]").each(function () {
      const field = $(this);
      // Trim spaces and check if the field is only spaces or empty
      const trimmedValue = field.val().trim();

      if (!trimmedValue) {
        // If the field is empty or contains only spaces
        isValid = false;
        // alert(fieldName + " cannot be empty or just spaces.");
        return false; // Exit the loop and stop further validation
      }
      if (!field.val()) {
        // If the field is empty
        isValid = false;
        return false;
      }
    });

    if (!isValid) {
      return;
    }
    // AJAX
    var nonce = agqa_ajax.nonce;
    $.ajax({
      type: "POST",
      url: agqa_ajax.ajax_url,
      data: {
        action: "agqa_insert_review_faq",
        form_data: formData,
        nonce: nonce,
      },
      success: function (response) {
        if (response.includes("Success")) {
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
          // alert(response);
          const $successMsg = $(
            `<div class="submitted-unsuccessfully">${response}</div>`
          );
          $form.append($successMsg);

          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
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
    // console.log(formData);
    // alert(formData);
  });

  /**
   * Edit FAQ Form
   */

  jQuery("#edit-form-faq").submit("submit", function () {
    var $form = jQuery(this);
    var formData = $form.serialize();
    // Create an object to store form data values
    var formDataObject = {};
    let isValid = true;
    // Check if all required fields are filled
    $form.find("[required]").each(function () {
      const field = $(this);
      // Trim spaces and check if the field is only spaces or empty
      const trimmedValue = field.val().trim();

      if (!trimmedValue) {
        isValid = false;
        return false;
      }
      if (!field.val()) {
        // If the field is empty
        isValid = false;
        return false;
      }
    });

    if (!isValid) {
      return;
    }
    // AJAX
    var nonce = agqa_ajax.nonce;
    $.ajax({
      type: "POST",
      url: agqa_ajax.ajax_url,
      data: {
        action: "agqa_edit_faq",
        form_data: formData,
        nonce: nonce,
      },
      success: function (response) {
        // console.log(response);
        if (response.includes("Success")) {
          // alert("Successfully Submitted");
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
          // alert(response);
          const $successMsg = $(
            `<div class="submitted-unsuccessfully">${response}</div>`
          );
          $form.append($successMsg);

          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
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

  /**
   * FAQ approvle script
   */

  jQuery("#edit-form-faq-review").submit("submit", function () {
    var $form = jQuery(this);
    var formData = $form.serialize();
    // Create an object to store form data values
    // console.log(formData);
    let isValid = true;
    // Check if all required fields are filled
    $form.find("[required]").each(function () {
      const field = $(this);
      // Trim spaces and check if the field is only spaces or empty
      const trimmedValue = field.val().trim();

      if (!trimmedValue) {
        isValid = false;
        return false;
      }
      if (!field.val()) {
        // If the field is empty
        isValid = false;
        return false;
      }
    });

    if (!isValid) {
      return;
    }
    // AJAX
    var nonce = agqa_ajax.nonce;
    $.ajax({
      type: "POST",
      url: agqa_ajax.ajax_url,
      data: {
        action: "approve_faq_review",
        form_data: formData,
        nonce: nonce,
      },
      success: function (response) {
        // console.log(response);
        if (response.includes("Success")) {
          // alert("Successfully Submitted");
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
          // alert(response);
          const $successMsg = $(
            `<div class="submitted-unsuccessfully">${response}</div>`
          );
          $form.append($successMsg);

          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
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

  /**
   * FAQ Filter
   */

  $("#agqa-game-filter").on("click", function (event) {
    event.preventDefault(); // Prevent form submission

    var searchTerm = $("#filter-search").val().toLowerCase(); // Get search term
    var selectedCategory = $("input.agqa-filter-select-hidden")
      .val()
      .toLowerCase(); // Get selected category
    var resultsFound = false; // Flag to track if any result is found

    // Initially hide pagination and "Nothing Found" message
    $(".no-found-ctn").hide(); // Hide "Nothing Found" message
    $("div#pagination-demo").hide(); // Hide pagination

    $(".faq-accordion").each(function () {
      var faqText = $(this).text().toLowerCase(); // Get all text inside the FAQ accordion
      var faqCategory = $(this)
        .find(".faq-accodion-status")
        .text()
        .toLowerCase(); // Optionally, get category text

      // If a category is selected, and it matches the FAQ category
      if (
        (selectedCategory === "all" ||
          faqCategory.includes(selectedCategory)) &&
        faqText.includes(searchTerm) // Check if the search term is found anywhere in the FAQ content
      ) {
        $(this).show(); // Show the FAQ item
        $(this).find(".faq-accordion-head").addClass("active expand");
        resultsFound = true; // Mark that at least one result is found
      } else if (
        // If no category filter is applied and only search term matches anywhere in the FAQ
        !selectedCategory &&
        faqText.includes(searchTerm)
      ) {
        $(this).show(); // Show the FAQ item
        this.find(".faq-accordion-head").addClass("active expand");
        resultsFound = true; // Mark that at least one result is found
      } else {
        $(this).hide(); // Hide the FAQ item
        $(this).find(".faq-accordion-head").removeClass("active expand");
      }
    });

    // If no results are found, show the 'nothing found' message
    if (!resultsFound) {
      $(".no-found-ctn").show(); // Show the 'no results' message
      $("div#pagination-demo").hide(); // Hide pagination
    } else {
      $("div#pagination-demo").show(); // Show pagination
      $(".no-found-ctn").hide(); // Hide the 'nothing found' message
    }
  });

  /**
   * FAQ like & dislike Script
   */
  $(".like-button").on("click", function () {
    var $form = jQuery(this);
    var formData = "faq-id=" + $form.find(".agqa-like").val();
    formData += "&like=1";
    //  alert(formData);
    var faqId = $(this).data("faq-id"); // Get the FAQ ID from the button's data attribute

    // Send AJAX request to handle like
    var nonce = agqa_ajax.nonce;
    $.ajax({
      url: agqa_ajax.ajax_url,
      type: "POST",
      data: {
        action: "like_dislike_action",
        form_data: formData,
        faq_id: faqId,
        action_type: "like",
        nonce: nonce,
      },
      success: function (response) {
        console.log(response);
        if (response.includes("Success")) {
          // alert("Successfully Submitted");
          const $successMsg = $(
            '<div class="submitted-successfully">Liked</div>'
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
          // alert(response);
          const $successMsg = $(
            `<div class="submitted-unsuccessfully">${response}</div>`
          );
          $form.append($successMsg);

          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
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

  // When a dislike button is clicked
  $(".unlike-button").on("click", function () {
    var $form = jQuery(this);
    var formData = "faq-id=" + $form.find(".agqa-dislike").val();
    formData += "&like=0";
    //  alert(formData);
    var faqId = $(this).data("faq-id"); // Get the FAQ ID from the button's data attribute
    // Send AJAX request to handle dislike
    var nonce = agqa_ajax.nonce;
    $.ajax({
      url: agqa_ajax.ajax_url,
      type: "POST",
      data: {
        action: "like_dislike_action",
        form_data: formData,
        faq_id: faqId,
        action_type: "dislike",
        nonce: nonce,
      },
      success: function (response) {
        console.log(response);
        if (response.includes("Success")) {
          // alert("Successfully Submitted");
          const $successMsg = $(
            '<div class="submitted-successfully">Dislike</div>'
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
          // alert(response);
          const $successMsg = $(
            `<div class="submitted-unsuccessfully">${response}</div>`
          );
          $form.append($successMsg);

          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
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

  /**
   * FAQ Delete Script
   */
  $("button#yes-cancel").on("click", function () {
    var faqId = "faq_id=" + $(this).val(); // Get the FAQ ID from the hidden input
    var del = $(this).val();
    $("#custom-faq-field-popup").show(); // Show the confirmation popup

    // When user clicks "Yes", send AJAX request to delete the FAQ

    // Send AJAX request to delete the FAQ
    var nonce = agqa_ajax.nonce;
    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "delete_faq",
        form_data: faqId,
        nonce: nonce, // Nonce for security
      },
      success: function (response) {
        // If deletion is successful, hide the popup and remove the FAQ from the DOM

        if (response.includes("Success")) {
          // alert();
          $(".faq-accordion[data-id='" + del + "']").remove();
          $("#custom-faq-field-popup").hide();
        } else {
          alert(response);
        }
      },
      error: function () {
        alert("An error occurred while deleting the FAQ.");
      },
    });

    // If user clicks "No", close the popup
    $(".no-cancel").on("click", function () {
      $("#custom-faq-field-popup").hide();
    });
  });
});
