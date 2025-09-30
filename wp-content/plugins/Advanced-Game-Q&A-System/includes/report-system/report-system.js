/**
 * FAQ Filter
 */
jQuery(document).ready(function ($) {
  $("#agqa-report-system-filter").on("click", function (event) {
    event.preventDefault(); // Prevent form submission
    alert("dfdf");
    var searchTerm = $("#report-filter-search").val().toLowerCase(); // Get search term
    var selectedCategory = $("input.agqa-filter-select-hidden")
      .val()
      .toLowerCase(); // Get selected category
    var resultsFound = false; // Flag to track if any result is found

    // Check if either is empty
    $(".agqa-report-cat-filter").removeClass("faq-cat-active");
    jQuery(".faq-accordion").removeClass("active");

    // highlighted
    var query = searchTerm;
    jQuery(".faq-main-content")
      .find(".highlighted")
      .each(function () {
        var $highlightedNode = jQuery(this);
        $highlightedNode.replaceWith($highlightedNode.text());
      });

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
    }

    if (!searchTerm && !selectedCategory) {
      $(".section-found").hide(); // Hide the 'nothing found' message
      $(".faq-accordion").show(); // Show the FAQ item
      $("#pagination-demo").show(); // Show the FAQ item

      $(document).find(".faq-accordion-head").removeClass("active"); // Add active class to the head
      $(document).find(".faq-accordion-body").slideUp(); // Slide down the body

      // Recalculate pagination based on the filtered visible items
      var itemsPerPages = 15;
      var totalItemss = $(".faq-accordion").length; // Count only visible items after filtering
      var totalPages = Math.ceil(totalItemss / itemsPerPages);
      $(".faq-accordion").removeAttr("data-page"); // Remove the data-page attribute
      // Reinitialize pagination
      $(".faq-accordion").each(function (index) {
        var pageNumber = Math.floor(index / itemsPerPages) + 1;
        // var pageNumber = "sajid";
        jQuery(this).attr("data-page", pageNumber);
        jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
          .addClass("active")
          .siblings()
          .removeClass("active");
        jQuery(".faq-accordion").hide();
        jQuery('.faq-accordion[data-page="' + "1" + '"]').show();
      });
      jQuery(".pagination-ctn ul li.page-item").show();
      jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
      return; // Return early if either is empty
    }

    // Initially hide pagination and "Nothing Found" message
    $(".section-found").hide(); // Hide "Nothing Found" message
    $("div#pagination-demo").hide(); // Hide pagination
    var currentPage = $(".pagination .active").text(); // Get the current page number

    $(".faq-accordion").each(function () {
      var faqText = $(this).text().toLowerCase(); // Get all text inside the FAQ accordion
      var faqCategory = $(this)
        .find(".faq-accodion-status")
        .text()
        .toLowerCase(); // Optionally, get category text
      if (
        (selectedCategory === "all" ||
          faqCategory.includes(selectedCategory)) &&
        faqText.includes(searchTerm) // Check if the search term is found anywhere in the FAQ content
      ) {
        $(this).show(); // Show the FAQ item
        if (!searchTerm) {
          $(this).find(".faq-accordion-head").removeClass("active"); // Remove active class from the head
          $(this).find(".faq-accordion-body").slideUp(); // Slide up the body
        } else {
          $(this).find(".faq-accordion-head").addClass("active"); // Add active class to the head
          $(this).find(".faq-accordion-body").slideDown(); // Slide down the body
        }
        resultsFound = true; // Mark that at least one result is found
      } else if (
        // If no category filter is applied and only search term matches anywhere in the FAQ
        !selectedCategory &&
        faqText.includes(searchTerm)
      ) {
        $(this).show(); // Show the FAQ item
        if (!searchTerm) {
          $(this).find(".faq-accordion-head").removeClass("active"); // Remove active class from the head
          $(this).find(".faq-accordion-body").slideUp(); // Slide up the body
        } else {
          $(this).find(".faq-accordion-head").addClass("active"); // Add active class to the head
          $(this).find(".faq-accordion-body").slideDown(); // Slide down the body
        }
        resultsFound = true; // Mark that at least one result is found
      } else {
        $(this).hide(); // Hide the FAQ item
        $(this).find(".faq-accordion-head").removeClass("active"); // Remove active class from the head
        $(this).find(".faq-accordion-body").slideUp(); // Slide up the body
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

    setTimeout(function () {
      // Recalculate pagination based on the filtered visible items
      var itemsPerPages = 15;
      var totalItemss = $(".faq-accordion:visible").length; // Count only visible items after filtering
      var totalPages = Math.ceil(totalItemss / itemsPerPages);

      $(".faq-accordion").removeAttr("data-page"); // Remove the data-page attribute
      // Reinitialize pagination
      $(".faq-accordion:visible").each(function (index) {
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
    }, 500);
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
   *
   */
  jQuery(".agqa-report-system-form").submit("submit", function (e) {
    e.preventDefault();

    const $form = $(this);
    var formData = $form.serialize();
    alert(formData);
    return;

    // AJAX
    var nonce = agqa_ajax.nonce;
    $.ajax({
      type: "POST",
      url: agqa_ajax.ajax_url,
      data: {
        action: "faq_report_system",
        form_data: formData,
        nonce: nonce,
      },
      success: function (response) {
        console.log(response);
        if (response.includes("Success")) {
          // alert("Successfully Submitted");
          const $successMsg = $(
            '<div class="submitted-successfully">Report Successfully Submitted</div>'
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
        console.error("AJAX Error:", error); // Log the error for debugging
        alert("An error occurred! Please try again later.");
      },
    });
  });
});
