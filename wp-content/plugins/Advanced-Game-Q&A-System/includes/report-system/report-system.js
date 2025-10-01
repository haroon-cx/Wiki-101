/**
 * Report System Filter
 */
jQuery(document).ready(function ($) {
  $("#agqa-report-system-filter").on("click", function (event) {
    event.preventDefault(); // Prevent form submission

    // var reportType = $("input.agqa-filter-select-hidden").val().toLowerCase();
    // var reportSearch = $("#report-filter-search").val().toLowerCase();
    var reportFilterStatus = $(".agqa-status-filter").val().toLowerCase();

    alert(reportFilterStatus);
    // jQuery(".custom-table-row").removeClass("active");
    // var resultsFound = false; // Flag to track if any result is found

    // if (!reportType && !reportSearch && !reportFilterStatus) {
    //   $(".section-found").hide();
    //   $(".custom-table-ctn").show();
    //   $(".custom-table-row").show();
    //   $("#pagination-demo").show();

    //   setTimeout(function () {
    //     // Recalculate pagination based on the filtered visible items
    //     var itemsPerPages = 15;
    //     var totalItemss = $(".custom-table-row").length; // Count only visible items after filtering
    //     var totalPages = Math.ceil(totalItemss / itemsPerPages);
    //     $(".custom-table-row").removeAttr("data-page"); // Remove the data-page attribute
    //     // Reinitialize pagination
    //     $(".custom-table-row").each(function (index) {
    //       var pageNumber = Math.floor(index / itemsPerPages) + 1;
    //       // var pageNumber = "sajid";
    //       jQuery(this).attr("data-page", pageNumber);
    //       jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
    //         .addClass("active")
    //         .siblings()
    //         .removeClass("active");

    //       jQuery(".custom-table-row").hide();
    //       jQuery('.custom-table-row[data-page="' + "1" + '"]').show();
    //     });
    //     jQuery(".pagination-ctn ul li.page-item").show();
    //     jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
    //   }, 500); // Delay of 500 milliseconds
    //   return; // Return early if either is empty
    // }

    // Initially hide pagination and "Nothing Found" message
    $(".section-found").hide();
    $(".custom-table-ctn").show();
    $("div#pagination-demo").hide();

    $(".custom-table-row").each(function () {
      var reportStatusText = $(this)
        .find(".table-body-col.report-status-response span")
        .text()
        .toLowerCase();
      // alert(reportStatusText);
      // var rowCategory = $(this).find(".table-row-status").text().toLowerCase();
      // var rowRole = $(this).find(".table-row-user-role").text().toLowerCase();
      // var rowCompany = $(this).find(".table-row-company").text().toLowerCase();
      // var rowDateText = $(this).find(".table-body-col-date").text().trim();

      // Apply filters based on exact match for state, role, company, and search term
      var isStateMatch =
        reportFilterStatus === "" ||
        reportStatusText.trim() === reportFilterStatus;
      // var isRoleMatch = selectedRole === "" || rowRole === selectedRole;
      // var isCompanyMatch =
      //   selectedCompany === "" || rowCompany === selectedCompany;
      // var isSearchMatch = reportStatusText.includes(reportFilterStatus);

      // alert(rowDateText);

      // Ensure that the row date matches the selected date range
      // var isDateMatch = true; // Default to true (if no date range is selected)
      // if (startDate && endDate) {
      //   // Check if the row's date is within the range
      //   isDateMatch = rowDateText >= startDate && rowDateText <= endDate; // Lexicographical comparison works for "YYYY/MM/DD"
      // } else if (startDate) {
      //   isDateMatch = rowDateText >= startDate; // If only start date is selected, check if the row's date is after start date
      // } else if (endDate) {
      //   isDateMatch = rowDateText <= endDate; // If only end date is selected, check if the row's date is before end date
      // }
      if (isStateMatch) {
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
        });
    }, 100); // Delay of 500 milliseconds
  });
  /**
   * Fetch answer script
   */
  $(".faq-item").on("click", function () {
    var faqId = $(this).data("faq-id");
    var nonce = agqa_ajax.nonce;

    $.ajax({
      type: "POST",
      url: agqa_ajax.ajax_url,
      data: {
        action: "fetch_faq_answer",
        faq_id: faqId,
        nonce: nonce,
      },
      success: function (response) {
        // console.log(response); // Check the full response
        if (response.success) {
          // console.log(response.data.answer); // Ensure the answer is present
          $(".respond-detail-textarea").text(response.data.answer); // Set the value
        } else {
          alert("Answer not found.");
        }
      },
      error: function () {
        alert("There was an error fetching the answer.");
      },
    });
  });

  /**
   * report-system-form
   */
  jQuery(".agqa-report-system-form").submit("submit", function (e) {
    e.preventDefault();

    const $form = $(this);
    var formData = $form.serialize();
    // alert(formData);
    // return;

    var nonce = agqa_ajax.nonce;
    $.ajax({
      type: "POST",
      url: agqa_ajax.ajax_url,
      data: {
        action: "agqa_report_reply_system",
        form_data: formData,
        nonce: nonce,
      },
      success: function (response) {
        // console.log(response);
        if (response.includes("Success")) {
          jQuery("div#confirm-submit-popup").removeClass("active");
          jQuery(".respond-popup").removeClass("active");
          // alert("Successfully Submitted");
          const $successMsg = $(
            '<div class="submitted-successfully">Responed Done</div>'
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
            `<div class="report submitted-unsuccessfully">${response}</div>`
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
        console.error("AJAX Error:", error);
        alert("An error occurred! Please try again later.");
      },
    });
  });
});
