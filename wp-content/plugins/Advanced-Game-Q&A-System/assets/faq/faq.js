jQuery(document).ready(function () {
  // ==========================
  // 1. Initialize Froala Editor
  // ==========================
  jQuery(".editor-faq").each(function () {
    var editor = new FroalaEditor(this, {
      toolbarButtons: [
        "bold",
        "italic",
        "underline",
        "strikeThrough",
        "fontFamily",
        "fontSize",
        "color",
        "paragraphFormat",
        "align",
        "formatOL",
        "formatUL",
        "outdent",
        "indent",
        "clearFormatting",
        "insertLink",
        "undo",
        "redo",
        "fullscreen",
        "html",
      ],
      imageUpload: false,
      videoUpload: false,
      fileUpload: false,
    });

    const maxChars = 2999; // Set the max character count to 3000
    let typingTimer;
    const typingDelay = 500;

    jQuery(document).on("input", function () {
      const $editorContent = jQuery(".fr-view");
      const $charCounter = jQuery(".form-field-editor .char-counter");
      const $currentCount = $charCounter.find(".current-count");
      const $formResponse = jQuery(".form-field-editor .form-response");

      // Get the text content and check its length
      let text = $editorContent.text();
      let length = text.length;

      // Limit the text to maxChars
      if (length > maxChars) {
        editor.html.set(text.substring(0, maxChars)); // Truncate the text
        text = editor.html.get();
      }

      // Update the character counter
      $currentCount.text(length);

      // Reset messages
      if ($formResponse.hasClass("success")) {
        $formResponse.text("").removeClass("success");
        $charCounter.removeClass("show-message");
      }

      // If the input is empty
      if (length === 0) {
        $formResponse.text("").removeClass("error success");
        $charCounter.removeClass("show-message");
        return;
      }

      // If the character limit is reached
      if (length === maxChars) {
        $formResponse
          .text("Unable to enter more characters")
          .removeClass("success")
          .addClass("error");
        $charCounter.addClass("show-message");

        // Prevent further typing beyond the limit
        // Create a flag to prevent input after limit is reached
        editor.events.on("input", function (e) {
          // Block the input event (prevent characters from being added or deleted)
          if (editor.$el.find(".fr-view").text().length >= maxChars) {
            e.preventDefault();
          }
        });

        return;
      }

      // Under the limit, show success message after typing stops
      clearTimeout(typingTimer);
      typingTimer = setTimeout(function () {
        if (editor.$el.find(".fr-view").text().length < maxChars) {
          $formResponse
            .text("Successfully submitted")
            .removeClass("error")
            .addClass("success");
          $charCounter.addClass("show-message");
        }
      }, typingDelay);
    });
  });

  // ==========================
  // 2. FAQ Accordion Toggle
  // ==========================
  jQuery('.faq-accordion-head').click(function () {
      var currentAccordionBody = jQuery(this).next('.faq-accordion-body');

      // Slide up all other accordion bodies except the one clicked
      jQuery('.faq-accordion-body').not(currentAccordionBody).slideUp(function () {
          // Reset display property after slideUp
          jQuery(this).css('display', '');
      });

      // Slide toggle the current accordion body
      currentAccordionBody.stop(true, true).slideToggle(function () {
          // If the accordion body is visible, set display: flex
          if (jQuery(this).is(':visible')) {
              jQuery(this).css('display', 'flex');
          } else {
              // Optionally, reset to default when hidden
              jQuery(this).css('display', '');
          }
      });

      // Toggle active class on the clicked header
      jQuery(this).toggleClass('active');

      // Remove active class from all other accordion heads
      jQuery('.faq-accordion-head').not(this).removeClass('active');
  });

  // ==========================
  // 3. Like/Dislike Buttons
  // ==========================

  jQuery(".like-button").click(function () {
    let likeBtn = jQuery(this);
    let faqId = likeBtn.data("faq-id");
    let likeCountSpan = likeBtn.find(".like-coounting");
    let unlikeBtn = likeBtn.closest(".faq-accordion").find(".unlike-button");
    let unlikeCountSpan = unlikeBtn.find(".unlike-coounting");

    let currentLikeCount = parseInt(likeCountSpan.text()) || 0;
    let currentUnlikeCount = parseInt(unlikeCountSpan.text()) || 0;

    // If like is already active, just deactivate it
    if (likeBtn.hasClass("active")) {
      likeCountSpan.text(Math.max(0, currentLikeCount - 1));
      likeBtn.removeClass("active");
    } else {
      // Deactivate unlike button if active
      if (unlikeBtn.hasClass("active")) {
        unlikeCountSpan.text(Math.max(0, currentUnlikeCount - 1)); // minus 1 if >0
        unlikeBtn.removeClass("active");
      }

      // Activate like
      likeCountSpan.text(currentLikeCount + 1);
      likeBtn.addClass("active");
    }
  });

  jQuery(".unlike-button").click(function () {
    let unlikeBtn = jQuery(this);
    let faqId = unlikeBtn.data("faq-id");
    let unlikeCountSpan = unlikeBtn.find(".unlike-coounting");
    let likeBtn = unlikeBtn.closest(".faq-accordion").find(".like-button");
    let likeCountSpan = likeBtn.find(".like-coounting");

    let currentLikeCount = parseInt(likeCountSpan.text()) || 0;
    let currentUnlikeCount = parseInt(unlikeCountSpan.text()) || 0;

    // If dislike is already active, just deactivate it
    if (unlikeBtn.hasClass("active")) {
      unlikeCountSpan.text(Math.max(0, currentUnlikeCount - 1));
      unlikeBtn.removeClass("active");
    } else {
      // Deactivate like button if active
      if (likeBtn.hasClass("active")) {
        likeCountSpan.text(Math.max(0, currentLikeCount - 1)); // minus 1 if >0
        likeBtn.removeClass("active");
      }

      // Activate dislike
      unlikeCountSpan.text(currentUnlikeCount + 1);
      unlikeBtn.addClass("active");
    }
  });

  // ==========================
  // 4. Copy Button
  // ==========================
  jQuery(".copy-button").click(function () {
    var question = jQuery(this)
      .closest(".faq-accordion")
      .find(".faq-accordion-head h2")
      .text();
    var answer = jQuery(this)
      .closest(".faq-accordion")
      .find(".faq-accordion-body p")
      .text();
    var textToCopy = "Q: " + question + " Ans: " + answer;

    var tempInput = document.createElement("textarea");
    tempInput.value = textToCopy;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);

    var successMessage = '<div class="copied-success">Copy Successful</div>';
    jQuery(this).closest(".faq-accordion").append(successMessage);
    setTimeout(function () {
      jQuery(".copied-success").fadeOut(500, function () {
        jQuery(this).remove();
      });
    }, 3000);
  });

  // ==========================
  // 6. Pagination
  // ==========================
  var itemsPerPage = 15;
  var totalItems = jQuery(".faq-accordion").length;
  var totalPages = Math.ceil(totalItems / itemsPerPage);

  jQuery("#pagination-demo").twbsPagination({
    totalPages: totalPages,
    visiblePages: 3,
    onPageClick: function (event, page) {
      jQuery(".faq-accordion").hide();
      jQuery('.faq-accordion[data-page="' + page + '"]').show();
    },
  });

  jQuery(".faq-accordion").each(function (index) {
    var page = Math.floor(index / itemsPerPage) + 1;
    jQuery(this).attr("data-page", page);
    if (page === 1) {
      jQuery(this).show();
    } else {
      jQuery(this).hide();
    }
  });

  // ==========================
  // 7. Search with Highlighting
  // ==========================
  jQuery("input[type='search']").on("input", function () {
    var query = jQuery(this).val().toLowerCase();

    if (query !== "") {
      jQuery(".faq-main-content")
        .find("*")
        .each(function () {
          var $node = jQuery(this);
          var text = $node.text();
          if (
            $node.children().length === 0 &&
            text.toLowerCase().includes(query)
          ) {
            var newText = text.replace(
              new RegExp("\\b" + query + "\\b", "gi"),
              function (match) {
                return '<span class="highlighted">' + match + "</span>";
              }
            );
            $node.html(newText);
          }
        });
    } else {
      jQuery(".faq-main-content")
        .find(".highlighted")
        .each(function () {
          var $highlightedNode = jQuery(this);
          $highlightedNode.replaceWith($highlightedNode.text());
        });
    }
  });

  // 3sep 2025 (Usama)

  // Open the popup when any delete button is clicked
  jQuery(".delete-button").on("click", function () {
    jQuery("#custom-faq-field-popup").addClass("active");
  });

  // Close popup on cross icon
  jQuery(".popup-form-cross-icon").on("click", function () {
    jQuery("#custom-faq-field-popup").removeClass("active");
  });

  // Close popup on clicking 'No' or 'Cancel' button
  jQuery("#custom-faq-field-popup .no-cancel").on("click", function () {
    jQuery("#custom-faq-field-popup").removeClass("active");
  });

  // Close popup when clicking outside of the popup inner area
  jQuery(document).on("click", function () {
    // Check if the click is outside the popup inner
    if (!jQuery("#custom-faq-field-popup-inner").length) {
      jQuery("#custom-faq-field-popup").removeClass("active");
    }
  });

  // Prevent click inside the popup from closing it
  jQuery("#custom-faq-field-popup-inner").on("click", function () {
    e.stopPropagation();
  });

  // Add functionality for confirming deletion
  jQuery("#custom-faq-field-popup #yes-cancel").on("click", function () {
    // Close the popup immediately after clicking "Yes"
    jQuery("#custom-faq-field-popup").removeClass("active");

    // Show the success message after a brief delay (0.5s)
    setTimeout(function () {
      // Append success message to the body or a specific container
      jQuery(".faq-main-content").append(
        '<div class="success-message">Successfully Deleted</div>'
      );

      // Hide the success message after 3 seconds
      setTimeout(function () {
        jQuery(".success-message").fadeOut(function () {
          jQuery(this).remove(); // Remove the message from the DOM after it fades out
        });
      }, 1500); // 3 seconds after showing the message
    }, 200); // Show the message 0.5 seconds after clicking "Yes"
  });

  // FAQ accordion body ke andar ke empty p, li, aur child elements ko hide karo
  jQuery(".faq-accordion-body")
    .find("p, li")
    .each(function () {
      // Check agar content empty ho (text ya html content ke hisaab se)
      if (jQuery(this).html().trim() === "") {
        jQuery(this).css({
          position: "absolute",
          opacity: "0",
          visibility: "hidden", // Optional, agar aap chahein ki woh element visually aur interactively bhi disappear ho
        });
      }
    });

  // Agar kisi aur empty element ko hide karna hai
  jQuery(".faq-accordion-body")
    .children()
    .each(function () {
      if (jQuery(this).is(":empty")) {
        jQuery(this).css({
          position: "absolute",
          opacity: "0",
          visibility: "hidden",
        });
      }
    });

  const maxCharLimit = 3000;  // Set character limit to 3000
  let userTypingTimer;
  const typingDelay = 2000;  // Delay for showing success message after typing stops
  
  // Initialize character count and other elements
  const $charCounterContainer = jQuery('.char-counter');
  const $currentCharCount = $charCounterContainer.find('.current-count');
  const $responseMessage = jQuery('.form-response'); // Assuming you have a response element for error/success

  // Initialize Froala Editor
  var editor = new FroalaEditor('.editor', {
    events: {
      'input': function() {
        const editorContent = this.html.get(); // Get the content of the editor
        const currentLength = editorContent.replace(/<[^>]+>/g, '').length; // Remove HTML tags to count plain text length

        // Update character count
        $currentCharCount.text(currentLength);

        // Disable input and show error message if character limit is reached
        if (currentLength >= maxCharLimit) {
          const truncatedContent = editorContent.substring(0, maxCharLimit);
          this.html.set(truncatedContent);  // Truncate text if it exceeds maxCharLimit
          $currentCharCount.text(maxCharLimit);  // Update the count to show max limit
          $responseMessage.text('Unable to add more characters').removeClass('success').addClass('error');
          $charCounterContainer.addClass('show-message'); // Show error message
          return; // Prevent further text input
        }

        // Clear error message if under the limit
        if ($responseMessage.hasClass('error')) {
          $responseMessage.text('').removeClass('error');
          $charCounterContainer.removeClass('show-message');
        }

        // If length is under maxCharLimit, show success message after typing stops
        clearTimeout(userTypingTimer);
        userTypingTimer = setTimeout(function() {
          if (currentLength < maxCharLimit) {
            $responseMessage.text('Successfully added').removeClass('error').addClass('success');
            $charCounterContainer.addClass('show-message'); // Show success message
          }
        }, typingDelay);
      }
    }
  });

});
