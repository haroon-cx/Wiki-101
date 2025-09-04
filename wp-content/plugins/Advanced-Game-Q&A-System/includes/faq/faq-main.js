jQuery(document).ready(function ($) {
    // ADD FAQ FORM
   jQuery('#add-form-faq').submit('submit', function(){
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
   })

// END


// EDIT FAQ FORM START
   jQuery('#edit-form-faq').submit('submit', function(){
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
   })
/**
 * FAQ approvle script
 */

  jQuery('#edit-form-faq-review').submit('submit', function(){
    var $form = jQuery(this);
    var formData = $form.serialize();
     // Create an object to store form data values
    console.log(formData);
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
   })

  /**
   * FAQ Filter 
   */

$("#agqa-game-filter").on("click", function(event) {
    event.preventDefault(); // Prevent form submission

    var searchTerm = $("#filter-search").val().toLowerCase();
    var selectedCategory = $('input.agqa-filter-select-hidden').val().toLowerCase();
    var resultsFound = false;  // Flag to track if any result is found

    // Initially hide pagination and "Nothing Found" message
    $(".no-found-ctn").hide();  // Hide "Nothing Found" message
    $('div#pagination-demo').hide();  // Hide pagination

    $(".faq-accordion").each(function() {
        var faqCategory = $(this).find(".faq-accodion-status").text().trim().toLowerCase();
        var questionText = $(this).find(".faq-accordion-head h2").text().toLowerCase();
        var answerText = $(this).find(".faq-accordion-body p").text().toLowerCase();

        // If search term is provided, filter based on search term alone, regardless of category
        // If no category is selected, just apply the search filter
        if ((selectedCategory === "all" || faqCategory === selectedCategory) && 
            (questionText.includes(searchTerm) || answerText.includes(searchTerm))) {
            $(this).show();  // Show the FAQ item
            resultsFound = true;  // Mark that at least one result is found
        } else if (searchTerm && (questionText.includes(searchTerm) || answerText.includes(searchTerm))) {
            // If only search term matches, show it without category filter
            $(this).show();  // Show the FAQ item
            resultsFound = true;  // Mark that at least one result is found
        } else {
            $(this).hide();  // Hide the FAQ item
        }
    });

    // If no results are found, show the 'nothing found' message
    if (!resultsFound) {
        $(".no-found-ctn").show();  // Show the 'no results' message
        $('div#pagination-demo').hide();  // Hide pagination
    } else {
        $('div#pagination-demo').show();  // Show pagination
        $(".no-found-ctn").hide();  // Hide the 'nothing found' message
    }
});

});

