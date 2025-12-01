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
        jQuery(this).css("display", "block");
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
    visiblePages: totalPages,
    onPageClick: function (event, page) {
      jQuery(".faq-accordion").hide();
      jQuery('.faq-accordion[data-page="' + page + '"]').show();
      var totalActiveItems = jQuery(".faq-accordion.active").length;
      var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

      // Loop through each page <li> (exclude Prev/Next)
      // Loop through each page <li> (exclude Prev/Next)
      jQuery('.pagination-ctn ul li.page-item').nextAll().not('.next').show();
      jQuery(".pagination-ctn ul li.page-item").not(".prev, .next").each(function () {
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
      // ========= NEW CODE: 1 hamesha show + center dots =========
      // Agar koi active page hi nahi to dots ka scene hi nahi
      if (!totalActivePages) {
        return;
      }


      var $pager = jQuery(".pagination-ctn ul");

      // Pichle custom dots hata do (refresh ke liye)
      $pager.find("li.page-item.cust-ellipsis").remove();

      // Sirf number waale page items (prev/next/first/last ko hata ke)
      var $numItems = $pager.find("li.page-item").not(".prev, .next, .first, .last");

      // Pehle sab numeric pages ko hide kar dete hain
      $numItems.each(function () {
        var n = parseInt(jQuery(this).text(), 10);
        if (isNaN(n)) return;

        // Sirf unhi numbers ke sath kaam jahan n <= totalActivePages
        if (n > totalActivePages) {
          jQuery(this).hide();
        }
      });

      // Ab decide karte hain kaun se page dikhane hain
      var sideRange = 1; // current ke 1-1 neighbour

      $numItems.each(function () {
        var n = parseInt(jQuery(this).text(), 10);
        if (isNaN(n) || n > totalActivePages) return;

        // hamesha show:
        // 1, lastActivePage, current, current-1, current+1
        if (
          n === 1 ||
          n === totalActivePages ||
          n === page ||
          n === page - sideRange ||
          n === page + sideRange
        ) {
          jQuery(this).show();
        } else {
          jQuery(this).hide();
        }
      });

      // 1st page <li> aur lastActivePage <li> pakdo
      var $page1 = $numItems.filter(function () {
        return parseInt(jQuery(this).text(), 10) === 1;
      });
      var $lastPage = $numItems.filter(function () {
        return parseInt(jQuery(this).text(), 10) === totalActivePages;
      });

      // Ensure page 1 visible
      if ($page1.length) {
        $page1.show();
      }

      // Dots after 1 (agar 1 ke baad direct 2 na ho visible mein)
      if ($page1.length && $page1.is(":visible")) {
        var $after1 = $page1.nextAll("li.page-item")
          .not(".prev,.next,.first,.last")
          .filter(":visible")
          .first();

        if ($after1.length) {
          var nAfter = parseInt($after1.text(), 10);
          if (!isNaN(nAfter) && nAfter > 2) {
            jQuery('<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>')
              .insertAfter($page1);
          }
        }
      }

      // Ensure last active page visible
      if ($lastPage.length) {
        $lastPage.show();
      }

      // Dots before lastActivePage (agar us se pehle vala visible number lastActivePage - 1 na ho)
      if ($lastPage.length && $lastPage.is(":visible")) {
        var $beforeLast = $lastPage.prevAll("li.page-item")
          .not(".prev,.next,.first,.last")
          .filter(":visible")
          .first();

        if ($beforeLast.length) {
          var nBefore = parseInt($beforeLast.text(), 10);
          if (!isNaN(nBefore) && nBefore < (totalActivePages - 1)) {
            jQuery('<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>')
              .insertBefore($lastPage);
          }
        }
      }
      // ========= NEW DOTS CODE END =========
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
