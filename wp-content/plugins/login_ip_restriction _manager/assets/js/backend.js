jQuery(document).ready(function ($) {
  // Handle form submission
  $("#cuim-add-form-user-man").on("submit", function (e) {
    e.preventDefault(); // Prevent the default form submission
    var $form = jQuery(this);
    var formData = $form.serialize();
    var isValid = true; // Flag to check if the form is valid

    // Check if required fields are filled
    $form.find("[required]").each(function () {
      if ($(this).val().trim() === "") {
        isValid = false; // Mark as invalid if the required field is empty
      }
    });

    // If the form is not valid, show error and return early
    if (!isValid) {
      return; // Stop further processing if the form is invalid
    }
    var nonce = cuim_ajax.nonce; // Nonce for security

    // Send the AJAX request
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "add_or_update_user",
        lang: window.location.pathname.startsWith("/zh") ? "zh" : "en",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // console.log(response);

        // Check if the response contains success
        if (response.success) {
          // If successful, show a success message
          jQuery("div#confirm-submit-popup").removeClass("active");
          const $successMsg = $(
            `<div class="submitted-successfully">${response.data.message}</div>`
          );
          $form.append($successMsg);
          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
              $(this).remove();
            });
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
          }, 3000);
        } else {
          console.log(response.data.message);
          jQuery("div#confirm-submit-popup").removeClass("active");
          const $successMsg = $(
            `<div class="submitted-unsuccessfully">${response.data.message}</div>`
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
      error: function (response) {
        // console.log(response.data.message);
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
        // Check if the response contains success
        if (response.success) {
          // If successful, show a success message
          const successText = window.location.pathname.startsWith("/zh")
            ? "編輯成功"
            : "Edit Successful";

          const $successMsg = $(`<div class="submitted-successfully">${successText}</div>`);
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

  /**
   * Rest link pending user script
   */
  $("#reset-link-pending-user").on("click", function (e) {
    e.preventDefault(); // Prevent the default form submission
    var $form = jQuery("#edit-form-user-manage");
    var formData = $form.serialize();
    var nonce = cuim_ajax.nonce; // Nonce for security
    jQuery(this).addClass("reset-link-disabled");

    // Start the countdown from 60 seconds
    let countdown = 60;

    // Replace the text with the countdown
    jQuery(this).text(`Resend(${countdown})`);

    // Set an interval to update the countdown every second
    let countdownInterval = setInterval(function () {
      countdown--; // Decrease the countdown by 1

      // Update the text with the current countdown value
      jQuery("#reset-link-pending-user").text(`Resend(${countdown})`);

      // Once the countdown reaches 0, stop the interval and reset the text
      if (countdown === 0) {
        clearInterval(countdownInterval);
        jQuery("#reset-link-pending-user")
          .removeClass("reset-link-disabled")
          .text("Reset link");
      }
    }, 1000); // Update every second (1000 ms)

    // Send the AJAX request
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "resend_pending_email",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // alert(response);

        // Check if the response contains success

        if (response.success) {
          // English Version (clean & natural)
          const englishMsg = "Resend Verification Email Successful<br>" +
            "Verification email has been resent to your<br>" +
            "Registered email address. Please check your inbox.";

          // Chinese Traditional Version (bilkul native feel)
          const chineseMsg = "重新寄送驗證信成功。<br>" +
            "驗證信已重新寄送至您註冊的電子郵件信箱<br>" +
            "請至收件匣查看。";

          // Auto detect language
          const finalMessage = window.location.pathname.startsWith("/zh")
            ? chineseMsg
            : englishMsg;

          // Show the message
          const $successMsg = $(`<div class="submitted-successfully">${finalMessage}</div>`);
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
   * Generate new password script
   */

  // When "Yes" is clicked in the confirmation popup
  $("#agqa-reset-password").on("click", function () {
    var $form = jQuery("#edit-form-user-manage");
    var formData = $form.serialize();
    var nonce = cuim_ajax.nonce; // Nonce for security
    let countdown = 58; // Set initial countdown value (58 seconds)
    const $this = $("#generate-password-button"); // Cache the reference to the button element

    // Hide the confirmation popup
    $("#reset-password-confirmation").removeClass("active");

    // Disable the button and update the text
    $this.addClass("resend-password-disabled");
    // Start the countdown
    let countdownInterval = setInterval(function () {
      countdown--; // Decrease countdown by 1

      // Update the text with the current countdown
      $this.text(`Resend (${countdown}s)`);

      // Once the countdown reaches 0, stop the interval and reset the text
      if (countdown === 0) {
        clearInterval(countdownInterval);
        $this
          .removeClass("resend-password-disabled")
          .text("Generate New Password");

        // Perform the AJAX request once the countdown finishes
      }
    }, 1000); // Update every second (1000 ms)
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "reset_password_handler",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // alert(response);
        if (response.success) {
          const isChinese = window.location.pathname.startsWith("/zh");

          const successText = isChinese
            ? "密碼重設成功。<br>重設連結已寄出，請至您的帳號電子郵件查看。<br>您可以在 60 秒後再次請求。"
            : "Reset Password Successful<br>Reset link sent. Please check your account email.<br>You can request another one in 60 seconds.";

          const $successMsg = $(`
        <div class="submitted-successfully created-successfully">
            ${successText}
        </div>
    `);

          $("body").append($successMsg);

          setTimeout(() => {
            $successMsg.fadeOut(400, () => $successMsg.remove());
          }, 3000);
        } else {
          const $errorMsg = $(
            `<div class="submitted-unsuccessfully">${response.data.message}</div>`
          );
          $("body").append($errorMsg);
          setTimeout(
            () => $errorMsg.fadeOut(400, () => $errorMsg.remove()),
            3000
          );
        }
      },
      error: function () {
        alert("There was an error processing your request.");
      },
    });
  });

  /**
   * user manage filter
   **/
  $(".manage-user-template #agqa-user-filters").on("click", function (event) {
    event.preventDefault(); // Prevent form submission
    var searchTerm = $("#manage-user-search").val().toLowerCase(); // Get search term
    var selectedStat = $("input#filter-select-states").val().toLowerCase(); // Get selected state
    var selectedRole = $("input#filter-select-roles").val().toLowerCase(); // Get selected role
    var selectedCompany = $("input#filter-select-companies")
      .val()
      .toLowerCase(); // Get selected company
    var dateRange = $("#daterange").val(); // Get selected date range from inputa

    // If date range is selected, parse the start and end dates as strings
    var dateArray = dateRange.split(" - ");
    var startDate = dateArray[0] || ""; // Start date string in "YYYY/MM/DD" format
    var endDate = dateArray[1] || ""; // End date string in "YYYY/MM/DD" format

    // alert(selectedStat + " " + selectedRole + " " + selectedCompany + " " + dateRange);
    // alert(endDate);
    jQuery(".custom-table-row").removeClass("active");
    jQuery(".pagination-ctn ul li.prev").addClass("disabled"); // Enable Next button
    var resultsFound = false; // Flag to track if any result is found


    if (
      !searchTerm &&
      !selectedStat &&
      !selectedRole &&
      !selectedCompany &&
      !dateRange
    ) {
      $(".section-found").hide(); // Hide the 'nothing found' message
      $(".custom-table-ctn").show(); // Hide the 'nothing found' message

      $(".custom-table-row").show(); // Show the FAQ item
      $("#pagination-demo").show(); // Show the FAQ item
      jQuery(".custom-table-row").addClass("active");

      setTimeout(function () {
        // Recalculate pagination based on the filtered visible items
        var itemsPerPages = 15;
        var totalItemss = $(".custom-table-row").length; // Count only visible items after filtering
        var totalPages = Math.ceil(totalItemss / itemsPerPages);
        $(".custom-table-row").removeAttr("data-page"); // Remove the data-page attribute
        // Reinitialize pagination
        $(".custom-table-row").each(function (index) {
          var pageNumber = Math.floor(index / itemsPerPages) + 1;
          // var pageNumber = "sajid";
          jQuery(this).attr("data-page", pageNumber);
          jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
            .addClass("active")
            .siblings()
            .removeClass("active");
          jQuery(".custom-table-row").hide();
          jQuery('.custom-table-row[data-page="' + "1" + '"]').show();
        });
        jQuery(".pagination-ctn ul li.page-item").show();
        jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
        // jQuery(".pagination-ctn ul li.page-item").not(".prev, .next").each(function () {
        //   var pageNumbers = parseInt(jQuery(this).text()); // Get the number of the page
        //   if (pageNumbers === totalPages && totalPages !== 0) {
        //
        //     // Remove all <li> items that come after this one
        //     jQuery(this).nextAll().not('.next').hide();
        //
        //     // Check the <li> just before the Next button
        //     var prevLi = jQuery(".pagination-ctn ul li.page-item.active").next();
        //
        //     // If the next page is hidden or .next button is visible, disable the next button
        //     if (prevLi.is(":hidden")) {
        //       jQuery(".pagination-ctn ul li.next").addClass("disabled"); // Disable Next button
        //     } else {
        //       jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
        //     }
        //
        //
        //   }
        // });

        applyCustomDots(totalPages);
      }, 500); // Delay of 500 milliseconds
      return; // Return early if either is empty
    }

    // Initially hide pagination and "Nothing Found" message
    $(".section-found").hide(); // Hide "Nothing Found" message
    $(".custom-table-ctn").show(); // Hide "Nothing Found" message

    $("div#pagination-demo").hide(); // Hide pagination

    $(".custom-table-row").each(function () {
      var rowText = $(this).find(".table-body-col-text").text().toLowerCase(); // Get all text inside the row
      var rowCategory = $(this).find(".table-row-status").data('user-status-mg-value').toLowerCase(); // Get the state of the row
      var rowRole = $(this).find(".table-row-user-role").data('user-role-value').toLowerCase(); // Get the role of the row
      var rowCompany = $(this).find(".table-row-company").text().toLowerCase(); // Get the company of the row
      var rowDateText = $(this).find(".table-body-col-date").text().trim(); // Get the date from the row (e.g., "2025/09/17")

      // Apply filters based on exact match for state, role, company, and search term
      var isStateMatch =
        selectedStat === "" || rowCategory.trim() === selectedStat; // Exact match for state
      var isRoleMatch = selectedRole === "" || rowRole === selectedRole; // Exact match for role
      var isCompanyMatch =
        selectedCompany === "" || rowCompany === selectedCompany; // Exact match for company
      var isSearchMatch = rowText.includes(searchTerm); // Check if the search term is found anywhere in the row content

      // alert(rowDateText);

      // Ensure that the row date matches the selected date range
      var isDateMatch = true; // Default to true (if no date range is selected)
      if (startDate && endDate) {
        // Check if the row's date is within the range
        isDateMatch = rowDateText >= startDate && rowDateText <= endDate; // Lexicographical comparison works for "YYYY/MM/DD"
      } else if (startDate) {
        isDateMatch = rowDateText >= startDate; // If only start date is selected, check if the row's date is after start date
      } else if (endDate) {
        isDateMatch = rowDateText <= endDate; // If only end date is selected, check if the row's date is before end date
      }

      // alert(isDateMatch);
      // Apply filter only if the row matches the selected state exactly
      if (
        isStateMatch &&
        isRoleMatch &&
        isCompanyMatch &&
        isSearchMatch &&
        isDateMatch
      ) {
        $(this).show(); // Show the row if it matches the filters
        resultsFound = true; // Mark that at least one result is found
      } else {
        $(this).hide(); // Hide the row if it does not match the filters
      }
    });

    // If no results are found, show the 'nothing found' message
    if (!resultsFound) {
      $(".section-found").show(); // Show the 'no results' message
      $(".custom-table-ctn").hide(); // Show the 'no results' message
      $("div#pagination-demo").hide(); // Hide pagination
    } else {
      $("div#pagination-demo").show(); // Show pagination
      $(".section-found").hide(); // Hide the 'nothing found' message
      $(".custom-table-ctn").show(); // Show the 'no results' message
    }

    setTimeout(function () {
      // Recalculate pagination based on the filtered visible items
      var itemsPerPages = 15;
      var totalItemss = $(".custom-table-row:visible").length; // Count only visible items after filtering
      var totalPages = Math.ceil(totalItemss / itemsPerPages);

      $(".custom-table-row").removeAttr("data-page"); // Remove the data-page attribute
      // Reinitialize pagination
      $(".custom-table-row:visible").each(function (index) {
        var pageNumber = Math.floor(index / itemsPerPages) + 1;
        // var pageNumber = "sajid";
        jQuery(this).attr("data-page", pageNumber);
        jQuery(this).addClass("active");
        jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
          .addClass("active")
          .siblings()
          .removeClass("active");
        if (pageNumber === 1) {
          $(this).show(); // Show items that belong to the current page
        } else {
          $(this).hide(); // Hide items that do not belong to the current page
        }
      });
      jQuery(".pagination-ctn ul li.page-item").show();
      jQuery(".pagination-ctn ul li.page-item")
        .not(".prev, .next")
        .each(function () {
          var pageNumbers = parseInt(jQuery(this).text()); // Get the number of the page
          if (pageNumbers === totalPages && totalPages !== 0) {
            // Remove all <li> items that come after this one
            jQuery(this).nextAll().not(".next").hide();

            // Check the <li> just before the Next button
            var prevLi = jQuery(
              ".pagination-ctn ul li.page-item.active"
            ).next();

            // If the next page is hidden or .next button is visible, disable the next button
            if (prevLi.is(":hidden")) {
              jQuery(".pagination-ctn ul li.next").addClass("disabled"); // Disable Next button
            } else {
              jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
            }
          }
          applyCustomDots(totalPages);
        });
    }, 100); // Delay of 500 milliseconds
  });
  function applyCustomDots(totalPages) {
    var $pager = jQuery(".pagination-ctn ul");

    // Agar 1 hi page hai to dots ka koi faida nahi
    if (!totalPages || totalPages <= 1) {
      $pager.find("li.page-item.cust-ellipsis").remove();
      return;
    }

    // Purane wale custom dots hata do
    $pager.find("li.page-item.cust-ellipsis").remove();

    // Sirf number wali li (prev / next ko hata ke)
    var $numItems = $pager.find("li.page-item").not(".prev, .next");

    // Current active page nikaalo (jo tum nth-child(3) se active kar rahe ho)
    var current = parseInt($pager.find("li.page-item.active").text(), 10);
    if (isNaN(current) || current < 1) current = 1;
    if (current > totalPages) current = totalPages;

    // Pehle sab numeric pages ko base state mein hide karo / > totalPages hide
    $numItems.each(function () {
      var n = parseInt(jQuery(this).text(), 10);
      if (isNaN(n)) return;

      if (n > totalPages) {
        jQuery(this).hide();
      } else {
        jQuery(this).hide(); // baad mein select karke show karenge
      }
    });

    var sideRange = 1; // current ke aas paas 1-1 page

    // 1, last, current, current-1, current+1 show karo
    $numItems.each(function () {
      var n = parseInt(jQuery(this).text(), 10);
      if (isNaN(n) || n > totalPages) return;

      if (
        n === 1 ||
        n === totalPages ||
        n === current ||
        n === current - sideRange ||
        n === current + sideRange
      ) {
        jQuery(this).show();
      }
    });

    // 1st page li aur last page li find karo
    var $page1 = $numItems.filter(function () {
      return parseInt(jQuery(this).text(), 10) === 1;
    });
    var $lastPage = $numItems.filter(function () {
      return parseInt(jQuery(this).text(), 10) === totalPages;
    });

    if ($page1.length) $page1.show();
    if ($lastPage.length) $lastPage.show();

    // 1 ke baad dots (agar gap ho)
    if ($page1.length && $page1.is(":visible")) {
      var $after1 = $page1.nextAll("li.page-item")
        .not(".prev,.next")
        .filter(":visible")
        .first();

      if ($after1.length) {
        var nAfter = parseInt($after1.text(), 10);
        if (!isNaN(nAfter) && nAfter > 2) {
          jQuery(
            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
          ).insertAfter($page1);
        }
      }
    }

    // last se pehle dots (agar gap ho)
    if ($lastPage.length && $lastPage.is(":visible")) {
      var $beforeLast = $lastPage.prevAll("li.page-item")
        .not(".prev,.next")
        .filter(":visible")
        .first();

      if ($beforeLast.length) {
        var nBefore = parseInt($beforeLast.text(), 10);
        if (!isNaN(nBefore) && nBefore < totalPages - 1) {
          jQuery(
            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
          ).insertBefore($lastPage);
        }
      }
    }
  }
  /**
   * real time validation account field
   */
  $(".cuim-manage-user-validation-20").on("input", function () {
    const minLength = 4;
    const maxLength = 20;

    const $input = $(this);
    let $errorMessage = $input.siblings("#error-message");
    const value = $input.val();
    const len = value.length;

    setTimeout(function () {
      toggleSubmitButton();
    }, 300);

    // Create error container once
    if ($errorMessage.length === 0) {
      $errorMessage = $(
        '<div id="error-message" class="cuim-validation-error" />'
      ).insertAfter($input);
    }

    // Check for special characters (anything that's not a letter, number, or space)
    const specialChars = /[^a-zA-Z0-9 ]/;
    if (specialChars.test(value)) {
      $input.addClass("error-field-input");
      if (window.location.pathname.startsWith("/zh")) {
        $errorMessage.text("僅允許使用 A–Z、a–z 和 0–9。");
      } else {
        $errorMessage.text("only A–Z, a–z, and 0–9 are allowed.");
      }
      return; // Stop further processing if special characters are found
    }

    if (len === 0) {
      // Empty: clear errors
      $input.removeClass("error-field-input");
      $errorMessage.text("");
    } else if (len < minLength) {
      if (window.location.pathname.startsWith("/zh")) {
        $errorMessage.text(`至少需要 ${minLength} 個字符。`);
      } else {
        $errorMessage.text(`Minimum ${minLength} characters required.`);
      }
    } else if (len > maxLength) {
      // Truncate to max and show message
      $input.val(value.substring(0, maxLength));
      $input.addClass("error-field-input");
      if (window.location.pathname.startsWith("/zh")) {
        $errorMessage.text(`最多允許 ${maxLength} 個字符。`);
      } else {
        $errorMessage.text(`Maximum ${maxLength} characters allowed.`);
      }
    } else {
      // Valid
      $input.removeClass("error-field-input");
      $errorMessage.text("");
    }
  });
  $(".cuim-manage-user-validation-20").on("keydown", function (e) {
    if (e.key === " " || e.keyCode === 32) {
      e.preventDefault(); // Prevent space character
    }
  });
  /**
   * real time validation password field
   */

  $(".cuim-manage-user-pwd-validation-20").on("input", function () {
    const minLength = 8;
    const maxLength = 20;

    const $input = $(this);
    let $errorMessage = $input.siblings("#error-message");
    const value = $input.val();
    const len = value.length;
    setTimeout(function () {
      toggleSubmitButton();
    }, 300);

    // Create error container once
    if ($errorMessage.length === 0) {
      $errorMessage = $(
        '<div id="error-message" class="cuim-validation-error" />'
      ).insertAfter($input);
    }

    // Regular expression for at least one number and one letter (lowercase or uppercase)
    const hasNumber = /[0-9]/.test(value);
    const hasLetter = /[a-zA-Z]/.test(value);

    if (len === 0) {
      // Empty: clear errors
      $input.removeClass("error-field-input");
      $errorMessage.text("");
    } else if (len < minLength) {
      $input.addClass("error-field-input");
      if (window.location.pathname.startsWith("/zh")) {
        $errorMessage.text(`至少需要 ${minLength} 個字符。`);
      } else {
        $errorMessage.text(`Minimum ${minLength} characters required.`);
      }
    } else if (len > maxLength) {
      // Truncate to max and show message
      $input.val(value.substring(0, maxLength));
      $input.addClass("error-field-input");
      if (window.location.pathname.startsWith("/zh")) {
        $errorMessage.text(`最多允許 ${maxLength} 個字符。`);
      } else {
        $errorMessage.text(`Maximum ${maxLength} characters allowed.`);
      }
    } else if (!hasNumber || !hasLetter) {
      // Check if input contains at least one number and one letter
      $input.addClass("error-field-input");
      if (window.location.pathname.startsWith("/zh")) {
        $errorMessage.text("密碼必須至少包含一個數字和一個字母。");
      } else {
        $errorMessage.text("Password must contain at least one number and one letter.");
      }
    } else {
      // Valid
      $input.removeClass("error-field-input");

      $errorMessage.text("");
      var newPassword = $("#new-password").val().trim();
      var confirmPassword = $("#confirm-password").val().trim();
      // let $errorMessage = $input.siblings("#error-message");0
      jQuery("button#save-profile-btn").prop("disabled", true);

      // Check if both passwords match
      if (newPassword === confirmPassword) {
        $input.removeClass("error-field");
        jQuery("button#save-profile-btn").prop("disabled", false);
        jQuery(".cuim-confrim-pasword-error").text(``);
      } else if (newPassword !== "" && confirmPassword !== "") {
        $input.addClass("error-field");
        jQuery("button#save-profile-btn").prop("disabled", true);

        if (window.location.pathname.startsWith("/zh")) {
          jQuery(".cuim-confrim-pasword-error").text("確認密碼必須與新密碼一致");
        } else {
          jQuery(".cuim-confrim-pasword-error").text("The confirmation password must match the new password.");
        }
      }
    }
  });

  /**
   * email validation
   */

  $('input[type="search"].cuim-manage-user-search-validation-254').on(
    "input",
    function () {
      const maxLengthInputSearchs = 254;

      // alert('dd');

      var $input = $("input#manage-user-search");
      $input.removeAttr("maxlength"); // Remove any HTML maxlength attribute
      var $errorMessage = $input.next("#error-message"); // Look for the error message next to the input
      var $input = $input.closest(".form-field"); // Find the parent .form-field of the current input

      // Check if input exceeds maxLength
      if ($input.val().length > maxLengthInputSearchs) {
        $input.val($input.val().substring(0, maxLengthInputSearchs)); // Truncate the value to maxLength
        $input.addClass("error-field-input"); // Add 'error' class to the parent .form-field
        // Append error message if it doesn't already exist
        if ($errorMessage.length === 0) {
          $(
            '<div id="error-message" class="cuim-validation-error">Max 254 characters allowed.</div>'
          ).insertAfter($input); // Insert the error message after the input
        }
      }
    }
  );

  // $(".cuim-manage-user-validation-254").on("input", function () {
  //   var maxLengthInputSerch = 254;
  //   alert(maxLengthInputSerch);
  //   var $input = $(this);
  //   var $errorMessage = $input.next("#error-message"); // Look for the error message next to the input
  //   var $input = $input.closest(".form-field"); // Find the parent .form-field of the current input

  //   setTimeout(function () {
  //     toggleSubmitButton();
  //   }, 300);
  //   // Check if the input exceeds the maxLength
  //   if ($input.val().length > maxLengthInputSerch) {
  //     $input.val($input.val().substring(0, maxLengthInputSerch)); // Truncate the value to maxLength
  //     $input.addClass("error-field-input"); // Add 'error' class to the parent .form-field
  //     // Append error message if it doesn't already exist
  //     if ($errorMessage.length === 0) {
  //       $(
  //         '<div id="error-message" class="cuim-validation-error">Max 254 characters allowed.</div>'
  //       ).insertAfter($input); // Insert the error message after the input
  //     }
  //   } else {
  //     $input.removeClass("error-field-input"); // Remove 'error' class if input is valid
  //     // Remove the error message if input length is valid
  //     if ($errorMessage.length > 0) {
  //       $errorMessage.remove();
  //     }
  //   }
  // });
  var maxLengthInputSerch = 254;

  function getErrorText() {
    var isChinese = window.location.pathname.includes("/zh/");
    return isChinese ? "最多允许 254 个字符。" : "Max 254 characters allowed.";
  }

  function showError($input) {
    var $parent = $input.closest(".form-field");
    var $errorMessage = $input.next("#error-message");
    var errorText = getErrorText();

    $parent.addClass("error-field-input");

    if ($errorMessage.length === 0) {
      $('<div id="error-message" class="cuim-validation-error">' + errorText + "</div>")
        .insertAfter($input);
    } else {
      $errorMessage.text(errorText);
    }
  }

  function removeError($input) {
    var $parent = $input.closest(".form-field");
    var $errorMessage = $input.next("#error-message");

    $parent.removeClass("error-field-input");
    if ($errorMessage.length) $errorMessage.remove();
  }

  // 1) 255 attempt pe typing block + error show
  $(document).on("keydown", ".cuim-manage-user-validation-254", function (e) {
    var $input = $(this);

    // allowed keys
    if (
      e.key === "Backspace" || e.key === "Delete" ||
      e.key === "ArrowLeft" || e.key === "ArrowRight" ||
      e.key === "ArrowUp" || e.key === "ArrowDown" ||
      e.key === "Tab" || e.key === "Home" || e.key === "End" ||
      e.ctrlKey || e.metaKey
    ) return;

    var el = this;
    var hasSelection = el.selectionStart !== el.selectionEnd;

    // 254 pe ho aur user 255 type kar raha ho (selection nahi)
    if (!hasSelection && $input.val().length >= maxLengthInputSerch) {
      e.preventDefault();      // stop 255
      showError($input);       // show error on 255 attempt
    }
  });

  // 2) Paste/drag-drop: agar 255+ aya to trim + error show
  $(document).on("input", ".cuim-manage-user-validation-254", function () {
    var $input = $(this);
    var val = $input.val();

    if (val.length > maxLengthInputSerch) {
      $input.val(val.substring(0, maxLengthInputSerch)); // cut to 254
      showError($input); // error because user attempted 255+
    } else {
      // agar user wapas 254 se kam kar de to error hata do
      removeError($input);
    }

    setTimeout(function () {
      toggleSubmitButton();
    }, 300);
  });
  // Prevent spaces from being typed into the input field
  // $(".cuim-manage-user-validation-254, .cuim-manage-user-search-validation-254").on("keypress", function (e) {
  //   var keyCode = e.keyCode || e.which;

  //   // Check if the key pressed is a space (keyCode 32)
  //   if (keyCode === 32) {
  //     e.preventDefault(); // Prevent the space from being entered
  //     var $input = $(this);
  //     var $input = $input.closest(".form-field");
  //     var $errorMessage = $input.next("#error-message");

  //     // If the error message doesn't already exist, append it
  //     if ($errorMessage.length === 0) {
  //       setTimeout(function () {
  //         toggleSubmitButton();
  //       }, 300);
  //       $(
  //         '<div id="error-message" class="cuim-validation-error">Spaces are not allowed.</div>'
  //       ).insertAfter($input); // Insert the error message
  //       $input.addClass("error-field-input");
  //     }
  //   }
  // });
  $(".cuim-manage-user-validation-254, .cuim-manage-user-search-validation-254").on("keypress", function (e) {
    var keyCode = e.keyCode || e.which;

    // اگر سپیس پریس کیا تو روک دو
    if (keyCode === 32) {
      e.preventDefault(); // سپیس ان پٹ میں نہ جائے

      var $input = $(this);                    // اصل ان پٹ فیلڈ
      var $parent = $input.closest(".form-field"); // صرف کلاس اور ایریر کے لیے
      var $error = $input.next("#error-message"); // پہلے سے موجود ایریر میسج

      // انگلش یا چائنیز میسج
      var isChinese = window.location.pathname.includes('/zh/');
      var errorText = isChinese ? "不允许使用空格。" : "Spaces are not allowed.";

      // اگر ایریر میسج پہلے سے نہیں ہے تو بناؤ
      if ($error.length === 0) {
        $('<div id="error-message" class="cuim-validation-error">' + errorText + '</div>')
          .insertAfter($input); // input کے فوراً بعد لگے گا
      } else {
        $error.text(errorText); // اگر پہلے سے ہے تو صرف ٹیکسٹ بدلو (زبان تبدیل ہونے پر)
      }

      $parent.addClass("error-field-input");

      // Submit button کو ڈس ایبل کرنے کے لیے
      setTimeout(function () {
        toggleSubmitButton();
      }, 300);
    }
  });
  /**
   * custom field validation
   */

  // $(".cuim-manage-user-validation-50").on("input", function () {
  //   var maxLengthInputUserCustom = 50;
  //   var $input = $(this);
  //   var $errorMessage = $input.next("#error-message");
  //   var $input = $input.closest(".form-field");
  //   // Check if input contains special characters (anything that's not a letter, number, or space)
  //   var specialChars = /[^a-zA-Z0-9 ]/;

  //   jQuery("#save-custom-field").prop("disabled", false);

  //   // Check for special characters
  //   if (specialChars.test($input.val())) {
  //     $input.addClass("error-field-input"); // Add 'error' class to the parent .form-field
  //     // Append error message if it doesn't already exist
  //     alert('dfdfdf');
  //     if ($errorMessage.length === 0) {
  //       jQuery("#save-custom-field").prop("disabled", true);
  //       $(
  //         '<div id="error-message" class="cuim-validation-error">Symbols are not allowed.</div>'
  //       ).insertAfter($input); // Insert the error message after the input
  //     }
  //   } else if ($input.val().length > maxLengthInputUserCustom) {
  //     // If input exceeds max length, truncate it and show error
  //     $input.val($input.val().substring(0, maxLengthInputUserCustom)); // Truncate the value to maxLength
  //     $input.addClass("error-field-input"); // Add 'error' class to the parent .form-field
  //     // Append error message if it doesn't already exist
  //     if ($errorMessage.length === 0) {
  //       alert('dfdf')
  //       $(
  //         '<div id="error-message" class="cuim-validation-error">Max 50 characters allowed.</div>'
  //       ).insertAfter($input); // Insert the error message after the input
  //     }
  //   } else {
  //     // Valid input
  //     $input.removeClass("error-field-input"); // Remove 'error' class if input is valid
  //     // Remove the error message if input length is valid
  //     if ($errorMessage.length > 0) {
  //       $errorMessage.remove();
  //     }
  //   }
  // });
  $(".cuim-manage-user-validation-50").on("input", function () {
    var maxLengthInputUserCustom = 50;
    var $inputField = $(this);                                // ← Actual input field
    var $parent = $inputField.closest(".form-field");         // ← Parent div for styling
    var $errorMessage = $inputField.next("#error-message");   // ← Error message after input

    var value = $inputField.val();  // ← Yeh sahi value milegi ab
    var specialChars = /[^a-zA-Z0-9 ]/;

    // Default: button enable
    jQuery("#save-custom-field").prop("disabled", false);

    // Remove old error message first
    if ($errorMessage.length > 0) {
      $errorMessage.remove();
    }

    // 1. Special characters check
    if (specialChars.test(value)) {
      $parent.addClass("error-field-input");
      jQuery("#save-custom-field").prop("disabled", true);

      $('<div id="error-message" class="cuim-validation-error">' +
        (window.location.pathname.startsWith("/zh") ? "不允許使用符號。" : "Symbols are not allowed.") +
        '</div>').insertAfter($inputField);

    }
    // 2. Max length check
    else if (value.length > maxLengthInputUserCustom) {
      $inputField.val(value.substring(0, maxLengthInputUserCustom)); // Truncate
      $parent.addClass("error-field-input");
      jQuery("#save-custom-field").prop("disabled", true);

      $('<div id="error-message" class="cuim-validation-error">' +
        (window.location.pathname.startsWith("/zh") ? "最多只能輸入 50 個字符。" : "Max 50 characters allowed.") +
        '</div>').insertAfter($inputField);
    }
    // 3. Valid input
    else {
      $parent.removeClass("error-field-input");
      jQuery("#save-custom-field").prop("disabled", false);
    }
  });
  /**
   * delete script
   */

  /**
   * Rest link pending user script
   */
  $("#delete-manage-users #yes-cancel").on("click", function (e) {
    e.preventDefault(); // Prevent the default form submission
    var formData = "username=" + jQuery(this).val();
    // var formData = $form.serialize();

    var nonce = cuim_ajax.nonce; // Nonce for security
    // Send the AJAX request
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "delete_manage_user",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // If deletion is successful, hide the popup and remove the FAQ from the DOM

        if (response.success) {
          $("div#custom-faq-field-popup").removeClass("active");

          const deleteSuccessMsg = window.location.pathname.startsWith("/zh")
            ? "使用者刪除成功。"
            : "The user successfully deleted.";

          const $successMsg = $(`<div class="submitted-successfully">${deleteSuccessMsg}</div>`);

          jQuery(".custom-table-body").append($successMsg);
          if (window.location.pathname.startsWith("/zh")) {
            window.location.href = "/zh/manage-user/";
          } else {
            window.location.href = "/manage-user/";
          }
          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
              $(this).remove();
            });
          }, 3000);

          // Add the 'table-body-disabled' class to the table row with the matching username-data
          var username = formData.split("=")[1]; // Get the username from formData
          $(".custom-table-row[username-data='" + username + "']").addClass(
            "table-body-disabled"
          );
        } else {
          alert(response);
        }
      },
      error: function (response) {
        // Error message if AJAX fails
        alert("An error occurred.");
      },
    });
  });

  /**
   * ip user script
   */
  $("#delete-ip-users .yes-cancel").on("click", function (e) {
    e.preventDefault(); // Prevent the default form submission
    var formData = "username=" + jQuery(this).val();
    // var formData = $form.serialize();
    // alert(formData);
    // return;
    var nonce = cuim_ajax.nonce; // Nonce for security
    // Send the AJAX request
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "delete_ip_user",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // If deletion is successful, hide the popup and remove the FAQ from the DOM

        if (response.includes("Success")) {
          $(".agqa-delete-popup-faq").removeClass("active");
          const deleteSuccessText = window.location.pathname.startsWith("/zh")
            ? "刪除成功。"
            : "Successfully Deleted";

          const $successMsg = $(`<div class="submitted-successfully">${deleteSuccessText}</div>`);
          jQuery(".custom-table-body").append($successMsg);
          if (window.location.pathname.startsWith("/zh")) {
            window.location.href = "/zh/manage-ip-whitelist/";
          } else {
            window.location.href = "/manage-ip-whitelist/";
          }
          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
              $(this).remove();
            });
          }, 3000);

          // Add the 'table-body-disabled' class to the table row with the matching username-data
          var username = formData.split("=")[1]; // Get the username from formData
          $(".custom-table-row[username-data='" + username + "']").addClass(
            "table-body-disabled"
          );
        } else {
          alert(response);
        }
      },
      error: function (response) {
        // Error message if AJAX fails
        alert("An error occurred.");
      },
    });
  });

  // jQuery(".toggle-password").on("click", function (e) {
  //   jQuery(this).toggleClass("show-pass");
  //   jQuery(".new-password");
  // });
  $(".toggle-password").on("click", function (e) {
    e.preventDefault();
    var passwordField = $(this).siblings(
      'input[type="password"], input[type="text"]'
    ); // Get the password input inside the same .password-field container

    // Toggle password visibility
    var fieldType =
      passwordField.attr("type") === "password" ? "text" : "password";
    passwordField.attr("type", fieldType); // Toggle the password visibility

    // Toggle the button class and icon
    $(this).toggleClass("show-pass");

    // Optionally change the icon or text on the button based on visibility
  });
  // Toggle for New Password
  // jQuery("#toggle-new-password").on("click", function () {
  //   var $newPasswordField = jQuery("#new-password-field");
  //   var currentType = $newPasswordField.attr("type");
  //   jQuery(this).toggleClass("show-pass");
  //   // Toggle password visibility
  //   if (currentType === "password") {
  //     $newPasswordField.attr("type", "text"); // Show password
  //   } else {
  //     $newPasswordField.attr("type", "password"); // Hide password
  //   }
  // });

  // Toggle for Confirm Password
  jQuery("#toggle-confirm-password").on("click", function () {
    var $confirmPasswordField = jQuery("#confirm-password-field");
    var currentType = $confirmPasswordField.attr("type");
    jQuery(this).toggleClass("show-pass");
    // Toggle password visibility
    if (currentType === "password") {
      $confirmPasswordField.attr("type", "text"); // Show password
    } else {
      $confirmPasswordField.attr("type", "password"); // Hide password
    }
  });

  // Disable or enable submit button based on error messages
  function toggleSubmitButton() {
    let errorExists = false; // Variable to track if there are any errors

    // Loop through each #error-message element
    $(".cuim-validation-error").each(function () {
      if ($(this).text().trim() !== "") {
        errorExists = true; // If an error message is found, set errorExists to true
        return false; // Exit the loop once an error is found
      }
    });

    // If any error message exists, disable the submit button
    if (errorExists) {
      jQuery("#confirm-submit-popup-button").prop("disabled", true);
    } else {
      // Enable submit button if no errors
      jQuery("#confirm-submit-popup-button").prop("disabled", false);
    }
  }

  /**
   * Profile Module JS Section
   */

  jQuery("#cuim-profile-reset-password").on("submit", function (e) {
    e.preventDefault();
    var $form = jQuery(this);
    var formData = $form.serialize();
    var isValid = true; // Flag to check if the form is valid

    // Check if required fields are filled
    $form.find("[required]").each(function () {
      if ($(this).val().trim() === "") {
        isValid = false; // Mark as invalid if the required field is empty
      }
    });

    // If the form is not valid, show error and return early
    if (!isValid) {
      return; // Stop further processing if the form is invalid
    }
    var nonce = cuim_ajax.nonce; // Nonce for security

    // Send the AJAX request
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "cuim_user_change_password",
        lang: window.location.pathname.startsWith("/zh") ? "zh" : "en",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // alert(response);

        // Check if the response contains success
        if (response.success) {
          // If successful, show a success message
          jQuery(".reset-password-popup").removeClass("active");

          const $successMsg = $(
            `<div class="submitted-successfully">${response.data.message}</div>`
          );
          jQuery(".cuim-profile-form-wrapper").append($successMsg);
          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.remove();
            jQuery(".cuim-profile-form-wrapper").removeClass("active");
            if (window.location.pathname.startsWith("/zh")) {
              window.location.href = `/zh/verification/?login-again=1`;
            } else {
              window.location.href = `/verification/?login-again=1`;
            }
          }, 3000);
        } else {
          jQuery("div#confirm-submit-popup").removeClass("active");
          const $successMsg = $(
            `<div class="submitted-unsuccessfully">${response.data.message}</div>`
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

      error: function (response) {
        // Error message if AJAX fails
        alert("An error occurred.");
      },
    });
  });
  /**
   * Check real time pwd
   */
  $(".cuim-profile-check-pwd").on("input", function () {
    const $input = $(this);
    // var newPassword = $("#new-password").val();
    // var confirmPassword = $("#confirm-password").val();
    // let $errorMessage = $input.siblings("#error-message");

    // // Check if both passwords match
    // if (newPassword === confirmPassword && !newPassword) {
    //   $input.removeClass("error-field");
    //   $errorMessage.text(``);
    //   // Hide error message if passwords match
    // } else {
    //   $input.addClass("error-field");
    //   $errorMessage.text(
    //     `The confirmation password must match the new password.`
    //   );
    // }
  });

  /**
   * user_profile_update
   */
  jQuery("#cuim-update-user-profile").on("submit", function (e) {
    e.preventDefault();
    var $form = jQuery(this);
    var blobUrl = jQuery("#cuim-avatar-preview").attr("src");
    // alert(blobUrl);
    // Convert blob: URL to data URL (base64)
    function blobUrlToDataURL(blobUrl) {
      return fetch(blobUrl)
        .then((res) => res.blob())
        .then(
          (blob) =>
            new Promise((resolve, reject) => {
              const reader = new FileReader();
              reader.onloadend = () => resolve(reader.result); // => data:image/png;base64,...
              reader.onerror = reject;
              reader.readAsDataURL(blob);
            })
        );
    }

    var isValid = true;
    $form.find("[required]").each(function () {
      if (jQuery(this).val().trim() === "") {
        isValid = false;
      }
    });
    if (!isValid) return;

    // If it's already a data URL, use as-is; if blob:, convert to data URL first
    const useAjax = (dataUrl) => {
      var formData = $form.serialize();
      formData += "&image=" + encodeURIComponent(dataUrl); // <-- send as string
      var nonce = cuim_ajax.nonce; // Nonce for security

      $.ajax({
        url: cuim_ajax.ajax_url,
        type: "POST",
        data: {
          action: "user_profile_update",
          form_data: formData, // Pass the form data to the server
          nonce: nonce,
        },
        success: function (response) {
          // Check if the response contains success
          if (response.success) {
            // If successful, show a success message

            jQuery(".reset-password-popup").removeClass("active");
            jQuery(".cuim-profile-box img").attr(
              "src",
              response.data.image_url
            );
            var getchangeName = jQuery(
              "form#cuim-update-user-profile #user-name"
            ).val();
            jQuery(".cuim-profile-dropdown-head .cuim-user-name").text(
              getchangeName
            );

            const $successMsg = $(
              `<div class="submitted-successfully">${response.data.message}</div>`
            );
            jQuery(".cuim-profile-form-wrapper").append($successMsg);
            // Hide after 3 seconds
            setTimeout(function () {
              $successMsg.remove();
              jQuery(".cuim-profile-form-wrapper").removeClass("active");
            }, 3000);
          } else {
            jQuery("div#confirm-submit-popup").removeClass("active");
            const $successMsg = $(
              `<div class="submitted-unsuccessfully">${response.data.message}</div>`
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

        error: function (response) {
          // Error message if AJAX fails
          alert("An error occurred.");
        },
      });
    };
    if (typeof blobUrl === "string" && blobUrl.indexOf("data:image/") === 0) {
      // Already a data URL
      useAjax(blobUrl);
    } else if (typeof blobUrl === "string" && blobUrl.indexOf("blob:") === 0) {
      // Convert blob: -> data:
      blobUrlToDataURL(blobUrl)
        .then(useAjax)
        .catch(() => {
          alert("Failed to read the image blob.");
        });
    } else {
      // Fallback (maybe a normal https URL)
      useAjax(blobUrl);
    }
  });

  /**
   *profile-username-validation-100
   */
  $(".profile-username-validation-100").on("input", function () {
    const minLength = 0;
    const maxLength = 100;

    const $input = $(this);
    let $errorMessage = $input.siblings("#error-message");
    const value = $input.val();
    const len = value.length;
    jQuery("#save-update-user-profile").prop("disabled", false);
    // Create error container once
    if ($errorMessage.length === 0) {
      $errorMessage = $(
        '<div id="error-message" class="cuim-validation-error" />'
      ).insertAfter($input);
    }

    // Check for special characters (anything that's not a letter, number, or space)
    const specialChars = /[^a-zA-Z0-9 ]/;
    const hasNumbers = /\d/; // Regex to check for numbers

    if (specialChars.test(value)) {
      if (window.location.pathname.startsWith("/zh")) {
        $input.addClass("error-field-input");
        $errorMessage.text("使用者名稱不可包含特殊字元。");
      } else {
        $errorMessage.text("User name cannot contain special characters.");
      }
      jQuery("#save-update-user-profile").prop("disabled", true);
      return; // Stop further processing if special characters are found
    }

    // Check if the username contains numbers (digits)
    if (hasNumbers.test(value)) {
      if (window.location.pathname.startsWith("/zh")) {
        $input.addClass("error-field-input");
        $errorMessage.text("使用者名稱僅能包含英文字母。");
      } else {
        $errorMessage.text("User name cannot contain special characters.");
      }
      jQuery("#save-update-user-profile").prop("disabled", true);
      return; // Stop further processing if numeric characters are found
    }

    if (len === 0) {
      // Empty: clear errors
      $input.removeClass("error-field-input");
      $errorMessage.text("");
    } else if (len < minLength) {
      $input.addClass("error-field-input");
      if (window.location.pathname.startsWith("/zh")) {
        $errorMessage.text(`至少需要 ${minLength} 個字符。`);
      } else {
        $errorMessage.text(`Minimum ${minLength} characters required.`);
      }
    } else if (len > maxLength) {
      // Truncate to max and show message
      $input.val(value.substring(0, maxLength));
      $input.addClass("error-field-input");
      if (window.location.pathname.startsWith("/zh")) {
        $errorMessage.text(`最多允許 ${maxLength} 個字符。`);
      } else {
        $errorMessage.text(`Maximum ${maxLength} characters allowed.`);
      }
    } else {
      // Valid
      $input.removeClass("error-field-input");
      $errorMessage.text("");
    }
  });
  /**
   * profile and username
   */
  $(".cuim-edit-profile-button").on("click", function () {
    var getImage = jQuery(".cuim-profile-dropdown-head img").attr("src");
    var getName = jQuery(".cuim-profile-dropdown-head .cuim-user-name").text();
    jQuery("img#cuim-avatar-preview").attr("src", getImage);
    jQuery("form#cuim-update-user-profile #user-name").val(getName);

    jQuery("#cuim-profile-reset-password input")
      .val("")
      .removeClass("error-field-input");
    jQuery("#cuim-profile-reset-password div#error-message").text("");
  });

  /**
   * Check User Account for IP Address
   */

  $("#add-ip-btn").on("click", function (e) {
    e.preventDefault();

    // Get form data
    var getFormData = $("#add-ip-from").serialize();

    // Check for empty required fields
    var isValid = true;

    // // Check if at least one of the IP fields is filled
    var ipv4 = $(".manage-ip-ipv4-field").val(); // Assuming the ID for the IPv4 field is 'ipv4'
    var ipv6 = $(".manage-ip-ipv6-field").val(); // Assuming the ID for the IPv6 field is 'ipv6'

    $(".ip-error").text("");

    if (ipv4 === "" && ipv6 === "") {
      isValid = false;
      $(".ip-error").text("Please enter at least one type of IP.");
    }

    // // Check if account field is filled (optional, as it was not mentioned explicitly)
    $(".account-error").text("");
    var account = $("#manage-ip-account-field").val(); // Assuming the ID for the account field is 'account'
    if (account === "") {
      var textTranslate = 'Account is required.';
      if (window.location.pathname.startsWith("/zh")) {
        textTranslate = '帳號為必填項目。';
      }
      isValid = false;
      $(".account-error").text(textTranslate);
      $(".manage-ip-account-field").addClass("error-field");
    }

    // If validation fails, stop the form submission
    if (!isValid) {
      return false;
    }

    var nonce = cuim_ajax.nonce; // Nonce for security

    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "check_user_account",
        form_data: getFormData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // Check if the response contains success
        if (response.success) {
          $("#add-ip-btn").siblings(".confirm-submit-popup").addClass("active");
        } else {
          // alert(response.data.message);
          $(".account-error").text(response.data.message);
          $("#manage-ip-account-field").addClass("error-field");
          return;
        }
      },

      error: function (response) {
        // Error message if AJAX fails
        alert("An error occurred.");
      },
    });
  });
  $(".manage-ip-ipv4-field").on("keydown", function (e) {
    if (e.key === " " || e.keyCode === 32) {
      e.preventDefault(); // Prevent space character
    }
  });
  $(".manage-ip-ipv6-field").on("keydown", function (e) {
    if (e.key === " " || e.keyCode === 32) {
      e.preventDefault(); // Prevent space character
    }
  });
  $(".cuim-profile-check-pwd, .cuim-manage-user-pwd-validation-20").on("keydown", function (e) {
    if (e.key === " " || e.keyCode === 32) {
      e.preventDefault(); // Prevent space character
    }
  });
  // function isValidIPv4(ip) {
  //   const parts = ip.trim().split(".");
  //   if (parts.length !== 4) return false;
  //
  //   for (let part of parts) {
  //     if (!/^\d+$/.test(part)) return false;
  //     const num = Number(part);
  //     if (num < 0 || num > 255) return false;
  //     if (part.length > 1 && part.startsWith("0")) return false;
  //   }
  //
  //   return true;
  // }
  //
  // // $(".manage-ip-ipv4-field").on("focusout", function () {
  // //   const ip = $(this).val();
  // //   const errorBox = $(".error-message.ipv4-error");
  //
  // //   if (isValidIPv4(ip)) {
  // //     $("button#add-ip-btn").prop("disabled", false);
  // //     $(".cuim-edit-button-ip").prop("disabled", false);
  // //     errorBox.text("");
  // //   } else {
  // //     $(".cuim-edit-button-ip").prop("disabled", true);
  // //     $("button#add-ip-btn").prop("disabled", true);
  //
  // //     $(this).next(errorBox).text("Please enter a valid IPv4 address");
  // //   }
  // // });
  //
  // function isValidIPv6(ip) {
  //   // Basic IPv6 pattern (not exhaustive, but covers valid formats)
  //   const ipv6Pattern = new RegExp(
  //     "^(" +
  //       "([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:)|" + // full form
  //       "([0-9A-Fa-f]{1,4}:){1,7}:|" + // :: at end
  //       "([0-9A-Fa-f]{1,4}:){1,6}:[0-9A-Fa-f]{1,4}|" + // :: in middle
  //       "([0-9A-Fa-f]{1,4}:){1,5}(:[0-9A-Fa-f]{1,4}){1,2}|" +
  //       "([0-9A-Fa-f]{1,4}:){1,4}(:[0-9A-Fa-f]{1,4}){1,3}|" +
  //       "([0-9A-Fa-f]{1,4}:){1,3}(:[0-9A-Fa-f]{1,4}){1,4}|" +
  //       "([0-9A-Fa-f]{1,4}:){1,2}(:[0-9A-Fa-f]{1,4}){1,5}|" +
  //       "[0-9A-Fa-f]{1,4}:((:[0-9A-Fa-f]{1,4}){1,6})|" +
  //       ":((:[0-9A-Fa-f]{1,4}){1,7}|:)|" + // starts with ::
  //       "fe80:(:[0-9A-Fa-f]{0,4}){0,4}%[0-9a-zA-Z]{1,}|" + // link-local
  //       "::(ffff(:0{1,4}){0,1}:){0,1}" +
  //       "((25[0-5]|(2[0-4]|1{0,1}[0-9])?[0-9]).){3,3}" +
  //       "(25[0-5]|(2[0-4]|1{0,1}[0-9])?[0-9])|" + // IPv4-mapped IPv6
  //       "([0-9A-Fa-f]{1,4}:){1,4}:" +
  //       "((25[0-5]|(2[0-4]|1{0,1}[0-9])?[0-9]).){3,3}" +
  //       "(25[0-5]|(2[0-4]|1{0,1}[0-9])?[0-9])" +
  //       ")$"
  //   );
  //   return ipv6Pattern.test(ip.trim());
  // }
  // // Helper: buttons ko enable/disable karna ek jagah se
  // function updateIpButtons() {
  //   const ipv4Val = $(".manage-ip-ipv4-field").val()?.trim() || "";
  //   const ipv6Val = $(".manage-ip-ipv6-field").val()?.trim() || "";
  //
  //   const ipv4Ok = ipv4Val === "" || isValidIPv4(ipv4Val);
  //   const ipv6Ok = ipv6Val === "" || isValidIPv6(ipv6Val);
  //
  //   const noErrorsInUI =
  //     $(".error-message.ipv4-error").text().trim() === "" &&
  //     $(".error-message.ipv6-error").text().trim() === "";
  //
  //   const enable = ipv4Ok && ipv6Ok && noErrorsInUI;
  //
  //   $(".cuim-edit-button-ip, button#add-ip-btn").prop("disabled", !enable);
  // }
  //
  // // IPv6
  // $(".manage-ip-ipv6-field").on("focusout", function () {
  //   const ip = $(this).val();
  //   // NOTE: .next() me selector string dein (assumes error span next sibling hai)
  //   const errorBox = $(this).next(".error-message.ipv6-error");
  //
  //   if (isValidIPv6(ip) || ip == "") {
  //     errorBox.text("");
  //   } else {
  //     errorBox.text("Please enter a valid IPv6 address");
  //   }
  //
  //   updateIpButtons();
  // });
  //
  // // IPv4
  // $(".manage-ip-ipv4-field").on("focusout", function () {
  //   const ip = $(this).val();
  //   const errorBox = $(".error-message.ipv4-error");
  //
  //   // alert(isValidIPv4(ip));
  //   if (isValidIPv4(ip) || ip == "") {
  //     errorBox.text("");
  //   } else {
  //     errorBox.text("Please enter a valid IPv4 address");
  //   }
  //
  //   updateIpButtons();
  // });

  // IPv4 Validation
  function isValidIPv4(ip) {
    const parts = ip.trim().split(".");
    if (parts.length !== 4) return false;

    for (let part of parts) {
      if (!/^\d+$/.test(part)) return false;
      const num = Number(part);
      if (num < 0 || num > 255) return false;
      if (part.length > 1 && part.startsWith("0")) return false; // Prevent leading zeros
    }
    return true;
  }

  // IPv6 Validation
  function isValidIPv6(ip) {
    const ipv6Pattern = new RegExp(
      "^(" +
      "([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:)|" + // full form
      "([0-9A-Fa-f]{1,4}:){1,7}:|" + // :: at end
      "([0-9A-Fa-f]{1,4}:){1,6}:[0-9A-Fa-f]{1,4}|" + // :: in middle
      "([0-9A-Fa-f]{1,4}:){1,5}(:[0-9A-Fa-f]{1,4}){1,2}|" +
      "([0-9A-Fa-f]{1,4}:){1,4}(:[0-9A-Fa-f]{1,4}){1,3}|" +
      "([0-9A-Fa-f]{1,4}:){1,3}(:[0-9A-Fa-f]{1,4}){1,4}|" +
      "([0-9A-Fa-f]{1,4}:){1,2}(:[0-9A-Fa-f]{1,4}){1,5}|" +
      "[0-9A-Fa-f]{1,4}:((:[0-9A-Fa-f]{1,4}){1,6})|" +
      ":((:[0-9A-Fa-f]{1,4}){1,7}|:)|" + // starts with ::
      "fe80:(:[0-9A-Fa-f]{0,4}){0,4}%[0-9a-zA-Z]{1,}|" + // link-local
      "::(ffff(:0{1,4}){0,1}:){0,1}" +
      "((25[0-5]|(2[0-4]|1{0,1}[0-9])?[0-9]).){3,3}" +
      "(25[0-5]|(2[0-4]|1{0,1}[0-9])?[0-9])|" + // IPv4-mapped IPv6
      "([0-9A-Fa-f]{1,4}:){1,4}:" +
      "((25[0-5]|(2[0-4]|1{0,1}[0-9])?[0-9]).){3,3}" +
      "(25[0-5]|(2[0-4]|1{0,1}[0-9])?[0-9])" +
      ")$"
    );
    return ipv6Pattern.test(ip.trim());
  }

  // Helper function to enable/disable buttons based on validity of IPs
  // function updateIpButtons() {
  //   const ipv4Val = $(".manage-ip-ipv4-field").val()?.trim() || "";
  //   const ipv6Val = $(".manage-ip-ipv6-field").val()?.trim() || "";

  //   // Check if both IPv4 and IPv6 are valid or empty
  //   const ipv4Valid = ipv4Val === "" || isValidIPv4(ipv4Val);
  //   const ipv6Valid = ipv6Val === "" || isValidIPv6(ipv6Val);

  //   // Check if there are any error messages displayed
  //   const noErrorsInUI =
  //     $(".error-message.ipv4-error").text().trim() === "" &&
  //     $(".error-message.ipv6-error").text().trim() === "";

  //   // Enable buttons if both IPs are valid, or one is valid and the other is empty
  //   const enable =
  //     (ipv4Valid && ipv6Valid) ||
  //     (ipv4Valid && ipv6Val === "") ||
  //     (ipv6Valid && ipv4Val === "");

  //   $(".cuim-edit-button-ip, button#add-ip-btn").prop("disabled", !enable);
  //   $(".button.edit-ip-btn.cuim-edit-button-ip").prop("disabled", !enable);
  // }
  function updateIpButtons() {
    // Validate for the global button (if needed for global input fields like the main add form)
    const ipv4ValGlobal = $(".manage-ip-ipv4-field").val()?.trim() || "";
    const ipv6ValGlobal = $(".manage-ip-ipv6-field").val()?.trim() || "";

    const ipv4ValidGlobal = ipv4ValGlobal === "" || isValidIPv4(ipv4ValGlobal);
    const ipv6ValidGlobal = ipv6ValGlobal === "" || isValidIPv6(ipv6ValGlobal);

    // Check if both IPv4 and IPv6 are valid or empty for the global button
    const enableGlobal =
      (ipv4ValidGlobal && ipv6ValidGlobal) ||
      (ipv4ValidGlobal && ipv6ValGlobal === "") ||
      (ipv6ValidGlobal && ipv4ValGlobal === "");

    // Disable/enable the global button based on the validation
    $(".cuim-edit-button-ip, button#add-ip-btn").prop("disabled", !enableGlobal);

    // Row-wise validation (using .each() for individual rows)
    $(".custom-table-row").each(function () {
      const ipv4Val = $(this).find(".manage-ip-ipv4-field").val()?.trim() || "";
      const ipv6Val = $(this).find(".manage-ip-ipv6-field").val()?.trim() || "";

      // Validate IPv4 and IPv6 for each row
      const ipv4Valid = ipv4Val === "" || isValidIPv4(ipv4Val);
      const ipv6Valid = ipv6Val === "" || isValidIPv6(ipv6Val);

      // Define the enabling condition for the button for this row
      const enableRow =
        (ipv4Valid && ipv6Valid) ||          // both valid
        (ipv4Valid && ipv6Val === "") ||    // IPv4 valid, IPv6 empty
        (ipv6Valid && ipv4Val === "");      // IPv6 valid, IPv4 empty

      // Find the button for this row and enable/disable it based on validation
      const rowButton = $(this).find(".cuim-edit-button-ip, #add-ip-btn");

      if (!enableRow) {
        rowButton.prop("disabled", true);  // Disable button for this row if not valid
      } else {
        rowButton.prop("disabled", false); // Enable button for this row if valid
      }
    });
  }
  // IPv6 Field Focusout Event
  $(".manage-ip-ipv6-field").on("focusout", function () {
    const ip = $(this).val();
    const errorBox = $(this).next(".error-message.ipv6-error");

    if (isValidIPv6(ip) || ip == "") {
      errorBox.text(""); // Clear error if valid or empty
      if ($(".manage-ip-ipv4-field").val() == "") {
        $(".error-message.ipv4-error").text("");
      }
    } else {
      errorBox.text("Please enter a valid IPv6 address");
    }


    updateIpButtons(); // Update button status
  });

  // IPv4 Field Focusout Event
  $(".manage-ip-ipv4-field").on("focusout", function () {
    const ip = $(this).val();
    const errorBox = $(this).next(".error-message.ipv4-error");

    if (isValidIPv4(ip) || ip == "") {
      errorBox.text(""); // Clear error if valid or empty
      if ($(".manage-ip-ipv6-field").val() == "") {
        $(".error-message.ipv6-error").text("");
      }
    } else {
      errorBox.text("Please enter a valid IPv4 address");
    }

    updateIpButtons(); // Update button status
  });

  // Optional: page load par bhi buttons ko state me lao
  $(function () {
    updateIpButtons();
  });

  // $(".manage-ip-ipv6-field").on("focusout", function () {
  //   const ip = $(this).val();
  //   const errorBox = $(".error-message.ipv6-error");

  //   if (isValidIPv6(ip)) {
  //     $(".cuim-edit-button-ip").prop("disabled", false);
  //     $("button#add-ip-btn").prop("disabled", false);

  //     errorBox.text("");
  //   } else {
  //     $(".cuim-edit-button-ip").prop("disabled", true);
  //     $("button#add-ip-btn").prop("disabled", true);

  //     $(this).next(errorBox).text("Please enter a valid IPv6 address");
  //   }
  // });

  /**
   * Add IP Data
   */

  $("#add-ip-from").on("submit", function (e) {
    e.preventDefault();

    // Get form data
    var getFormData = $("#add-ip-from").serialize();

    var nonce = cuim_ajax.nonce; // Nonce for security

    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "handle_add_user_ip",
        form_data: getFormData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // Check if the response contains success
        // alert(response.data.message);
        if (response.success) {
          jQuery(".add-manage-ip-form").removeClass("active");
          const $successMsg = $(`
        <div class="submitted-successfully">${response.data.message}</div>
    `);

          // Append the success message to the custom table body
          jQuery(".custom-table-body").append($successMsg);
          // window.location.reload();
          // Hide the message after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
              $(this).remove();
            });
          }, 3000);
        } else {
          return;
        }
      },

      error: function (response) {
        // Error message if AJAX fails
        alert("An error occurred.");
      },
    });
  });

  /**
   * Edit IP Data
   */

  $(".edit-ip-from-list").on("submit", function (e) {
    e.preventDefault();

    // Check for empty required fields
    var isValid = true;

    // // Check if at least one of the IP fields is filled
    var ipv4 = $(this).find(".manage-ip-ipv4-field").val(); // Assuming the ID for the IPv4 field is 'ipv4'
    var ipv6 = $(this).find(".manage-ip-ipv6-field").val(); // Assuming the ID for the IPv6 field is 'ipv6'
    var check_ipv4 = $(this).find(".ip-edit-ip4-check").val(); // Assuming the ID for the IPv4 field is 'ipv4'
    var check_ipv6 = $(this).find(".ip-edit-ip6-check").val(); // Assuming the ID for the IPv6 field is 'ipv6'

    $(".ip-error").text("");

    if (ipv4 === "" && ipv6 === "") {
      isValid = false;
      $(this).find(".ip-error").text("Please enter at least one type of IP.");
    }
    // If validation fails, stop the form submission
    if (!isValid) {
      return false;
    }
    if (ipv4 === check_ipv4 && ipv6 === check_ipv6) {
      $(this).find(".cuim-edit-submit-popup-again").addClass("active");
    } else {
      $(this).find(".cuim-edit-submit-popup").addClass("active");
    }
  });

  $(".edit-ip-from-list .cuim-submit-again-btn").on("click", function (e) {
    e.preventDefault();
    $(".cuim-edit-submit-popup-again").removeClass("active");
    $(".edit-manage-ip-form").removeClass("active");
    const updateSuccessText = window.location.pathname.startsWith("/zh")
      ? "更新成功。"
      : "Successfully Updated";

    const $successMsg = $(`<div class="submitted-successfully">${updateSuccessText}</div>`);
    jQuery(".add-manage-ip-form").append($successMsg);
    // Hide after 3 seconds
    setTimeout(function () {
      $successMsg.remove();
    }, 3000);
  });

  $(".edit-ip-from-list .cuim-confirm-submit-ip").on("click", function (e) {
    var getFormData = jQuery(this).closest(".edit-ip-from-list").serialize();

    var nonce = cuim_ajax.nonce; // Nonce for security

    const $successMsg = $(`
        <div class="submit-warning">Please Wait...</div>
    `);

    // Append the success message to the custom table body
    jQuery(this).append($successMsg);

    // Hide the message after 3 seconds
    setTimeout(function () {
      $successMsg.fadeOut(300, function () {
        $(this).remove();
      });
    }, 2000);

    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "handle_edit_user_ip_update",
        form_data: getFormData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // Check if the response contains success
        // alert(response.data.message);
        if (response.success) {
          $(".cuim-edit-submit-popup").removeClass("active");
          $(".edit-manage-ip-form").removeClass("active");
          jQuery(".add-manage-ip-form").removeClass("active");
          const $successMsg = $(`
        <div class="submitted-successfully">${response.data.message}</div>
    `);

          // Append the success message to the custom table body
          jQuery(".custom-table-body").append($successMsg);
          window.location.reload();
          // Hide the message after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
              $(this).remove();
            });
          }, 3000);
        }
      },

      error: function (response) {
        // Error message if AJAX fails
        alert("An error occurred.");
      },
    });
  });
  /**
   * IP Search Filter
   */

  $(".cuim-filter-select-list-li li").on("click", function (event) {
    event.preventDefault(); // Prevent form submission
    var getDataLi = jQuery(this).data("value");
    jQuery("." + getDataLi)
      .show()
      .siblings(".cuim-ipv-selected")
      .hide();
  });

  $("#cuim-ip-serch-filters").on("click", function (event) {
    event.preventDefault(); // Prevent form submission

    // var reportType = $("input.agqa-filter-select-hidden").val().toLowerCase();
    var IPaccountText = $("input#manage-ip-account-search").val().toLowerCase();

    var IPTextipv4 = $("#manage-ip-ipv4-search").val().toLowerCase();
    var IPTextipv6 = $("#manage-ip-ipv6-search").val().toLowerCase();

    var resultsFound = false; // Flag to track if any result is found


    if (!IPaccountText && !IPTextipv4 && !IPTextipv6) {
      $(".section-found").hide();
      $(".custom-table-ctn").show();
      $(".custom-table-row").show();
      $("#pagination-demo").show();
      jQuery('.custom-table-row').addClass("active");
      setTimeout(function () {
        // Recalculate pagination based on the filtered visible items
        var itemsPerPages = 15;
        var totalItemss = $(".custom-table-row").length; // Count only visible items after filtering
        var totalPages = Math.ceil(totalItemss / itemsPerPages);
        $(".custom-table-row").removeAttr("data-page"); // Remove the data-page attribute
        // Reinitialize pagination
        $(".custom-table-row").each(function (index) {
          var pageNumber = Math.floor(index / itemsPerPages) + 1;
          // var pageNumber = "sajid";
          jQuery(this).attr("data-page", pageNumber);
          jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
            .addClass("active")
            .siblings()
            .removeClass("active");

          jQuery(".custom-table-row").hide();
          jQuery('.custom-table-row[data-page="' + "1" + '"]').show();
        });
        jQuery(".pagination-ctn ul li.page-item").show();
        jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
        jQuery(".pagination-ctn ul li.prev").addClass("disabled"); // Disable Next button

        var beforeNext = jQuery(".pagination-ctn ul li.next").prev("li");

        if (!beforeNext.is(":hidden") && beforeNext.hasClass("active")) {
          jQuery(".pagination-ctn ul li.next").addClass("disabled");
        }

        applyCustomDots(totalPages);
      }, 500); // Delay of 500 milliseconds
      return; // Return early if either is empty
    }
    jQuery('.custom-table-row').removeClass("active");
    // Initially hide pagination and "Nothing Found" message
    $(".section-found").hide();
    $(".custom-table-ctn").show();
    $("div#pagination-demo").hide();

    $(".custom-table-row").each(function () {
      var IPaccountsearchText = $(this)
        .find(".cuim-ip-user-account")
        .text()
        .toLowerCase();
      // alert(IPaccountsearchText);
      var IPSerchipv4Text = $(this)
        .find(".table-body-col.cuim-ip-user-ipv4")
        .text()
        .toLowerCase();
      var IPSerchipv6Text = $(this)
        .find(".table-body-col.cuim-ip-user-ipv6")
        .text()
        .toLowerCase();
      // Apply filters based on exact match for state, role, company, and search term
      var isIPAccountMatch = IPaccountsearchText.includes(IPaccountText); // Check if the search term is found anywhere in the row content
      var IsIPSerchipv4Match =
        IPTextipv4 === "" || IPSerchipv4Text.trim() === IPTextipv4.trim();
      var IsIPSerchipv6Match =
        IPTextipv6 === "" || IPSerchipv6Text.trim() === IPTextipv6.trim();

      if (isIPAccountMatch && IsIPSerchipv4Match && IsIPSerchipv6Match) {
        $(this).show(); // Show the row if it matches the filters
        resultsFound = true; // Mark that at least one result is found
      } else {
        $(this).hide(); // Hide the row if it does not match the filters
      }
    });

    // If no results are found, show the 'nothing found' message
    if (!resultsFound) {
      $(".section-found").show(); // Show the 'no results' message
      $(".custom-table-ctn").hide(); // Show the 'no results' message
      $("div#pagination-demo").hide(); // Hide pagination
    } else {
      $("div#pagination-demo").show(); // Show pagination
      $(".section-found").hide(); // Hide the 'nothing found' message
      $(".custom-table-ctn").show(); // Show the 'no results' message
    }

    setTimeout(function () {
      // Recalculate pagination based on the filtered visible items
      var itemsPerPages = 15;
      var totalItemss = $(".custom-table-row:visible").length; // Count only visible items after filtering
      var totalPages = Math.ceil(totalItemss / itemsPerPages);

      $(".custom-table-row").removeAttr("data-page"); // Remove the data-page attribute
      // Reinitialize pagination
      $(".custom-table-row:visible").each(function (index) {
        var pageNumber = Math.floor(index / itemsPerPages) + 1;
        // var pageNumber = "sajid";
        jQuery(this).attr("data-page", pageNumber);
        jQuery(this).addClass("active");
        jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
          .addClass("active")
          .siblings()
          .removeClass("active");
        if (pageNumber === 1) {
          $(this).show(); // Show items that belong to the current page
        } else {
          $(this).hide(); // Hide items that do not belong to the current page
        }
      });
      jQuery(".pagination-ctn ul li.page-item").show();
      jQuery(".pagination-ctn ul li.page-item")
        .not(".prev, .next")
        .each(function () {
          var pageNumbers = parseInt(jQuery(this).text()); // Get the number of the page
          if (pageNumbers === totalPages && totalPages !== 0) {
            // Remove all <li> items that come after this one
            jQuery(this).nextAll().not(".next").hide();

            // Check the <li> just before the Next button
            var prevLi = jQuery(
              ".pagination-ctn ul li.page-item.active"
            ).next();
            jQuery(".pagination-ctn ul li.prev").addClass("disabled"); // Disable Next button
            // If the next page is hidden or .next button is visible, disable the next button
            if (prevLi.is(":hidden")) {
              jQuery(".pagination-ctn ul li.next").addClass("disabled"); // Disable Next button
            } else {
              jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
            }

            var beforeNext = jQuery(".pagination-ctn ul li.next").prev("li");

            if (!beforeNext.is(":hidden") && beforeNext.hasClass("active")) {
              jQuery(".pagination-ctn ul li.next").addClass("disabled");
            }
          }
        });
      applyCustomDots(totalPages);
    }, 100); // Delay of 500 milliseconds
  });
  function applyCustomDots(totalPages) {
    var $pager = jQuery(".pagination-ctn ul");

    // Agar 1 hi page hai to dots ka koi faida nahi
    if (!totalPages || totalPages <= 1) {
      $pager.find("li.page-item.cust-ellipsis").remove();
      return;
    }

    // Purane wale custom dots hata do
    $pager.find("li.page-item.cust-ellipsis").remove();

    // Sirf number wali li (prev / next ko hata ke)
    var $numItems = $pager.find("li.page-item").not(".prev, .next");

    // Current active page nikaalo (jo tum nth-child(3) se active kar rahe ho)
    var current = parseInt($pager.find("li.page-item.active").text(), 10);
    if (isNaN(current) || current < 1) current = 1;
    if (current > totalPages) current = totalPages;

    // Pehle sab numeric pages ko base state mein hide karo / > totalPages hide
    $numItems.each(function () {
      var n = parseInt(jQuery(this).text(), 10);
      if (isNaN(n)) return;

      if (n > totalPages) {
        jQuery(this).hide();
      } else {
        jQuery(this).hide(); // baad mein select karke show karenge
      }
    });

    var sideRange = 1; // current ke aas paas 1-1 page

    // 1, last, current, current-1, current+1 show karo
    $numItems.each(function () {
      var n = parseInt(jQuery(this).text(), 10);
      if (isNaN(n) || n > totalPages) return;

      if (
        n === 1 ||
        n === totalPages ||
        n === current ||
        n === current - sideRange ||
        n === current + sideRange
      ) {
        jQuery(this).show();
      }
    });

    // 1st page li aur last page li find karo
    var $page1 = $numItems.filter(function () {
      return parseInt(jQuery(this).text(), 10) === 1;
    });
    var $lastPage = $numItems.filter(function () {
      return parseInt(jQuery(this).text(), 10) === totalPages;
    });

    if ($page1.length) $page1.show();
    if ($lastPage.length) $lastPage.show();

    // 1 ke baad dots (agar gap ho)
    if ($page1.length && $page1.is(":visible")) {
      var $after1 = $page1.nextAll("li.page-item")
        .not(".prev,.next")
        .filter(":visible")
        .first();

      if ($after1.length) {
        var nAfter = parseInt($after1.text(), 10);
        if (!isNaN(nAfter) && nAfter > 2) {
          jQuery(
            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
          ).insertAfter($page1);
        }
      }
    }

    // last se pehle dots (agar gap ho)
    if ($lastPage.length && $lastPage.is(":visible")) {
      var $beforeLast = $lastPage.prevAll("li.page-item")
        .not(".prev,.next")
        .filter(":visible")
        .first();

      if ($beforeLast.length) {
        var nBefore = parseInt($beforeLast.text(), 10);
        if (!isNaN(nBefore) && nBefore < totalPages - 1) {
          jQuery(
            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
          ).insertBefore($lastPage);
        }
      }
    }
  }

  jQuery(".cuim-cancel-button-ip").on("click", function () {
    var check_ipv4 = $(this)
      .closest(".edit-ip-from-list")
      .find(".ip-edit-ip4-check")
      .val(); // Assuming the ID for the IPv4 field is 'ipv4'
    var check_ipv6 = $(this)
      .closest(".edit-ip-from-list")
      .find(".ip-edit-ip6-check")
      .val(); // Assuming the ID for the IPv6 field is 'ipv6'
    jQuery(this)
      .closest(".edit-ip-from-list")
      .find(".manage-ip-ipv4-field")
      .val(check_ipv4);
    jQuery(this)
      .closest(".edit-ip-from-list")
      .find(".manage-ip-ipv6-field")
      .val(check_ipv6);
    jQuery(".ip-error").text('');
    jQuery('.cuim-edit-button-ip').prop('disabled', false);
  });


  /**
   * Cross Icon JS clear data functionality
   */

  jQuery(".manage-ip-cross-icon").on("click", function () {
    // Get the values from the hidden input fields directly using .find()
    var check_ipv4 = $(this)
      .closest(".edit-manage-ip-form-inner") // Look for the parent container
      .find(".ip-edit-ip4-check") // Find the hidden IPv4 field inside the container
      .val(); // Get the value of the hidden IPv4 field

    var check_ipv6 = $(this)
      .closest(".edit-manage-ip-form-inner") // Look for the parent container
      .find(".ip-edit-ip6-check") // Find the hidden IPv6 field inside the container
      .val(); // Get the value of the hidden IPv6 field

    // Set the IPv4 and IPv6 input fields' values to the ones retrieved from hidden fields
    $(this)
      .closest(".edit-manage-ip-form-inner") // Ensure we're targeting the right form container
      .find(".manage-ip-ipv4-field")
      .val(check_ipv4);

    $(this)
      .closest(".edit-manage-ip-form-inner") // Ensure we're targeting the right form container
      .find(".manage-ip-ipv6-field")
      .val(check_ipv6);

    // Clear the error messages
    jQuery(".ip-error").text('');
  });


  jQuery(".cuim-cancel-btn-ip-add, .manage-ip-cross-icon").on("click", function (e) {
    e.preventDefault();
    jQuery("#add-ip-from").find("input").val("");
    jQuery("#add-ip-from input").removeClass('error-field');
    jQuery("#add-ip-from .error-message").text('');
    jQuery(".cancel-form-confirmation").removeClass("active");
    jQuery(".add-manage-ip-form").removeClass("active");
    if (jQuery(".cuim-main-add-ip-account").next(".error-message").length === 0) {
      jQuery(".cuim-main-add-ip-account").after('<div class="error-message account-error"></div>');
    }
    if (jQuery(".manage-ip-ipv4-field").next(".error-message").length === 0) {
      jQuery(".manage-ip-ipv4-field").after('<div class="error-message ip-error ipv4-error"></div>');
    }
    if (jQuery(".manage-ip-ipv6-field").next(".error-message").length === 0) {
      jQuery(".manage-ip-ipv6-field").after('<div class="error-message ip-error ipv6-error"></div>');
    }
    jQuery('#add-ip-btn').prop('disabled', false);

  });

  /**
   * user login form script section
   */
  jQuery("#cuim-user-login-form").on("submit", function (e) {
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serialize();

    var nonce = cuim_ajax.nonce; // Nonce for security
    jQuery(".cuim-user-login-error").html("");
    jQuery("input#user-login-submit").prop('disabled', true);
    // Send the AJAX request
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "cuim_login_check",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        if (response.success) {
          // If successful, show a success message

          window.location = response.data.redirect;
          $form.append($successMsg);

          // alert('test');
        } else {
          if (
            response.data.code == "Please check your username and password."
          ) {
            jQuery("#user-login-flow-password").after(
              "<div class='error-message cuim-user-login-error'>Login failed.<br>" + response.data.code + "</div>"
            );
            jQuery("input#user-login-submit").prop('disabled', false);

          } else {
            const $successMsg = $(
              `<div class="submitted-unsuccessfully">${response.data.code}</div>`
            );
            $form.append($successMsg);
            jQuery("input#user-login-submit").prop('disabled', false);
            // Hide after 3 seconds
            setTimeout(function () {
              $successMsg.fadeOut(400, function () {
                $(this).remove();
              });
            }, 3000);
          }
        }
      },
      error: function (response) {
        // Error message if AJAX fails
        alert("An error occurred.");
      },
    });
  });

  /**
   * forget password by user script
   */
  jQuery("#cuim-user-forget-form").on("submit", function (e) {
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serialize();

    var nonce = cuim_ajax.nonce; // Nonce for security

    // Send the AJAX request
    $.ajax({
      url: cuim_ajax.ajax_url,
      type: "POST",
      data: {
        action: "handle_forget_user_password",
        form_data: formData, // Pass the form data to the server
        nonce: nonce,
      },
      success: function (response) {
        // alert(response);
        // Check if the response contains success
        if (response.success) {
          // If successful, show a success message

          window.location = response.data.redirect;
          $form.append($successMsg);
        } else {
          if (
            response.data.code == "Please check your username and password."
          ) {
            jQuery(".cuim-user-login-error").html(
              "Login failed." + "<br>" + response.data.message
            );
          } else {
            const $successMsg = $(
              `<div class="submitted-unsuccessfully">${response.data.message}</div>`
            );
            $form.append($successMsg);
            // Hide after 3 seconds
            setTimeout(function () {
              $successMsg.fadeOut(400, function () {
                $(this).remove();
              });
            }, 3000);
          }
        }
      },
      error: function (response) {
        // Error message if AJAX fails
        alert("An error occurred.");
      },
    });
  });
  jQuery(".cuim-user-login-flow-validation-254").on("input", function () {
    var maxLength = 254; // Maximum length for email
    var $input = $(this);
    var $errorMessage = $input
      .closest(".user-login-flow-form-field")
      .next(".error-message");

    // Check if the input length exceeds the max length
    if ($input.val().length > maxLength) {
      // Truncate the value and display an error message
      $input.val($input.val().substring(0, maxLength));
      $errorMessage
        .text(
          window.location.pathname.startsWith("/zh")
            ? "電子郵箱地址最多只能輸入 254 個字符，請縮短您的輸入。"
            : "Maximum length for email address is 254 characters. Please shorten your input."
        )
        .show();
    } else {
      // Hide the error message if length is within the limit
      $errorMessage.text("").hide();
    }
  });
  $(".cuim-user-login-flow-validation-254").on("keypress", function (e) {
    if (e.which === 32) {
      e.preventDefault(); // Prevent spacebar
    }
  });



  /**
   * login records search filter
   */


  $("#agqa-login-records-filters").on("click", function (event) {
    event.preventDefault(); // Prevent form submission
    // alert('fgfgfgf');
    // var reportType = $("input.agqa-filter-select-hidden").val().toLowerCase();
    var loginRecordText = $("input#login-records-search").val().toLowerCase();
    // alert(IPaccountText);

    var dateRange = $("#daterange").val(); // Get selected date range from inputa

    // If date range is selected, parse the start and end dates as strings
    var dateArray = dateRange.split(" - ");
    var startDate = dateArray[0] || ""; // Start date string in "YYYY/MM/DD" format
    var endDate = dateArray[1] || ""; // End date string in "YYYY/MM/DD" format
    var resultsFound = false; // Flag to track if any result is found


    if (!loginRecordText && !dateRange) {
      $(".section-found").hide();
      $(".custom-table-ctn").show();
      $(".custom-table-row").show();
      $("#pagination-demo").show();

      setTimeout(function () {
        // // Recalculate pagination based on the filtered visible items
        var itemsPerPages = 15;
        var totalItemss = $(".custom-table-row").length; // Count only visible items after filtering
        var totalPages = Math.ceil(totalItemss / itemsPerPages);
        $(".custom-table-row").removeAttr("data-page"); // Remove the data-page attribute
        // Reinitialize pagination
        $(".custom-table-row").each(function (index) {
          var pageNumber = Math.floor(index / itemsPerPages) + 1;
          // var pageNumber = "sajid";
          jQuery(this).attr("data-page", pageNumber);
          jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
            .addClass("active")
            .siblings()
            .removeClass("active");

          jQuery(".custom-table-row").hide();
          jQuery('.custom-table-row[data-page="' + "1" + '"]').show();
        });
        jQuery(".pagination-ctn ul li.page-item").show();
        jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
        jQuery(".pagination-ctn ul li.prev").addClass("disabled"); // Enable Next button
        applyCustomDots(totalPages);
      }, 500); // Delay of 500 milliseconds
      return; // Return early if either is emptyEn
    }
    jQuery('.custom-table-row').removeClass('active');
    // Initially hide pagination and "Nothing Found" message
    $(".section-found").hide();
    $(".custom-table-ctn").show();
    $("div#pagination-demo").hide();

    $(".custom-table-row").each(function () {
      var IPaccountsearchText = $(this)
        .find(".login-record-account")
        .text()
        .toLowerCase();

      // var rowDateText = $(this).find(".table-body-col-date").text().trim(); // Get the date from the row (e.g., "2025/09/17")
      var rowDateText = $(this).find(".table-body-col-date").text().trim(); // "2025/10/20 19:50"

      // Option 1: split (handles multiple spaces too)
      var dateOnly = rowDateText.split(/\s+/)[0]; // "2025/10/20"
      var formatted = `${rowDateText}`;           // "(2025/10/20 )"
      var isIPAccountMatch = IPaccountsearchText.includes(loginRecordText);
      var isDateMatch = true;
      if (startDate && endDate) {
        // Check if the row's date is within the range
        isDateMatch = formatted >= startDate && formatted <= endDate;
      } else if (startDate) {
        isDateMatch = formatted >= startDate;
      } else if (endDate) {
        isDateMatch = formatted <= endDate;
      }

      if (isIPAccountMatch && isDateMatch) {
        $(this).show();
        resultsFound = true;
      } else {
        $(this).hide();
      }
    });

    // If no results are found, show the 'nothing found' message
    if (!resultsFound) {
      $(".section-found").show();
      $(".custom-table-ctn").hide();
      $("div#pagination-demo").hide();
    } else {
      $("div#pagination-demo").show();
      $(".section-found").hide();
      $(".custom-table-ctn").show();
    }

    setTimeout(function () {
      // Recalculate pagination based on the filtered visible items
      var itemsPerPages = 15;
      var totalItemss = $(".custom-table-row:visible").length; // Count only visible items after filtering
      var totalPages = Math.ceil(totalItemss / itemsPerPages);

      $(".custom-table-row").removeAttr("data-page"); // Remove the data-page attribute
      // Reinitialize pagination
      $(".custom-table-row:visible").each(function (index) {
        var pageNumber = Math.floor(index / itemsPerPages) + 1;
        // var pageNumber = "sajid";
        jQuery(this).attr("data-page", pageNumber);
        jQuery(this).addClass("active");
        jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
          .addClass("active")
          .siblings()
          .removeClass("active");
        if (pageNumber === 1) {
          $(this).show(); // Show items that belong to the current page
        } else {
          $(this).hide(); // Hide items that do not belong to the current page
        }
      });
      jQuery(".pagination-ctn ul li.page-item").show();
      jQuery(".pagination-ctn ul li.page-item")
        .not(".prev, .next")
        .each(function () {
          var pageNumbers = parseInt(jQuery(this).text()); // Get the number of the page
          if (pageNumbers === totalPages && totalPages !== 0) {
            // Remove all <li> items that come after this one
            jQuery(this).nextAll().not(".next").hide();

            // Check the <li> just before the Next button
            var prevLi = jQuery(
              ".pagination-ctn ul li.page-item.active"
            ).next();
            jQuery(".pagination-ctn ul li.prev").addClass("disabled"); // Disable Next button
            // If the next page is hidden or .next button is visible, disable the next button
            if (prevLi.is(":hidden")) {
              jQuery(".pagination-ctn ul li.next").addClass("disabled"); // Disable Next button
            } else {
              jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
            }
          }
        });
      applyCustomDotsLoginRecords(totalPages);
    }, 500); // Delay of 500 milliseconds
  });
  function applyCustomDotsLoginRecords(totalPages) {
    var $pager = jQuery(".pagination-ctn ul");

    // Agar 1 hi page hai to dots ka koi faida nahi
    if (!totalPages || totalPages <= 1) {
      $pager.find("li.page-item.cust-ellipsis").remove();
      return;
    }

    // Purane wale custom dots hata do
    $pager.find("li.page-item.cust-ellipsis").remove();

    // Sirf number wali li (prev / next ko hata ke)
    var $numItems = $pager.find("li.page-item").not(".prev, .next");

    // Current active page nikaalo (jo tum nth-child(3) se active kar rahe ho)
    var current = parseInt($pager.find("li.page-item.active").text(), 10);
    if (isNaN(current) || current < 1) current = 1;
    if (current > totalPages) current = totalPages;

    // Pehle sab numeric pages ko base state mein hide karo / > totalPages hide
    $numItems.each(function () {
      var n = parseInt(jQuery(this).text(), 10);
      if (isNaN(n)) return;

      if (n > totalPages) {
        jQuery(this).hide();
      } else {
        jQuery(this).hide(); // baad mein select karke show karenge
      }
    });

    var sideRange = 1; // current ke aas paas 1-1 page

    // 1, last, current, current-1, current+1 show karo
    $numItems.each(function () {
      var n = parseInt(jQuery(this).text(), 10);
      if (isNaN(n) || n > totalPages) return;

      if (
        n === 1 ||
        n === totalPages ||
        n === current ||
        n === current - sideRange ||
        n === current + sideRange
      ) {
        jQuery(this).show();
      }
    });

    // 1st page li aur last page li find karo
    var $page1 = $numItems.filter(function () {
      return parseInt(jQuery(this).text(), 10) === 1;
    });
    var $lastPage = $numItems.filter(function () {
      return parseInt(jQuery(this).text(), 10) === totalPages;
    });

    if ($page1.length) $page1.show();
    if ($lastPage.length) $lastPage.show();

    // 1 ke baad dots (agar gap ho)
    if ($page1.length && $page1.is(":visible")) {
      var $after1 = $page1.nextAll("li.page-item")
        .not(".prev,.next")
        .filter(":visible")
        .first();

      if ($after1.length) {
        var nAfter = parseInt($after1.text(), 10);
        if (!isNaN(nAfter) && nAfter > 2) {
          jQuery(
            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
          ).insertAfter($page1);
        }
      }
    }

    // last se pehle dots (agar gap ho)
    if ($lastPage.length && $lastPage.is(":visible")) {
      var $beforeLast = $lastPage.prevAll("li.page-item")
        .not(".prev,.next")
        .filter(":visible")
        .first();

      if ($beforeLast.length) {
        var nBefore = parseInt($beforeLast.text(), 10);
        if (!isNaN(nBefore) && nBefore < totalPages - 1) {
          jQuery(
            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
          ).insertBefore($lastPage);
        }
      }
    }
  }


  /**
   * Approval Page Filter
   */

  $("#agqa-approval-page-filter").on("click", function (e) {
    e.preventDefault(); // Prevent form submission
    // alert('fgfgfgfgffffffffff');
    // return;
    var reportType = $("input.agqa-filter-select-hidden").val().toLowerCase();
    // alert(reportType);
    var statusApproval = $(".agqa-filter-select-hidden.cuim-status").val().toLowerCase();
    // var loginRecordText = $("input#login-records-search").val().toLowerCase();
    //     alert(statusApproval);
    // return;
    var resultsFound = false; // Flag to track if any result is found
    jQuery('.custom-table-row.active').removeClass('active');

    if (!reportType && !statusApproval) {
      $(".section-found").hide();
      $(".custom-table-ctn").show();
      $(".custom-table-row").show();
      $("#pagination-demo").show();

      setTimeout(function () {
        // Recalculate pagination based on the filtered visible items
        var itemsPerPages = 15;
        var totalItemss = $(".custom-table-row").length; // Count only visible items after filtering
        var totalPages = Math.ceil(totalItemss / itemsPerPages);
        $(".custom-table-row").removeAttr("data-page"); // Remove the data-page attribute
        // Reinitialize pagination
        $(".custom-table-row").each(function (index) {
          var pageNumber = Math.floor(index / itemsPerPages) + 1;
          // var pageNumber = "sajid";
          jQuery(this).attr("data-page", pageNumber);
          jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
            .addClass("active")
            .siblings()
            .removeClass("active");

          jQuery(".custom-table-row").hide();
          jQuery('.custom-table-row[data-page="' + "1" + '"]').show();
        });
        jQuery(".pagination-ctn ul li.page-item").show();
        jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
      }, 500); // Delay of 500 milliseconds
      return; // Return early if either is empty
    }

    // Initially hide pagination and "Nothing Found" message
    $(".section-found").hide();
    $(".custom-table-ctn").show();
    $("div#pagination-demo").hide();

    $(".custom-table-row").each(function () {
      var IPaccountsearchText = $(this)
        .find(".cuim-type-name-approval")
        .data('approval-value')
        .toLowerCase();
      var matchStatus = $(this)
        .find(".table-row-status span")
        .data('approval-status-value')
        .toLowerCase();
      // alert(IPaccountsearchText);

      var isIPAccountMatch = reportType === "all" ||
        reportType === "" || IPaccountsearchText.trim() === reportType;
      var isMatchStatus = statusApproval === "all" ||
        statusApproval === "" || matchStatus.trim() === statusApproval;


      if (isIPAccountMatch && isMatchStatus) {
        $(this).show();
        resultsFound = true;
      } else {
        $(this).hide();
      }
    });

    // If no results are found, show the 'nothing found' message
    if (!resultsFound) {
      $(".section-found").show();
      $(".custom-table-ctn").hide();
      $("div#pagination-demo").hide();
    } else {
      $("div#pagination-demo").show();
      $(".section-found").hide();
      $(".custom-table-ctn").show();
    }

    setTimeout(function () {
      // Recalculate pagination based on the filtered visible items
      var itemsPerPages = 15;
      var totalItemss = $(".custom-table-row:visible").length; // Count only visible items after filtering
      var totalPages = Math.ceil(totalItemss / itemsPerPages);


      $(".custom-table-row").removeAttr("data-page"); // Remove the data-page attribute
      // Reinitialize pagination
      $(".custom-table-row:visible").each(function (index) {
        var pageNumber = Math.floor(index / itemsPerPages) + 1;
        // var pageNumber = "sajid";
        jQuery(this).attr("data-page", pageNumber);
        jQuery(this).addClass("active");
        jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
          .addClass("active")
          .siblings()
          .removeClass("active");
        if (pageNumber === 1) {
          $(this).show(); // Show items that belong to the current page
        } else {
          $(this).hide(); // Hide items that do not belong to the current page
        }
      });
      jQuery(".pagination-ctn ul li.page-item").show();
      jQuery(".pagination-ctn ul li.page-item")
        .not(".prev, .next")
        .each(function () {
          var pageNumbers = parseInt(jQuery(this).text()); // Get the number of the page
          if (pageNumbers === totalPages && totalPages !== 0) {
            // Remove all <li> items that come after this one
            jQuery(this).nextAll().not(".next").hide();

            // Check the <li> just before the Next button
            var prevLi = jQuery(
              ".pagination-ctn ul li.page-item.active"
            ).next();
            jQuery(".pagination-ctn ul li.prev").addClass("disabled");
            // If the next page is hidden or .next button is visible, disable the next button
            if (prevLi.is(":hidden")) {
              jQuery(".pagination-ctn ul li.next").addClass("disabled"); // Disable Next button
            } else {
              jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
            }
          }
        });
      // 🔹 FILTER KE BAAD BHI DOTS / 1 / LAST APPLY KARO (NEW)
      applyCustomDots(totalPages);

    }, 500); // Delay of 500 milliseconds
  });

  // 🔧 NEW HELPER: dots + 1 + last logic
  function applyCustomDots(totalPages) {
    var $pager = jQuery(".pagination-ctn ul");

    // Agar 1 hi page hai to dots ka koi faida nahi
    if (!totalPages || totalPages <= 1) {
      $pager.find("li.page-item.cust-ellipsis").remove();
      return;
    }

    // Purane wale custom dots hata do
    $pager.find("li.page-item.cust-ellipsis").remove();

    // Sirf number wali li (prev / next ko hata ke)
    var $numItems = $pager.find("li.page-item").not(".prev, .next");

    // Current active page nikaalo (jo tum nth-child(3) se active kar rahe ho)
    var current = parseInt($pager.find("li.page-item.active").text(), 10);
    if (isNaN(current) || current < 1) current = 1;
    if (current > totalPages) current = totalPages;

    // Pehle sab numeric pages ko base state mein hide karo / > totalPages hide
    $numItems.each(function () {
      var n = parseInt(jQuery(this).text(), 10);
      if (isNaN(n)) return;

      if (n > totalPages) {
        jQuery(this).hide();
      } else {
        jQuery(this).hide(); // baad mein select karke show karenge
      }
    });

    var sideRange = 1; // current ke aas paas 1-1 page

    // 1, last, current, current-1, current+1 show karo
    $numItems.each(function () {
      var n = parseInt(jQuery(this).text(), 10);
      if (isNaN(n) || n > totalPages) return;

      if (
        n === 1 ||
        n === totalPages ||
        n === current ||
        n === current - sideRange ||
        n === current + sideRange
      ) {
        jQuery(this).show();
      }
    });

    // 1st page li aur last page li find karo
    var $page1 = $numItems.filter(function () {
      return parseInt(jQuery(this).text(), 10) === 1;
    });
    var $lastPage = $numItems.filter(function () {
      return parseInt(jQuery(this).text(), 10) === totalPages;
    });

    if ($page1.length) $page1.show();
    if ($lastPage.length) $lastPage.show();

    // 1 ke baad dots (agar gap ho)
    if ($page1.length && $page1.is(":visible")) {
      var $after1 = $page1.nextAll("li.page-item")
        .not(".prev,.next")
        .filter(":visible")
        .first();

      if ($after1.length) {
        var nAfter = parseInt($after1.text(), 10);
        if (!isNaN(nAfter) && nAfter > 2) {
          jQuery(
            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
          ).insertAfter($page1);
        }
      }
    }

    // last se pehle dots (agar gap ho)
    if ($lastPage.length && $lastPage.is(":visible")) {
      var $beforeLast = $lastPage.prevAll("li.page-item")
        .not(".prev,.next")
        .filter(":visible")
        .first();

      if ($beforeLast.length) {
        var nBefore = parseInt($beforeLast.text(), 10);
        if (!isNaN(nBefore) && nBefore < totalPages - 1) {
          jQuery(
            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
          ).insertBefore($lastPage);
        }
      }
    }
  }


  /**
   * ip cancel button popup
   */
  $(".cuim-ip-cancel-btn").on("click", function (e) {
    e.preventDefault(); // Prevents the default action (if it's inside a form)

    // Show the confirmation box
    jQuery(this).next(".cancel-form-confirmation").addClass("active");

  });

});
