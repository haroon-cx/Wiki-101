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

  $("#agqa-faq-filter").on("click", function (event) {
    event.preventDefault(); // Prevent form submission

    var searchTerm = $("#filter-search").val().toLowerCase(); // Get search term
    var selectedCategory = $("input.agqa-filter-select-hidden")
      .val()
      .toLowerCase(); // Get selected category
    var resultsFound = false; // Flag to track if any result is found

    // Check if either is empty
    $(".agqa-faq-cat-filter").removeClass("faq-cat-active");
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
  // cat filter

  //   $(".agqa-faq-cat-filter li").on("click", function (event) {
  //     event.preventDefault(); // Prevent form submission

  //     var searchTerm = ""; // Get search term
  //     var selectedCategory = $(this).text().toLowerCase(); // Get selected category
  //     var resultsFound = false; // Flag to track if any result is found

  //     $(".agqa-faq-cat-filter").addClass("faq-cat-active");
  //     jQuery(".faq-accordion").removeClass("active");
  //     // Initially hide pagination and "Nothing Found" message
  //     $(".no-found-ctn").hide(); // Hide "Nothing Found" message
  //     $("div#pagination-demo").hide(); // Hide pagination

  //     // alert(currentPage);
  //     // Hide after 3 seconds
  //     setTimeout(function () {
  //       $(".faq-accordion").each(function () {
  //         var faqText = $(this).text().toLowerCase(); // Get all text inside the FAQ accordion
  //         var faqCategory = $(this)
  //           .find(".faq-accodion-status")
  //           .text()
  //           .toLowerCase(); // Optionally, get category text

  //         // If a category is selected, and it matches the FAQ category
  //         if (
  //           (selectedCategory === "all" ||
  //             faqCategory.includes(selectedCategory)) &&
  //           faqText.includes(searchTerm) // Check if the search term is found anywhere in the FAQ content
  //         ) {
  //           $(this).show(); // Show the FAQ item
  //           $(this).find(".faq-accordion-head").removeClass("active"); // Add active class to the head
  //           $(this).find(".faq-accordion-body").slideUp(); // Slide down the body
  //           resultsFound = true; // Mark that at least one result is found
  //         } else if (
  //           // If no category filter is applied and only search term matches anywhere in the FAQ
  //           !selectedCategory &&
  //           faqText.includes(searchTerm)
  //         ) {
  //           $(this).show(); // Show the FAQ item
  //           $(this).find(".faq-accordion-head").removeClass("active"); // Add active class to the head
  //           $(this).find(".faq-accordion-body").slideUp(); // Slide down the body
  //           resultsFound = true; // Mark that at least one result is found
  //         } else {
  //           $(this).hide(); // Hide the FAQ item
  //           $(this).find(".faq-accordion-head").removeClass("active"); // Add active class to the head
  //           $(this).find(".faq-accordion-body").slideUp(); // Slide down the body
  //         }
  //       });

  //       // If no results are found, show the 'nothing found' message
  //       if (!resultsFound) {
  //         $(".no-found-ctn").show(); // Show the 'no results' message
  //         $("div#pagination-demo").hide(); // Hide pagination
  //       } else {
  //         $("div#pagination-demo").show(); // Show pagination
  //         $(".no-found-ctn").hide(); // Hide the 'nothing found' message
  //       }

  //       setTimeout(function () {
  //         // Recalculate pagination based on the filtered visible items
  //         var itemsPerPages = 15;
  //         var totalItemss = $(".faq-accordion:visible").length; // Count only visible items after filtering
  //         var totalPages = Math.ceil(totalItemss / itemsPerPages);

  //         $(".faq-accordion").removeAttr("data-page"); // Remove the data-page attribute
  //         // Reinitialize pagination
  //         $(".faq-accordion:visible").each(function (index) {
  //           var pageNumber = Math.floor(index / itemsPerPages) + 1;
  //           // var pageNumber = "sajid";
  //           jQuery(this).attr("data-page", pageNumber);
  //           jQuery(this).addClass("active");
  //           jQuery(".pagination-ctn ul li.page-item:nth-child(3)")
  //             .addClass("active")
  //             .siblings()
  //             .removeClass("active");
  //           if (pageNumber === 1) {
  //             $(this).show(); // Show items that belong to the current page
  //           } else {
  //             $(this).hide(); // Hide items that do not belong to the current page
  //           }
  //         });
  //         jQuery(".pagination-ctn ul li.page-item").show();
  //         jQuery(".pagination-ctn ul li.page-item")
  //           .not(".prev, .next")
  //           .each(function () {
  //             var pageNumbers = parseInt(jQuery(this).text()); // Get the number of the page
  //             if (pageNumbers === totalPages && totalPages !== 0) {
  //               // Remove all <li> items that come after this one
  //               jQuery(this).nextAll().not(".next").hide();

  //               // Check the <li> just before the Next button
  //               var prevLi = jQuery(
  //                 ".pagination-ctn ul li.page-item.active"
  //               ).next();

  //               // If the next page is hidden or .next button is visible, disable the next button
  //               if (prevLi.is(":hidden")) {
  //                 jQuery(".pagination-ctn ul li.next").addClass("disabled"); // Disable Next button
  //               } else {
  //                 jQuery(".pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
  //               }
  //             }
  //           });
  //       }, 100);
  //     }, 100);
  //   });
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
        // console.log(response);
        if (response.includes("Success")) {
          // alert("Successfully Submitted");
          // const $successMsg = $(
          //     '<div class="submitted-successfully">Liked</div>'
          // );
          // $form.append($successMsg);

          // // Hide after 3 seconds
          // setTimeout(function () {
          //     $successMsg.fadeOut(400, function () {
          //         $(this).remove();
          //     });
          // }, 3000);
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
        // console.log(response);
        if (response.includes("Success")) {
          // alert("Successfully Submitted");
          // const $successMsg = $(
          //     '<div class="submitted-successfully">Dislike</div>'
          // );
          // $form.append($successMsg);

          // // Hide after 3 seconds
          // setTimeout(function () {
          //     $successMsg.fadeOut(400, function () {
          //         $(this).remove();
          //     });
          // }, 3000);
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
  $("#delete-faq-div button#yes-cancel").on("click", function () {
    var faqId = "faq_id=" + $(this).val(); // Get the FAQ ID from the hidden input
    var del = $(this).val();
    // $("#custom-faq-field-popup").show(); // Show the confirmation popup

    // When user clicks "Yes", send AJAX request to delete the FAQ

    // Send AJAX request to delete the FAQ
    var nonce = agqa_ajax.nonce;
    $.ajax({
      url: agqa_ajax.ajax_url,
      type: "POST",
      data: {
        action: "delete_faq",
        form_data: faqId,
        nonce: nonce, // Nonce for security
      },
      success: function (response) {
        // If deletion is successful, hide the popup and remove the FAQ from the DOM

        if (response.includes("Success")) {
          // $(".faq-accordion[data-id='" + del + "']").remove();
          $(".agqa-delete-popup-faq").removeClass("active");
          window.location.href = "/faq/";
          const $successMsg = $(
            `<div class="submitted-successfully">Successfully Deleted.</div>`
          );
          jQuery(".faq-accordions").append($successMsg);
          // Hide after 3 seconds
          setTimeout(function () {
            $successMsg.fadeOut(400, function () {
              $(this).remove();
            });
          }, 3000);
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

  /**
   * faqs report script
   */

  function dataURLtoFile(dataUrl, filename = "image.jpg") {
    // data:[<mime>][;base64],<data>
    const arr = dataUrl.split(",");
    const header = arr[0];
    const mimeMatch = header.match(/data:(.*?);/);
    const mime = mimeMatch ? mimeMatch[1] : "image/jpeg";
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) u8arr[n] = bstr.charCodeAt(n);
    return new File([u8arr], filename, { type: mime });
  }

  async function urlToFile(url, filename = "image.jpg") {
    // works for blob:, http(s):, and (in most browsers) data: as well
    const res = await fetch(url);
    const blob = await res.blob();
    const mime = blob.type || "application/octet-stream";
    const ext = (mime.split("/")[1] || "bin").replace("jpeg", "jpg");
    return new File(
      [blob],
      filename.endsWith(ext) ? filename : `${filename}.${ext}`,
      { type: mime }
    );
  }

  async function collectFilesFromPreviews(
    selector = ".report-preview-item img"
  ) {
    const files = [];
    const $imgs = $(selector);

    for (let i = 0; i < $imgs.length; i++) {
      const src = $imgs.eq(i).attr("src");
      if (!src) continue;

      // make a nice filename
      const baseName = `image_${i + 1}`;

      if (src.startsWith("data:")) {
        // guaranteed to work for your case
        files.push(dataURLtoFile(src, `${baseName}.jpg`));
      } else {
        // blob: or http(s):
        files.push(await urlToFile(src, baseName));
      }
    }
    return files;
  }

  // --- submit handler ---
  $(document).on("submit", "#faq_report_form", async function (e) {
    e.preventDefault();

    const $form = $(this);

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
    const fd = new FormData($form[0]); // grabs your other form fields
    fd.append("action", "report_image_system_upload"); // adjust if your PHP action name differs
    fd.append("nonce", agqa_ajax.nonce); // must match your localized nonce

    // collect files from preview images
    const files = await collectFilesFromPreviews(".report-preview-item img");

    // if (!files.length) {
    //   // alert("Please add at least one image.");
    //   jQuery(".agqa-popup-form-field.report-upload-field")
    //     .append(
    //       '<div class="error-message">Please add at least one image.</div>'
    //     )
    //     .next();
    //   return;
    // }

    // IMPORTANT: match the field name to what your PHP expects: 'file[]' or 'attachments[]'
    for (const f of files) {
      fd.append("attachments[]", f, f.name); // change to 'file[]' if your handler expects that
    }

    // optional UI feedback
    const $msg = $('<div class="submit-warning">Please wait...</div>');
    $form.append($msg);
    setTimeout(
      () =>
        $msg.fadeOut(300, function () {
          $(this).remove();
        }),
      1000
    );

    $.ajax({
      url: agqa_ajax.ajax_url,
      type: "POST",
      data: fd,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (res) {
        // console.log(res);
        if (res.success) {
          // alert("test");
          // console.log("Uploaded:", res.data.url);
          var agqaImages = res.data.url;
          reportSystemFaqs(agqaImages);
        } else {
          $("#ddmu-response").html(
            "<p>" + (res?.message || "Upload failed") + "</p>"
          );
        }
      },
      error: function () {
        $("#ddmu-response").html(
          "<p>Something went wrong. Please try again.</p>"
        );
      },
    });

    //function for faq_report_system

    function reportSystemFaqs(agqaImages) {
      var dataArr = $form
        .serializeArray()
        // "files" (ya jis name se base64 aa raha hai) ko hata do
        .filter(function (f) {
          return f.name !== "report-upload-files" && f.name !== "image";
        });

      var formData = jQuery.param(dataArr);

      // Agar "files" param chahiye lekin empty, to explicitly add:
      formData += "&report-upload-files=";

      // Agar imageUrl bhi bhejni hai:
      formData += "&imageUrl=" + encodeURIComponent(agqaImages || "");
      // console.log(formData);
      // alert(formData);
      // return;
      // Create an object to store form data values
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
          action: "faq_report_system",
          form_data: formData,
          nonce: nonce,
        },
        success: function (response) {
          // console.log(response);
          if (response.includes("Success")) {
            // alert("Successfully Submitted");
            jQuery("input#issue_type").val("");
            jQuery("textarea#detail-description").val("");
            jQuery("input.report-upload-files").val("");
            jQuery(".report-file-preview .report-preview-item").remove();
            jQuery("span.custom-dropdown-default-value")
              .show()
              .siblings()
              .hide();

            jQuery(".agqa-popup-form.agqa-report-popup-form").removeClass(
              "active"
            );
            const $successMsg = $(
              '<div class="submitted-successfully">Report Successfully Submitted</div>'
            );
            jQuery(".faq-main-content").append($successMsg);
            jQuery(".api-cards-wrapper").append($successMsg);

            // Hide after 3 seconds
            setTimeout(function () {
              $successMsg.fadeOut(300, function () {
                $(this).remove();
              });
            }, 3000);
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
    }
  });

  $(".faq-accordion-button.report-button").on("click", function (e) {
    e.preventDefault();
    jQuery("input#issue_type").val("");
    jQuery("textarea#detail-description").val("");
    jQuery("input.report-upload-files").val("");
    jQuery(".report-file-preview .report-preview-item").remove();
    jQuery("span.custom-dropdown-default-value").show().siblings().hide();
  });
});
