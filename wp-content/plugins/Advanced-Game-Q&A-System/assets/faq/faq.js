jQuery(document).ready(function ($) {
  // ==========================
  // 1. Initialize Froala Editor
  // ==========================
  // jQuery(".editor-faq").each(function () {
  //   var editor = new FroalaEditor(this, {
  //     toolbarButtons: [
  //       "bold",
  //       "italic",
  //       "underline",
  //       "strikeThrough",
  //       "fontFamily",
  //       "fontSize",
  //       "color",
  //       "paragraphFormat",
  //       "align",
  //       "formatOL",
  //       "formatUL",
  //       "outdent",
  //       "indent",
  //       "clearFormatting",
  //       "insertLink",
  //       "undo",
  //       "redo",
  //       "fullscreen",
  //       "html",
  //     ],
  //     imageUpload: false,
  //     videoUpload: false,
  //     fileUpload: false,
  //   });

  //   const maxChars = 2999; // Set the max character count to 3000
  //   let typingTimer;
  //   const typingDelay = 500;

  //   jQuery(".form-field-editor").on("input", function () {
  //     const $editorContent = jQuery(".fr-view");
  //     const $charCounter = jQuery(".form-field-editor .char-counter");
  //     const $currentCount = $charCounter.find(".current-count");
  //     const $formResponse = jQuery(".form-field-editor .form-response");

  //     // Get the text content and check its length
  //     let text = $editorContent.text();
  //     let length = text.length;

  //     // Limit the text to maxChars
  //     if (length > maxChars) {
  //       editor.html.set(text.substring(0, maxChars)); // Truncate the text
  //       text = editor.html.get();
  //     }

  //     // Update the character counter
  //     $currentCount.text(length);

  //     // Reset messages
  //     if ($formResponse.hasClass("success")) {
  //       $formResponse.text("").removeClass("success");
  //       $charCounter.removeClass("show-message");
  //     }

  //     // If the input is empty
  //     if (length === 0) {
  //       $formResponse.text("").removeClass("error success");
  //       $charCounter.removeClass("show-message");
  //       return;
  //     }

  //     // If the character limit is reached
  //     if (length === maxChars) {
  //       $formResponse
  //         .text("Unable to enter more characters")
  //         .removeClass("success")
  //         .addClass("error");
  //       $charCounter.addClass("show-message");

  //       // Prevent further typing beyond the limit
  //       // Create a flag to prevent input after limit is reached
  //       editor.events.on("input", function (e) {
  //         // Block the input event (prevent characters from being added or deleted)
  //         if (editor.$el.find(".fr-view").text().length >= maxChars) {
  //           e.preventDefault();
  //         }
  //       });

  //       return;
  //     }

  //     // Under the limit, show success message after typing stops
  //     clearTimeout(typingTimer);
  //     typingTimer = setTimeout(function () {
  //       if (editor.$el.find(".fr-view").text().length < maxChars) {
  //         $formResponse
  //           .text("Successfully submitted")
  //           .removeClass("error")
  //           .addClass("success");
  //         $charCounter.addClass("show-message");
  //       }
  //     }, typingDelay);
  //   });
  // });

  // ==========================
  // 2. FAQ Accordion Toggle
  // ==========================
  jQuery(".faq-accordion-head").click(function () {
    var currentAccordionBody = jQuery(this).next(".faq-accordion-body");

    // Slide up all other accordion bodies except the one clicked
    jQuery(".faq-accordion-body")
        .not(currentAccordionBody)
        .slideUp(function () {
          // Reset display property after slideUp
          jQuery(this).css("display", "");
        });

    // Slide toggle the current accordion body
    currentAccordionBody.stop(true, true).slideToggle(function () {
      // If the accordion body is visible, set display: flex
      if (jQuery(this).is(":visible")) {
        jQuery(this).css("display", "flex");
      } else {
        // Optionally, reset to default when hidden
        jQuery(this).css("display", "");
      }
    });

    // Toggle active class on the clicked header
    jQuery(this).toggleClass("active");

    // Remove active class from all other accordion heads
    jQuery(".faq-accordion-head").not(this).removeClass("active");
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
        .find(".faq-accordion-body")
        .text().trim();
    var textToCopy = "Q: " + question + "\nAns: " + answer;
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
      var totalActiveItems = jQuery(".faq-accordion.active").length;
      var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

      // Loop through each page <li> (exclude Prev/Next)
      // Loop through each page <li> (exclude Prev/Next)
      jQuery('.pagination-ctn ul li.page-item').nextAll().not('.next').show();
      jQuery(".pagination-ctn ul li.page-item").not(".prev, .next").each(function() {
        var pageNumberss = parseInt(jQuery(this).text()); // Get the number of the page

        if (pageNumberss === totalActivePages && totalActivePages !== 0) {

          // Remove all <li> items that come after this one
          jQuery(this).nextAll().not('.next').hide();

          // Check the <li> just before the Next button
          var prevLi = jQuery(".pagination-ctn ul li.page-item.active").next();

          // If the next page is hidden or .next button is visible, disable the next button
          if (prevLi.is(":hidden")) {
            jQuery(".pagination-ctn ul li.next").addClass("disabled"); // Disable Next button
          } else {
            jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
          }

          // Break the loop since we found the match
          // return false;
        }
      });
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


  // 3sep 2025 (Usama)

  jQuery(".delete-button,.delete-user-button").on("click", function () {
    // First, remove the 'active' class from all popups
    jQuery("#custom-faq-field-popup").removeClass("active");

    // Then, add the 'active' class to the clicked popup
    jQuery(this).prev("#custom-faq-field-popup").addClass("active");
  });
  // Close popup on cross icon
  jQuery(".popup-form-cross-icon, .no-cancel").on("click", function () {
    jQuery(".agqa-delete-popup-faq").removeClass("active");
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
});
