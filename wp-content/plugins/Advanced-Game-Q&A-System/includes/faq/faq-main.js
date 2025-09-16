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

        // Check if either is empty
        $('.agqa-faq-cat-filter').removeClass('faq-cat-active');
        jQuery('.faq-accordion').removeClass("active");

        // highlighted
        var query = searchTerm;

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

        if (!searchTerm && !selectedCategory) {
            $(".no-found-ctn").hide(); // Hide the 'nothing found' message
            $('.faq-accordion').show(); // Show the FAQ item
            var itemsPerPage = 15;
            var totalItems = $(".faq-accordion").filter(":visible").length; // Count only visible items

            if (totalItems > itemsPerPage) {
                $("#pagination-demo").show(); // Show pagination if more than 15 visible items
            } else {
                $("#pagination-demo").hide(); // Hide pagination if 15 or fewer visible items
            }
            $(document).find(".faq-accordion-head").removeClass("active"); // Add active class to the head
            $(document).find(".faq-accordion-body").slideUp(); // Slide down the body
            return; // Return early if either is empty
        }


        // Initially hide pagination and "Nothing Found" message
        $(".no-found-ctn").hide(); // Hide "Nothing Found" message
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
            $(".no-found-ctn").show(); // Show the 'no results' message
            $("div#pagination-demo").hide(); // Hide pagination
        } else {
            $("div#pagination-demo").show(); // Show pagination
            $(".no-found-ctn").hide(); // Hide the 'nothing found' message
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


        }, 500);
    });
    // cat filter


    $(".agqa-faq-cat-filter li").on("click", function (event) {
        event.preventDefault(); // Prevent form submission

        var searchTerm = ""; // Get search term
        var selectedCategory = $(this)
            .text()
            .toLowerCase(); // Get selected category
        var resultsFound = false; // Flag to track if any result is found

        $('.agqa-faq-cat-filter').addClass('faq-cat-active');
        jQuery('.faq-accordion').removeClass("active");
        // Initially hide pagination and "Nothing Found" message
        $(".no-found-ctn").hide(); // Hide "Nothing Found" message
        $("div#pagination-demo").hide(); // Hide pagination

        // alert(currentPage);
        // Hide after 3 seconds
        setTimeout(function () {
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
                    $(this).find(".faq-accordion-head").removeClass("active"); // Add active class to the head
                    $(this).find(".faq-accordion-body").slideUp(); // Slide down the body
                    resultsFound = true; // Mark that at least one result is found
                } else if (
                    // If no category filter is applied and only search term matches anywhere in the FAQ
                    !selectedCategory &&
                    faqText.includes(searchTerm)
                ) {
                    $(this).show(); // Show the FAQ item
                    $(this).find(".faq-accordion-head").removeClass("active"); // Add active class to the head
                    $(this).find(".faq-accordion-body").slideUp(); // Slide down the body
                    resultsFound = true; // Mark that at least one result is found
                } else {
                    $(this).hide(); // Hide the FAQ item
                    $(this).find(".faq-accordion-head").removeClass("active"); // Add active class to the head
                    $(this).find(".faq-accordion-body").slideUp(); // Slide down the body
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
            }, 100);

        }, 100);
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
        // $("#custom-faq-field-popup").show(); // Show the confirmation popup

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
                    $(".faq-accordion[data-id='" + del + "']").remove();
                    $("#custom-faq-field-popup").removeClass("active");
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
