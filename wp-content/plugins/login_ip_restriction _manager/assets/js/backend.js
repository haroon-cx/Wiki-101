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

  /**
   * user manage filter
   **/
  $(".manage-user-template #agqa-user-filters").on("click", function (event) {
    event.preventDefault(); // Prevent form submission

    var searchTerm = $("#manage-user-search").val().toLowerCase(); // Get search term
    var selectedStat = $("input#filter-select-states").val().toLowerCase(); // Get selected state
    var selectedRole = $("input#filter-select-roles").val().toLowerCase(); // Get selected role
    var selectedCompany = $("input#filter-select-companies").val().toLowerCase(); // Get selected company
    var dateRange = $("#daterange").val(); // Get selected date range from inputa

    // If date range is selected, parse the start and end dates as strings
    var dateArray = dateRange.split(" - ");
    var startDate = dateArray[0] || ""; // Start date string in "YYYY/MM/DD" format
    var endDate = dateArray[1] || ""; // End date string in "YYYY/MM/DD" format

    // alert(selectedStat + " " + selectedRole + " " + selectedCompany + " " + dateRange);
    // alert(endDate);
    jQuery('.custom-table-row').removeClass("active");
    var resultsFound = false; // Flag to track if any result is found

    if (!searchTerm && !selectedStat && !selectedRole && !selectedCompany && !dateRange) {
      $(".section-found").hide(); // Hide the 'nothing found' message
      $('.custom-table-row').show(); // Show the FAQ item
      $('#pagination-demo').show(); // Show the FAQ item

      setTimeout(function() {
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
          jQuery(".pagination-ctn ul li.page-item:nth-child(3)").addClass('active').siblings().removeClass('active');
          jQuery(".custom-table-row").hide();
          jQuery('.custom-table-row[data-page="' + '1' + '"]').show();
        });
        jQuery('.pagination-ctn ul li.page-item').show();
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

      }, 500); // Delay of 500 milliseconds
      return; // Return early if either is empty
    }


    // Initially hide pagination and "Nothing Found" message
    $(".section-found").hide(); // Hide "Nothing Found" message
    $("div#pagination-demo").hide(); // Hide pagination

    $(".custom-table-row").each(function () {
      var rowText = $(this).find(".table-body-col-text").text().toLowerCase(); // Get all text inside the row
      var rowCategory = $(this).find(".table-row-status").text().toLowerCase(); // Get the state of the row
      var rowRole = $(this).find(".table-row-user-role").text().toLowerCase(); // Get the role of the row
      var rowCompany = $(this).find(".table-row-company").text().toLowerCase(); // Get the company of the row
      var rowDateText = $(this).find(".table-body-col-date").text().trim(); // Get the date from the row (e.g., "2025/09/17")

      // Apply filters based on exact match for state, role, company, and search term
      var isStateMatch = (selectedStat === "" || rowCategory.trim() === selectedStat); // Exact match for state
      var isRoleMatch = (selectedRole === "" || rowRole === selectedRole); // Exact match for role
      var isCompanyMatch = (selectedCompany === "" || rowCompany === selectedCompany); // Exact match for company
      var isSearchMatch = rowText.includes(searchTerm); // Check if the search term is found anywhere in the row content

      // alert(rowDateText);


      // Ensure that the row date matches the selected date range
      var isDateMatch = true; // Default to true (if no date range is selected)
      if (startDate && endDate) {
        // Check if the row's date is within the range
        isDateMatch = (rowDateText >= startDate && rowDateText <= endDate); // Lexicographical comparison works for "YYYY/MM/DD"
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
      $("div#pagination-demo").hide(); // Hide pagination
    } else {
      $("div#pagination-demo").show(); // Show pagination
      $(".section-found").hide(); // Hide the 'nothing found' message
    }

    setTimeout(function() {
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
        jQuery(".pagination-ctn ul li.page-item:nth-child(3)").addClass('active').siblings().removeClass('active');
        if (pageNumber === 1) {
          $(this).show(); // Show items that belong to the current page
        } else {
          $(this).hide(); // Hide items that do not belong to the current page
        }
      });
      jQuery('.pagination-ctn ul li.page-item').show();
      jQuery(".pagination-ctn ul li.page-item").not(".prev, .next").each(function () {
        var pageNumbers = parseInt(jQuery(this).text()); // Get the number of the page
        if (pageNumbers === totalPages && totalPages !== 0) {

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


        }
      });

    }, 100); // Delay of 500 milliseconds

  });
});
