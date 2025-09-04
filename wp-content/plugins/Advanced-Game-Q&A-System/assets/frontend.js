jQuery(document).ready(function ($) {
    /*  Filter Accordion (Usama) */

    const $filterWrapper = $(".filter-select");
    const $toggleBtn = $filterWrapper.find(".filter-select-title");
    const $dropdownList = $filterWrapper.find(".filter-select-list");
    const $defaultFilterText = $toggleBtn.find(".filter-default-text");
    const $selectedLabel = $toggleBtn.find(".filter-selected-text");
    const $hiddenInput = $filterWrapper.find(".agqa-filter-select-hidden");

    // Initially hide selected text area
    $selectedLabel.hide();

    // Toggle dropdown only when clicking on button (not selected label or cross)
    $toggleBtn.on("click", function (e) {
        // Prevent dropdown toggle if clicked on selected text or cross
        if ($(e.target).closest(".filter-selected-text").length > 0) return;

        e.preventDefault();
        e.stopPropagation();

        const $this = $(this);
        $(".filter-select-title").not($this).removeClass("active");
        $this.toggleClass("active");
        $dropdownList.stop().slideToggle(300);
    });

    // Handle dropdown item click
    $dropdownList.on("click", "li", function (e) {
        e.stopPropagation();
        const selectedVal = $(this).text().trim();

        $selectedLabel
            .html(`<span>${selectedVal}</span><span class="agqa-cross-icon"></span>`)
            .css({ display: "flex", alignItems: "center", gap: "8px" })
            .show();
        $defaultFilterText.hide();
        $hiddenInput.val(selectedVal);
        $dropdownList.slideUp(300);
        $toggleBtn.removeClass("active");
    });
    // Handle ONLY cross icon click to remove selection
    $filterWrapper.on("click", ".agqa-cross-icon", function (e) {
        e.preventDefault();

        $selectedLabel.fadeOut(200, function () {
            $selectedLabel.empty();
            $defaultFilterText.show();
        });
        $hiddenInput.val("");
        // Set a timeout to allow the hidden input to update and then process the data

    });

    // Close dropdown on outside click
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".filter-select").length) {
            $dropdownList.slideUp(300);
            $(".filter-select-title").removeClass("active");
        }
    });
    /* New Categories Multi Select script */
    const $multi = $("#select-role");
    const $button = $multi.find(".agqa-popup-form-button");
    const $defaultText = $button.find(".default-text");
    const $selectedItemBox = $button.find(".selected-dropdown-item");
    const $multiDropdown = $multi.find(".agqa-popup-form-select");
    const $input = $multi.find(".selected-values");
    // Toggle dropdown and toggle active class
    $button.on("click", function (e) {
        e.stopPropagation();
        $multiDropdown.stop().slideToggle(300);
        $button.toggleClass("active");
    });
    // Select an item
    $multiDropdown.on("click", "li", function (e) {
        e.stopPropagation();
        const $li = $(this);
        const value = $li.data("value");
        const text = $li.text();
        if (!$selectedItemBox.find(`[data-value="${value}"]`).length) {
            $selectedItemBox.append(
                `<span class="tag" data-value="${value}">${text}<span class="agqa-cross-icon"></span></span>`
            );
            $li.addClass("select-item");
            updateState();
        }
    });
    // Remove tag on cross icon click
    $selectedItemBox.on("click", ".agqa-cross-icon", function (e) {
        e.stopPropagation();
        const $tag = $(this).closest(".tag");
        const value = $tag.data("value");
        $tag.remove();
        $multiDropdown.find(`li[data-value="${value}"]`).removeClass("select-item");
        updateState();
    });
    // Update hidden input and default text visibility
    function updateState() {
        const selectedValues = [];
        $selectedItemBox.find(".tag").each(function () {
            selectedValues.push($(this).data("value"));
        });
        $input.val(selectedValues.join(","));
        $defaultText.toggle(selectedValues.length === 0);
    }
    // ✅ Reset function for full popup form
    function resetPopupForm() {
        // Reset multi-select
        $selectedItemBox.empty();
        $multiDropdown.find("li").removeClass("select-item");
        $input.val("");
        $defaultText.show();
        $button.removeClass("active");
        $multiDropdown.hide();
        // Reset other fields in the form
        const $form = $(".agqa-popup-form-inner form");
        $form[0].reset();
        // Clear file preview and hidden file input
        $(".file-preview").hide().empty();
        $('input[type="file"]').val("");
        $(".upload-file").val("");
    }
    // ✅ Add 0.5s delay before clearing fields on cancel or close
    $(".popup-form-cross-icon, .cancel-button").on("click", function () {
        setTimeout(() => {
            resetPopupForm();
        }, 500); // 500ms = 0.5s delay
    });
    // Remove tag using .agqa-cross-icon
    $selectedItemBox.on("click", ".agqa-cross-icon", function (e) {
        e.stopPropagation();
        const $tag = $(this).closest(".tag");
        const value = $tag.data("value");
        $tag.remove();
        $multiDropdown.find(`li[data-value="${value}"]`).removeClass("select-item");
        updateState();
    });
    // Remove tag using .agqa-popup-form-buttons input:button
    $(".agqa-popup-form-buttons").on("click", "button", function (e) {
        e.stopPropagation();
        const value = $(this).data("value");
        const $tag = $selectedItemBox.find(`.tag[data-value="${value}"]`);
        if ($tag.length) {
            $tag.remove();
            $multiDropdown
                .find(`li[data-value="${value}"]`)
                .removeClass("select-item");
            updateState();
        }
    });
    // Close dropdown on outside click
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".agqa-popup-form-multi-select").length) {
            $multiDropdown.slideUp(300);
        }
    });
    $(".agqa-popup-form-buttons").on("click", "input:button", function (e) {
        e.stopPropagation();
        const value = $(this).data("value");
        const $tag = $selectedItemBox.find(`.tag[data-value="${value}"]`);
        if ($tag.length) {
            $tag.remove();
            $multiDropdown
                .find(`li[data-value="${value}"]`)
                .removeClass("select-item");
            updateState();
        }
        // 🔽 Close the dropdown popup just like .popup-form-cross-icon
        $multiDropdown.slideUp(300);
    });
    // Dummy updateState function (make sure your real one is defined elsewhere)
    function updateState() {
        const selectedValues = $selectedItemBox
            .find(".tag")
            .map(function () {
                return $(this).data("value");
            })
            .get();

        $input.val(selectedValues.join(","));
    }
    // Update input value + toggle default text
    function updateState() {
        const tags = $selectedItemBox.find(".tag");
        const values = tags
            .map(function () {
                return $(this).data("value");
            })
            .get();
        $input.val(values.join(","));
        $defaultText.toggle(tags.length === 0);
    }
    $(".form-multi-select").each(function () {
        const $multi = $(this);
        const $button = $multi.find(".agqa-popup-form-button");
        const $defaultText = $button.find(".default-text");
        const $selectedItemBox = $button.find(".selected-dropdown-item");
        const $multiDropdown = $multi.find(".agqa-popup-form-select");
        const $input = $multi.find(".selected-values");
        // Toggle dropdown
        $button.on("click.formMulti", function (e) {
            e.stopPropagation();
            $multiDropdown.stop().slideToggle(300);
            $button.toggleClass("active");
        });
        // Select an item
        $multiDropdown.on("click.formMulti", "li", function (e) {
            e.stopPropagation();
            const $li = $(this);
            const value = $li.data("value");
            const text = $li.text();
            if (!$selectedItemBox.find(`[data-value="${value}"]`).length) {
                $selectedItemBox.append(
                    `<span class="tag" data-value="${value}">${text}<span class="agqa-cross-icon"></span></span>`
                );
                $li.addClass("select-item");
                updateState();
            }
        });
        // Remove tag on cross icon click
        $selectedItemBox.on("click.formMulti", ".agqa-cross-icon", function (e) {
            e.stopPropagation();
            const $tag = $(this).closest(".tag");
            const value = $tag.data("value");
            $tag.remove();
            $multiDropdown
                .find(`li[data-value="${value}"]`)
                .removeClass("select-item");
            updateState();
            setTimeout(function () {
                // Get the value from the hidden input (CSV of category IDs)
                var hiddenInputValue = $("input[name='select-game-category']").val();

                // Split the CSV string into an array of category IDs
                var categoryIds = hiddenInputValue.split(',');

                // Iterate over each custom type list item
                $('.agqa-custom-type-list li').each(function () {
                    var optionCatId = $(this).data('id-cat'); // Get the data-id-cat of the li

                    // Ensure that optionCatId is defined before using .toString()
                    if (optionCatId && categoryIds.includes(optionCatId.toString())) {
                        $(this).show(); // Show the li if it matches any category ID
                    } else {
                        $(this).hide(); // Hide the li if it doesn't match any category ID
                    }
                });

                $('.agqa-cat-id-list .agqa-cross-icon').addClass('agqa-cross-icon-ext')
            }, 500); // Delay of 500ms
        });
        // Update hidden input & default text
        function updateState() {
            const selectedValues = $selectedItemBox
                .find(".tag")
                .map(function () {
                    return $(this).data("value");
                })
                .get();
            $input.val(selectedValues.join(","));
            $defaultText.toggle(selectedValues.length === 0);
        }
        // Close dropdown on outside click (scoped to this instance)
        $(document).on("click.formMulti", function (e) {
            if (!$(e.target).closest($multi).length) {
                $multiDropdown.slideUp(300);
                $button.removeClass("active");
            }
        });
    });
    $('form[data-inited-validation="1"]').each(function () {
        const $form = $(this);

        // Prevent default browser validation
        $form.attr('novalidate', 'novalidate');
        // On submit
        $form.on('submit', function (e) {
            let isValid = true;
            $form.find('.error-message').remove();
            $form.find('.error-field').removeClass('error-field');
            $form.find('.submitted-successfully').remove(); // remove old success messages
            // Validate required fields
            $form.find('[required]').each(function () {
                const $field = $(this);
                const value = $field.val().trim();
                const $fieldWrapper = $field.closest('.form-field, .agqa-popup-form-field');

                if (!value) {
                    isValid = false;
                    const labelText = $fieldWrapper.find('label').text().replace('*', '').trim();

                    if ($field.is(':hidden') && $field.hasClass('selected-values')) {
                        // Custom select hidden input
                        $fieldWrapper.find('.agqa-popup-form-button').addClass('error-field');
                    } else if ($field.hasClass('upload-file')) {
                        $fieldWrapper.find('.browse-link').addClass('error-field');
                    } else {
                        $field.addClass('error-field');
                    }

                    if ($fieldWrapper.find('.error-message').length === 0) {
                        $fieldWrapper.append(`<div class="error-message">${labelText} is required</div>`);
                    }
                }
            });

            // Custom multi-select validation
            $form.find('.form-multi-select').each(function () {
                const $multiSelect = $(this);
                const $hiddenInput = $multiSelect.find('input.selected-values');
                const value = $hiddenInput.val().trim();
                const $fieldWrapper = $multiSelect.closest('.form-field, .agqa-popup-form-field');

                if ($hiddenInput.prop('required') && !value) {
                    isValid = false;
                    $multiSelect.find('.agqa-popup-form-button').addClass('error-field');

                    const labelText = $fieldWrapper.find('label').text().replace('*', '').trim();

                    if ($fieldWrapper.find('.error-message').length === 0) {
                        $fieldWrapper.append(`<div class="error-message">${labelText} is required</div>`);
                    }
                }
            });
            // ✅ Extra validation for Game Info Website
            // const $websiteInput = $('#game-info-website');
            // if ($websiteInput.length) {
            //     const value = $websiteInput.val().trim();
            //     const $fieldWrapper = $websiteInput.closest('.form-field, .agqa-popup-form-field');
            //     if (/\s/.test(value)) {
            //         isValid = false;
            //         $websiteInput.addClass('error-field');
            //         if ($fieldWrapper.find('.error-message').length === 0) {
            //             $fieldWrapper.append(`<div class="error-message">Official Website must not contain spaces. Please re-enter.</div>`);
            //         }
            //     } else if (!/^[a-zA-Z0-9.-]+\.com$/i.test(value)) {
            //         isValid = false;
            //         $websiteInput.addClass('error-field');
            //         if ($fieldWrapper.find('.error-message').length === 0) {
            //             $fieldWrapper.append(`<div class="error-message">Only .com domain names are allowed. Please re-enter.</div>`);
            //         }
            //     }
            // }

            // ✅ Add success message if valid
            // if (isValid) {
            //     const $successMsg = $('<div class="submitted-successfully">Successful submission</div>');
            //     $form.append($successMsg);

            //     // Hide after 3 seconds
            //     setTimeout(function () {
            //         $successMsg.fadeOut(400, function () {
            //             $(this).remove();
            //         });
            //     }, 3000);
            // } else {
            //     e.preventDefault();
            // }
        });

        // Remove error on valid input/select change
        $form.on('input change', '[required]', function () {
            const $field = $(this);
            const value = $field.val().trim();
            const $fieldWrapper = $field.closest('.form-field, .agqa-popup-form-field');

            if (value) {
                if ($field.is(':hidden') && $field.hasClass('selected-values')) {
                    $fieldWrapper.find('.agqa-popup-form-button').removeClass('error-field');
                } else {
                    $field.removeClass('error-field');
                }
                $fieldWrapper.find('.error-message').remove();
                $fieldWrapper.find('.browse-link').removeClass('error-field');
            }
        });

        // Remove error when hidden input (multi-select) value changes
        $form.find('.form-multi-select input.selected-values').on('change', function () {
            const $hiddenInput = $(this);
            const value = $hiddenInput.val().trim();
            const $multiSelect = $hiddenInput.closest('.form-multi-select');
            const $fieldWrapper = $multiSelect.closest('.form-field, .agqa-popup-form-field');

            if (value) {
                $multiSelect.find('.agqa-popup-form-button').removeClass('error-field');
                $fieldWrapper.find('.error-message').remove();
            }
        });

        // Remove error on selecting an item from multi-select
        $form.find('.form-multi-select .agqa-popup-form-select li').on('click', function () {
            const $multiSelect = $(this).closest('.form-multi-select');
            const $fieldWrapper = $multiSelect.closest('.form-field, .agqa-popup-form-field');

            $multiSelect.find('.agqa-popup-form-button').removeClass('error-field');
            $fieldWrapper.find('.error-message').remove();
        });

        // Remove error when file is uploaded
        $form.find('input[type="file"]').on('change', function () {
            const $fileInput = $(this);
            const $fieldWrapper = $fileInput.closest('.form-field, .agqa-popup-form-field');

            $fieldWrapper.find('.browse-link').removeClass('error-field');
            $fieldWrapper.find('input.upload-file').removeClass('error-field');
            $fieldWrapper.find('.error-message').remove();
        });
    });

    // Clear errors on popup close
    $(document).on(
        "click",
        ".cancel-button, .popup-form-cross-icon, .agqa-popup-overlay",
        function () {
            $(".agqa-popup-form-inner form").each(function () {
                $(this).find(".error-message").remove();
                $(this).find(".error-field").removeClass("error-field");
            });
        }
    );

    // Reusable function to clear errors with delay
    function clearPopupFormErrors($form) {
        setTimeout(function () {
            $form.find(".error-message").remove();
            $form.find(".error-field").removeClass("error-field");
            $form.find(".browse-link").removeClass("error-field");
            $form.find(".agqa-popup-form-button").removeClass("error-field");
        }, 500); // 0.5s delay
    }

    // ✅ 1. Click outside the popup form
    $(document).on("mousedown", function (e) {
        const $popupInner = $(".agqa-popup-form-inner");
        if (
            $popupInner.length &&
            !$popupInner.is(e.target) &&
            $popupInner.has(e.target).length === 0
        ) {
            const $form = $popupInner.find("form");
            clearPopupFormErrors($form);
        }
    });

    // ✅ 2. Click on cancel button
    $(document).on("click", ".cancel-button", function (e) {
        e.preventDefault();
        const $form = $(this).closest("form");
        clearPopupFormErrors($form);
    });

    // ✅ 3. Click on cross icon
    $(document).on("click", ".popup-form-cross-icon", function () {
        const $form = $(this).closest("form");
        clearPopupFormErrors($form);
    });

    $(".copy-detail").on("click", function () {
        // 1. Get current and next .api-price-section
        const $currentSection = $(this).closest(".api-price-section");
        const $nextSection = $currentSection.next(".api-price-section");

        // Get label and value for #1
        const label1 = $currentSection.find("span.label").text().trim();
        const value1 = $currentSection.find("h2.large-text").text().trim();

        // Get label and value for #2
        const label2 = $nextSection.find("span.label").text().trim();
        const value2 = $nextSection.find("h2.large-text").text().trim();

        // Get label and value for #3 (provider section)
        const label3 = $(".api-provider-section-inner")
            .find("span.api-info-label")
            .first()
            .text()
            .trim();
        const value3 = $(".api-provider-section-inner")
            .find("span.provider-name-text")
            .first()
            .text()
            .trim();

        // Format copied text
        const copiedText = `1: ${label1}: ${value1}\n2: ${label2}: ${value2}\n3: ${label3}: ${value3}`;

        // Copy using execCommand fallback
        const $tempTextarea = $("<textarea>");
        $("body").append($tempTextarea);
        $tempTextarea.val(copiedText).select();

        try {
            const successful = document.execCommand("copy");
            if (successful) {
                // Append success message in .api-heading-wrapper
                const $message = $('<div class="copied-success">Copy Successful</div>');
                $(".api-heading-wrapper").append($message);

                // Remove after 2s
                setTimeout(function () {
                    $message.fadeOut(300, function () {
                        $(this).remove();
                    });
                }, 2000);
            } else {
                console.error("Copy command was unsuccessful");
            }
        } catch (err) {
            console.error("Unable to copy", err);
        }

        $tempTextarea.remove();
    });

    /*  Add Custom Field Script (Usama)  */

    let editMode = false;
    let editTarget = null;
    let removeTarget = null;
    const maxFields = 4;

    const popupWrapper = $("#custom-field-popup");
    const addFieldPopup = $(".popup-content.add-field");
    const submitConfirmPopup = $(".popup-content.submit-confirm");
    const cancelConfirmPopup = $(".popup-content.cancel-confirm");
    const firstNameInput = $("#first-name");
    const addBtn = $("#add-custom-field-btn");

    // This finds the .form-field containing the add button
    const addBtnContainer = addBtn.closest(".form-field");

    function showPopup(popupEl) {
        $(".popup-content").hide();
        popupEl.show();
        popupWrapper.fadeIn(200);
    }

    function hidePopup() {
        popupWrapper.fadeOut(200);
        removeTarget = null;
    }

    function checkFieldLimit() {
        const count = $(
            ".api-form-wrapper > form > .form-field.custom-field-item"
        ).length;
        if (count >= maxFields) {
            addBtn.addClass("disabled").prop("disabled", true);
        } else {
            addBtn.removeClass("disabled").prop("disabled", false);
        }
    }

    // Open Add Custom Field popup
    addBtn.on("click", function () {
        editMode = false;
        editTarget = null;
        firstNameInput.val("").attr("placeholder", "Description");
        showPopup(addFieldPopup);
    });

    // Save button → go to submit confirmation popup
    $("#save-custom-field").on("click", function (e) {
        e.preventDefault();
        showPopup(submitConfirmPopup);
    });

    // YES submit → save or update
    // $(".yes-submit").on("click", function (e) {
    //     e.preventDefault();
    //     const value = $.trim(firstNameInput.val());
    //     if (!value) {
    //         hidePopup();
    //         return;
    //     }

    //     if (editMode && editTarget) {
    //         editTarget.find("label").text(value);
    //         editTarget.find("input[type='text']").attr("placeholder", value);
    //     } else {
    //         const count = $(".api-form-wrapper > form > .form-field.custom-field-item").length;
    //         if (count >= maxFields) {
    //             hidePopup();
    //             return;
    //         }

    //         const newField = $(`
    //             <div class="form-field custom-field-item">
    //             <input type="hidden" name="custom-label-${count + 1}" value="${value}">
    //                 <label>${value}</label>
    //                 <div class="custom-append-field">
    //                     <input type="text" name="custom-field-${count + 1}" placeholder="${value}">
    //                     <button type="button" class="edit-field-btn"></button>
    //                     <button type="button" class="remove-field-btn"></button>
    //                 </div>
    //             </div>
    //         `);

    //         // Append directly before the "Add Custom Field" .form-field
    //         addBtnContainer.before(newField);
    //     }

    //     checkFieldLimit();
    //     hidePopup();
    // });
    // YES submit → save or update
    $(".yes-submit").on("click", function (e) {
        e.preventDefault();
        const value = $.trim(firstNameInput.val());
        if (!value) {
            hidePopup();
            return;
        }

        if (editMode && editTarget) {
            // Update label text
            editTarget.find("label").text(value);

            // Update hidden input's value
            editTarget.find("input[type='hidden']").val(value);

            // Update input field's placeholder
            editTarget.find("input[type='text']").attr("placeholder", value);
        } else {
            // Recount fields to ensure the next field number is correct
            const count = $(
                ".api-form-wrapper > form > .form-field.custom-field-item"
            ).length;
            if (count >= maxFields) {
                hidePopup();
                return;
            }

            // Create new field
            const newField = $(`
            <div class="form-field custom-field-item">
                <input type="hidden" name="custom-label-${count + 1
                }" value="${value}">
                <label>${value}</label>
                <div class="custom-append-field">
                    <input type="text" name="custom-field-${count + 1
                }" placeholder="${value}">
                    <button type="button" class="edit-field-btn"></button>
                    <button type="button" class="remove-field-btn"></button>
                </div>
            </div>
        `);

            // Append new field before the "Add Custom Field" button
            addBtnContainer.before(newField);
        }

        checkFieldLimit();
        hidePopup();
    });

    // Handle the removal of fields and re-count the remaining fields
    $(document).on("click", ".remove-field-btn", function () {
        // Remove the field
        const field = $(this).closest(".form-field.custom-field-item");
        field.remove();

        // Recount all fields and update their index
        $(".form-field.custom-field-item").each(function (index) {
            // Update the hidden input name and label for each field
            $(this)
                .find("input[type='hidden']")
                .attr("name", `custom-label-${index + 1}`);
            // $(this).find("label").text(`Custom Label ${index + 1}`);
            // $(this).find("input[type='text']").attr("name", `custom-field-${index + 1}`).attr("placeholder", `Custom Field ${index + 1}`);
        });

        checkFieldLimit();
    });

    // Function to add a custom field dynamically
    function addCustomField(fieldNumber, value) {
        const newField = $(`
        <div class="form-field custom-field-item">
            <input type="hidden" name="custom-label-${fieldNumber}" value="${value}">
            <label>${value}</label>
            <div class="custom-append-field">
                <input type="text" name="custom-field-${fieldNumber}" placeholder="${value}">
                <button type="button" class="edit-field-btn"></button>
                <button type="button" class="remove-field-btn"></button>
            </div>
        </div>
    `);

        // Append the new field
        addBtnContainer.before(newField);
    }

    // NO submit → back to Add Field popup
    $(".no-submit").on("click", function () {
        showPopup(addFieldPopup);
    });

    // Cancel button → show cancel confirmation popup
    $(".popup-content .cancel-button").on("click", function () {
        showPopup(cancelConfirmPopup);
    });

    // YES cancel → remove target field
    $("#yes-cancel").on("click", function (e) {
        e.preventDefault();
        if (removeTarget) {
            removeTarget.remove();
            checkFieldLimit();
        }
        hidePopup();
    });

    // NO cancel → back to Add Field popup
    $(".no-cancel").on("click", function () {
        showPopup(addFieldPopup);
    });

    // Cross icon click → close popup
    $(".popup-form-cross-icon").on("click", function () {
        hidePopup();
    });

    // Edit field
    $(document).on("click", ".edit-field-btn", function () {
        editMode = true;
        editTarget = $(this).closest(".form-field.custom-field-item");
        firstNameInput.val(editTarget.find("label").text());
        showPopup(addFieldPopup);
    });

    // Remove field → show cancel confirmation popup
    $(document).on("click", ".remove-field-btn", function () {
        removeTarget = $(this).closest(".form-field.custom-field-item");
        showPopup(cancelConfirmPopup);
    });

    // Click outside to close
    $(document).on("mousedown", function (e) {
        if (
            popupWrapper.is(":visible") &&
            !$(e.target).closest(".popup-content").length
        ) {
            hidePopup();
        }
    });

    checkFieldLimit();

    /* Textarea Characters Limit Script (Usama) */

    const maxChars = 1000;
    let typingTimer;
    const typingDelay = 2000; // Wait 2s after typing stops

    $('textarea').each(function () {
        const $textarea = $(this);
        const $charCounter = $textarea.siblings('.char-counter');
        const $currentCount = $charCounter.find('.current-count');
        const $formResponse = $textarea.siblings('.form-response');

        $textarea.on('input', function () {
            clearTimeout(typingTimer);

            let text = $textarea.val();

            // Stop typing at 1000 characters
            if (text.length > maxChars) {
                $textarea.val(text.substring(0, maxChars));
                text = $textarea.val();
            }

            const length = text.length;
            $currentCount.text(length);

            // Hide success message immediately when typing
            if ($formResponse.hasClass('success')) {
                $formResponse.text('').removeClass('success');
                $charCounter.removeClass('show-message');
            }

            // If empty → hide all
            if (length === 0) {
                $formResponse.text('').removeClass('error success');
                $charCounter.removeClass('show-message');
                return;
            }

            // If exactly at limit → show error instantly
            if (length === maxChars) {
                $formResponse.removeClass('success').addClass('error').text('Unable to enter more characters');
                $charCounter.addClass('show-message');
                return;
            }

            // If under limit → show success after 2s of no typing
            typingTimer = setTimeout(function () {
                if ($textarea.val().length < maxChars) {
                    $formResponse.removeClass('error').addClass('success').text('Successfully submitted');
                    $charCounter.addClass('show-message');
                }
            }, typingDelay);
        });

        // Clear on submit
        $textarea.closest('form').on('submit', function (e) {
            e.preventDefault();
            $formResponse.text('').removeClass('error success');
            $charCounter.removeClass('show-message');
        });
    });

    /* Upload Contract Pdf file script (Usama) */

    const dropArea = $(".custom-upload-area-pdf");
    const browseLink = dropArea.find(".browse-link-pdf");
    const filePreview = dropArea.find(".file-preview-pdf");
    const errorMessage = dropArea.find(".error-message-pdf");
    const fileInput = $("#pdf-upload-input");
    const hiddenInput = $('input[name="upload-contract"]');
    const MAX_SIZE = 5 * 1024 * 1024; // 5MB

    // Open file browser
    browseLink.on("click", function () {
        fileInput.trigger("click");
    });

    // Drag over
    dropArea.on("dragover", function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropArea.addClass("dragging");
        browseLink.text("Drop here");
    });

    // Drag leave
    dropArea.on("dragleave", function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropArea.removeClass("dragging");
        browseLink.text("Upload Contract");
    });

    // Drop file
    dropArea.on("drop", function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropArea.removeClass("dragging");
        browseLink.text("Upload Contract");

        const file = e.originalEvent.dataTransfer.files[0];
        // console.log(files);
        handleFile(file);
    });

    // File selection
    fileInput.on("change", function () {
        handleFile(this.files[0]);
    });

    // File validation + watermark
    function handleFile(file) {
        errorMessage.hide().text(""); // Clear old errors
        if (!file) return;

        if (file.type !== "application/pdf") {
            errorMessage.text("❌ Only PDF format is allowed.").show();
            return;
        }

        if (file.size > MAX_SIZE) {
            errorMessage.text("❌ File size must be 5MB or less.").show();
            return;
        }

        // Apply watermark
        applyPdfWatermark(file)
            .then(function (watermarkedFile) {
                if (!watermarkedFile) {
                    throw new Error("Watermarked file is not available");
                }

                // Create a new DataTransfer object to simulate file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(watermarkedFile);

                // Update the file input with the new file
                document.getElementById("pdf-upload-input").files = dataTransfer.files;

                // Display the file name in the preview
                filePreview.html(`<span>${watermarkedFile.name}</span>`).show();

                // Read the file as a Data URL to store it in a hidden input (for further use)
                const reader = new FileReader();
                reader.onload = function (e) {
                    hiddenInput.val(e.target.result);
                };
                reader.readAsDataURL(watermarkedFile);
            })
            .catch(function (err) {
                console.error(err);
                errorMessage.text("❌ Failed to apply watermark.").show();
            });

        // Function to apply watermark to PDF
        async function applyPdfWatermark(file) {
            const { PDFDocument, rgb, degrees } = PDFLib;

            try {
                // Convert the file into an array buffer
                const existingPdfBytes = await file.arrayBuffer();
                const pdfDoc = await PDFDocument.load(existingPdfBytes);

                // Image path (Ensure the path is correct and accessible)
                const imagePath =
                    "/wp-content/plugins/Advanced-Game-Q&A-System/assets/images/watermaxk.png";
                // Fetch the image as an ArrayBuffer
                const imageUrl = await fetch(imagePath)
                    .then((res) => {
                        if (!res.ok) {
                            throw new Error("Image not found or failed to fetch");
                        }
                        return res.arrayBuffer();
                    })
                    .catch((error) => {
                        console.error("Error fetching the image:", error);
                        return null;
                    });

                if (!imageUrl) {
                    throw new Error("Image could not be fetched or is invalid");
                }

                // Embed the PNG image into the PDF document
                const image = await pdfDoc.embedPng(imageUrl);
                const { width, height } = image.scale(0.5);

                // Get all pages of the PDF document
                const pages = pdfDoc.getPages();

                // Apply the watermark (image) to each page
                pages.forEach((page) => {
                    const x = (page.getWidth() - width) / 2;
                    const y = (page.getHeight() - height) / 2;

                    // Draw the watermark (image) on the page with 45-degree rotation and opacity
                    page.drawImage(image, {
                        x,
                        y,
                        width,
                        height,
                        opacity: 0.4,
                        rotate: degrees(45),
                    });
                });

                // Save the modified PDF as a new file
                const pdfBytes = await pdfDoc.save();
                return new File([pdfBytes], file.name, { type: "application/pdf" });
            } catch (error) {
                console.error("Error while applying watermark:", error);
                return null;
            }
        }
        /**
         * Dont remove
         */
        // Apply watermark
        // applyPdfWatermark(file)
        //   .then(function (watermarkedFile) {
        //     const dataTransfer = new DataTransfer();
        //     dataTransfer.items.add(watermarkedFile);
        //     document.getElementById("pdf-upload-input").files = dataTransfer.files;

        //     filePreview.html(`<span>${watermarkedFile.name}</span>`).show();

        //     const reader = new FileReader();
        //     reader.onload = function (e) {
        //       hiddenInput.val(e.target.result);
        //     };
        //     reader.readAsDataURL(watermarkedFile);
        //   })
        //   .catch(function (err) {
        //     console.error(err);
        //     errorMessage.text("❌ Failed to apply watermark.").show();
        //   });
        // Watermark using PDF-lib
        //   async function applyPdfWatermark(file) {
        //     const { PDFDocument, rgb, degrees } = PDFLib;
        //     const existingPdfBytes = await file.arrayBuffer();

        //     const pdfDoc = await PDFDocument.load(existingPdfBytes);
        //     const pages = pdfDoc.getPages();

        //     // pages.forEach(page => {
        //     //     page.drawText("CONFIDENTIAL", {
        //     //         x: 100,
        //     //         y: page.getHeight() / 2,
        //     //         size: 50,
        //     //         color: rgb(0.95, 0.1, 0.1),
        //     //         opacity: 0.4,
        //     //         rotate: degrees(45)
        //     //     });
        //     // });

        //     const imagePath = "/images/test.png"; // Image ka path
        //     console.log(imagePath);
        //     const image = await pdfDoc.embedPng(imagePath); // Ya embedJpg bhi use kar sakte hain
        //     const { width, height } = image.scale(0.5); // Image ko scale karna (optional)

        //     pages.forEach((page) => {
        //       page.drawImage(image, {
        //         x: 100,
        //         y: page.getHeight() / 2,
        //         width,
        //         height,
        //         opacity: 0.4,
        //         rotate: degrees(45),
        //       });
        //     });

        //     const pdfBytes = await pdfDoc.save();
        //     return new File([pdfBytes], file.name, { type: "application/pdf" });
        //   }
    }

    /* Reorder popup */

    // Open reorder popup
    $(".reorder-button").on("click", function (e) {
        e.stopPropagation();
        $(".reorder-popup").addClass("active");
    });

    // Close popup on cross icon
    $(".reorder-popup-close").on("click", function (e) {
        e.stopPropagation();
        $(".reorder-popup").removeClass("active");
    });

    // Close popup on cancel button
    $(".reorder-popup .cancel-button").on("click", function (e) {
        e.preventDefault(); // prevent form submission if inside a form
        e.stopPropagation();
        $(".reorder-popup").removeClass("active");
    });

    // Close when clicking outside popup inner
    $(document).on("click", function (e) {
        if (
            !$(e.target).closest(".reorder-popup-inner").length &&
            $(".reorder-popup").hasClass("active")
        ) {
            $(".reorder-popup").removeClass("active");
        }
    });



    /* Reorder Sort by dropdown */

    const $sortByDropdown = $(".sort-by-dropdown");
    const $sortByButton = $sortByDropdown.find(".sort-by-dropdown-button");
    const $sortByList = $sortByDropdown.find(".sort-by-dropdown-lists");
    const $sortByDefaultText = $sortByDropdown.find(".default-text");
    const $sortBySelectedText = $sortByDropdown.find(".sortby-selected-text");
    const $sortByResetButton = $sortByDropdown.find(".sort-by-reset-button");

    // Toggle dropdown list
    $sortByButton.on("click", function (e) {
        e.stopPropagation();
        $sortByList.slideToggle(300);
    });

    // Select option from list
    $sortByList.find("ul li").on("click", function () {
        $sortBySelectedText.text($(this).text()).show();
        $sortByDefaultText.hide();
        $sortByList.slideUp(300);
    });

    // Reset to default
    $sortByResetButton.on("click", function () {
        $sortBySelectedText.hide().text("");
        $sortByDefaultText.show();
        $sortByList.slideUp(300);
    });

    // Close dropdown when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest($sortByDropdown).length) {
            $sortByList.slideUp(300);
        }
    });

    /* Approval History by dropdown */

    $(".api-card-approval-history").each(function () {
        const $thisHistory = $(this);
        const $historyHead = $thisHistory.find(".approval-history-head");
        const $historyList = $thisHistory.find(".dropdown-lists");

        $historyHead.on("click", function (e) {
            e.stopPropagation();

            // Close all others & remove their active state
            $(".api-card-approval-history .dropdown-lists")
                .not($historyList)
                .slideUp(300);
            $(".approval-history-head").not($historyHead).removeClass("active");

            // Toggle current one
            $historyList.slideToggle(300);
            $historyHead.toggleClass("active");
        });
    });

    // Close all when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".api-card-approval-history").length) {
            $(".dropdown-lists").slideUp(300);
            $(".api-card-approval-history .approval-history-head").removeClass(
                "active"
            );
        }
    });

    const reportUploadAreaEl = $(".report-upload-area");
    const reportBrowseLinkEl = $(".report-browse-link, label[for='report-upload-input']");
    const reportFileInputEl = $("#report-upload-input");
    const reportFilePreviewEl = $(".report-file-preview");
    const reportHiddenInputEl = $("input[name='report-upload-files']");

    let reportUploadedFiles = [];
    const REPORT_MAX_SIZE = 2 * 1024 * 1024; // 2MB
    const REPORT_MAX_FILES = 5;

    // Open file picker when clicking browse link or label
    reportBrowseLinkEl.on("click", function (e) {
        e.preventDefault();
        if (reportUploadedFiles.length < REPORT_MAX_FILES) {
            reportFileInputEl.trigger("click");
        }
    });

    // Drag events
    reportUploadAreaEl.on("dragover", function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (reportUploadedFiles.length < REPORT_MAX_FILES) {
            $(this).addClass("dragging");
        }
    });

    reportUploadAreaEl.on("dragleave", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass("dragging");
    });

    reportUploadAreaEl.on("drop", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass("dragging");

        const files = e.originalEvent.dataTransfer.files;
        handleReportFiles(files);
    });

    // File input change
    reportFileInputEl.on("change", function () {
        handleReportFiles(this.files);
        $(this).val(""); // reset so same file can be chosen again
    });

    // Handle files
    function handleReportFiles(files) {
        if (!files.length) return;

        for (let file of files) {
            if (reportUploadedFiles.length >= REPORT_MAX_FILES) {
                alert(`You can upload up to ${REPORT_MAX_FILES} images.`);
                break;
            }

            if (file.type !== "image/jpeg") {
                alert("Only JPG format is supported.");
                continue;
            }

            if (file.size > REPORT_MAX_SIZE) {
                alert("Each image must be 2MB or less.");
                continue;
            }

            reportUploadedFiles.push(file);
        }

        updateReportPreview();
        updateReportHiddenInput();
        checkMaxFileLimit();
    }

    // Update preview
    function updateReportPreview() {
        reportFilePreviewEl.empty();

        reportUploadedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewItem = `
                    <div class="report-preview-item">
                        <img src="${e.target.result}" alt="${file.name}" />
                        <button type="button" class="report-remove-file" data-index="${index}"></button>
                    </div>
                `;
                reportFilePreviewEl.append(previewItem).show();
            };
            reader.readAsDataURL(file);
        });
    }

    // Update hidden input with Base64 values
    function updateReportHiddenInput() {
        const fileReaders = [];
        const base64Files = [];

        reportUploadedFiles.forEach((file, i) => {
            fileReaders.push(
                new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        base64Files[i] = e.target.result;
                        resolve();
                    };
                    reader.readAsDataURL(file);
                })
            );
        });

        Promise.all(fileReaders).then(() => {
            reportHiddenInputEl.val(JSON.stringify(base64Files));
        });
    }

    // Remove file without closing popup
    reportFilePreviewEl.on("click", ".report-remove-file", function (e) {
        e.preventDefault();
        e.stopPropagation(); // stop popup from closing

        const index = $(this).data("index");
        reportUploadedFiles.splice(index, 1);

        // Remove only clicked item
        $(this).closest(".report-preview-item").remove();

        // Update hidden input
        updateReportHiddenInput();

        // Hide preview if empty
        if (reportUploadedFiles.length === 0) {
            reportFilePreviewEl.hide();
        } else {
            // Reassign data-index to remaining items
            reportFilePreviewEl.find(".report-remove-file").each(function (i) {
                $(this).attr("data-index", i);
            });
        }

        checkMaxFileLimit();
    });

    // Check the file limit and disable the upload input if reached
    function checkMaxFileLimit() {
        if (reportUploadedFiles.length >= REPORT_MAX_FILES) {
            // Disable file input and prevent further file uploads
            reportFileInputEl.prop("disabled-uploading", true);
            reportBrowseLinkEl.addClass("disabled-uploading");
        } else {
            // Re-enable file input if file count is less than the max limit
            reportFileInputEl.prop("disabled-uploading", false);
            reportBrowseLinkEl.removeClass("disabled-uploading");
        }
    }

    /* Multi Select Draging scroll Script (Usama) */

    var isDown = false;
    var startX;
    var scrollLeft;
    var $container = $('.selected-dropdown-item');

    $container.on('mousedown', function (e) {
        isDown = true;
        $container.addClass('dragging');
        startX = e.pageX - $container.offset().left;
        scrollLeft = $container.scrollLeft();
    });

    $container.on('mouseleave mouseup', function () {
        isDown = false;
        $container.removeClass('dragging');
    });

    $container.on('mousemove', function (e) {
        if (!isDown) return;
        e.preventDefault(); // text select na ho
        var x = e.pageX - $container.offset().left;
        var walk = (x - startX) * 1.5; // drag speed multiplier
        $container.scrollLeft(scrollLeft - walk);
    });

    // Number Max values = 100 

    (function () {
        const sel = '.api-form-ctn input[type="number"]';

        // Keep last key & previous value
        $(document).on('focus', sel, function () {
            $(this).data('prevVal', this.value);
            $(this).data('lastKey', '');
            // Ensure step/min/max for spinner behavior
            this.setAttribute('min', '0');
            this.setAttribute('max', '100');
            this.setAttribute('step', '1');
        });

        // Block invalid keys and handle ArrowUp/Down ourselves
        $(document).on('keydown', sel, function (e) {
            const blocked = ['e', 'E', '+', '-', '.'];
            if (blocked.includes(e.key)) { e.preventDefault(); return; }

            $(this).data('prevVal', this.value);
            $(this).data('lastKey', e.key);

            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                e.preventDefault(); // manual step so we can clamp strictly
                let v = String(this.value || '').replace(/\D/g, '');
                let n = v === '' ? 0 : parseInt(v, 10);
                if (isNaN(n)) n = 0;
                n = e.key === 'ArrowUp' ? Math.min(100, n + 1) : Math.max(0, n - 1);
                this.value = String(n);
                $(this).trigger('input'); // keep any listeners in sync
            }
        });

        // Sanitize input + special rule: typing can't create 100
        $(document).on('input', sel, function (e) {
            const lastKey = $(this).data('lastKey') || '';
            const prevVal = $(this).data('prevVal') ?? '';
            const typedDigit = /^[0-9]$/.test(lastKey);
            const inputType = (e.originalEvent && e.originalEvent.inputType) || '';
            const fromPaste = inputType.indexOf('paste') !== -1;

            // digits only
            let raw = String(this.value).replace(/\D/g, '');
            if (raw === '') { this.value = ''; return; }

            let n = parseInt(raw, 10);
            if (isNaN(n)) { this.value = ''; return; }

            // If user is typing digits or pasting and result > 100, revert (don't coerce to 100)
            if ((typedDigit || fromPaste) && n > 100) {
                let safe = String(prevVal).replace(/\D/g, '');
                let safeNum = safe === '' ? '' : Math.max(0, Math.min(100, parseInt(safe, 10)));
                this.value = safeNum === '' ? '' : String(safeNum);
                return;
            }

            // Otherwise clamp normally 0–100
            if (n > 100) n = 100;
            if (n < 0) n = 0;

            // Enforce length: allow '100' (only via arrow/spinner path), else max 2 digits
            if (n === 100) {
                this.value = '100';
            } else {
                raw = raw.slice(0, 2);
                this.value = String(parseInt(raw, 10));
            }
        });

        // Disallow pasting > 100 (100 must be via ArrowUp/spinner)
        $(document).on('paste', sel, function (e) {
            const t = (e.originalEvent || e).clipboardData.getData('text');
            if (!/^\d{1,3}$/.test(t)) { e.preventDefault(); return; }
            const n = parseInt(t, 10);
            if (!Number.isInteger(n) || n > 100) e.preventDefault();
        });
    })();

    // Prevent space typing
    $(document).on('keydown', '#game-info-website', function (e) {
        if (e.key === ' ') {
            e.preventDefault();
        }
    });
    /* Cusotm Dropdown Sinlge Select Script (Usama) */
    $(document).on("click", ".custom-select-dropdown-title", function (e) {
        e.stopPropagation();
        const $dropdown = $(this).closest(".custom-select-dropdown"),
            $title = $dropdown.find(".custom-select-dropdown-title"),
            $list = $dropdown.find(".custom-select-dropdown-lists");

        // Close other dropdowns
        $(".custom-select-dropdown-lists").not($list).slideUp(300);
        $(".custom-select-dropdown-title").not($title).removeClass("active");

        // Toggle current
        $list.stop(true, true).slideToggle(300);
        $title.toggleClass("active");
        $('.agqa-add-default-btn').hide();
        $('.agqa-add-update-btn').show();
    });

    // Select item
    $(document).on("click", ".custom-select-dropdown-lists li", function () {
        const $item = $(this),
            $dropdown = $item.closest(".custom-select-dropdown"),
            $title = $dropdown.find(".custom-select-dropdown-title"),
            $default = $title.find(".custom-dropdown-default-value"),
            $selected = $title.find(".custom-dropdown-selected-value"),
            $hidden = $dropdown.find("input[type=hidden]"),
            $list = $dropdown.find(".custom-select-dropdown-lists");
        $dataValue = $item.data('value');

        // Update active item
        $dropdown.find("li").removeClass("selected-dropdown-item");
        $item.addClass("selected-dropdown-item");

        // Update UI + hidden input
        $default.hide();
        $selected.text($item.text()).show();
        $hidden.val($dataValue);

        // Close dropdown
        $list.slideUp(300);
        $title.removeClass("active");
    });

    // Close dropdown if clicked outside
    $(document).on("click", function () {
        $(".custom-select-dropdown-lists").slideUp(300);
        $(".custom-select-dropdown-title").removeClass("active");
    });
    (function ($) {
        // Set worker
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
        }

        const $overlay = $(".pdf-modal-overlay");
        const $wrapper = $("#pdfWrapper");
        const $closeBtns = $(".pdf-close, .popup-model-button");

        let pdfDoc = null;
        let currentPdfUrl = null;

        // If you have links like <a class="pdf-link" data-pdf="...">Open</a>
        // and/or inside h2.api-info-value .pdf-link — both are supported:
        $("h2.api-info-value .pdf-link, .pdf-link").on("click", function (e) {
            e.preventDefault();
            const pdfUrl = $(this).data("pdf");
            if (!pdfUrl) return console.error("No data-pdf provided on .pdf-link.");
            openPdfModal(pdfUrl);
        });

        // Close modal
        $closeBtns.on("click", function () {
            closePdfModal();
        });

        // Optional: click outside to close
        $overlay.on("click", function (e) {
            if ($(e.target).is(".pdf-modal-overlay")) closePdfModal();
        });

        function openPdfModal(pdfUrl) {
            $overlay.addClass("active").show();
            loadPDF(pdfUrl);
        }

        function closePdfModal() {
            $overlay.removeClass("active").hide();
            cleanupViewer();
        }

        function cleanupViewer() {
            $wrapper.empty();
            // Leave the original <canvas id="pdfCanvas"> out of the way:
            // If it exists, we won't use it; remove its content so DOM stays clean.
            $("#pdfCanvas").remove(); // optional: delete the single canvas since we render many
            pdfDoc = null;
            currentPdfUrl = null;
        }

        function loadPDF(pdfUrl) {
            if (typeof pdfjsLib === "undefined") {
                console.error("PDF.js not loaded");
                return;
            }
            cleanupViewer();
            currentPdfUrl = pdfUrl;

            pdfjsLib.getDocument(pdfUrl).promise
                .then(function (pdf) {
                    pdfDoc = pdf;
                    renderAllPages();
                })
                .catch(function (err) {
                    console.error("Error loading PDF:", err);
                    $wrapper.html('<div style="padding:24px;text-align:center;color:#a00;">Failed to load PDF.</div>');
                });
        }

        // Render all pages into #pdfWrapper
        function renderAllPages() {
            const total = pdfDoc.numPages;
            // Render sequentially to manage memory
            (async function () {
                for (let pageNum = 1; pageNum <= total; pageNum++) {
                    await renderPage(pageNum);
                }
            })();
        }

        async function renderPage(pageNum) {
            const page = await pdfDoc.getPage(pageNum);

            // Compute scale to fit wrapper width
            const initialViewport = page.getViewport({ scale: 1.0 });
            const containerWidth = $wrapper.width() || 800;
            const scale = containerWidth / initialViewport.width;
            const viewport = page.getViewport({ scale });

            // Create canvas per page
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            // High DPI rendering
            const dpr = window.devicePixelRatio || 1;
            canvas.width = Math.floor(viewport.width * dpr);
            canvas.height = Math.floor(viewport.height * dpr);
            canvas.style.width = Math.floor(viewport.width) + "px";
            canvas.style.height = Math.floor(viewport.height) + "px";

            canvas.setAttribute("role", "img");
            canvas.setAttribute("aria-label", "PDF page " + pageNum);

            $wrapper.append(canvas);

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport,
                transform: dpr !== 1 ? [dpr, 0, 0, dpr, 0, 0] : null
            };

            await page.render(renderContext).promise;
        }

        // Optional: handle window resize to re-render to new width
        const debounce = (fn, wait = 200) => {
            let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(null, args), wait); };
        };
        $(window).on("resize", debounce(function () {
            if (!$overlay.hasClass("active") || !pdfDoc) return;
            // Re-render all pages at the new width
            const url = currentPdfUrl;
            loadPDF(url);
        }, 200));
    })(jQuery);

    $(function () {
        // Cancel Confirmation Modal
        const $cancelModal = $('#cancel-form-confirmation');
        const openCancelBtnSelector = '#cancel-confirmation-button';

        // Submit Confirmation Modal
        const $submitModal = $('#confirm-submit-popup');
        const openSubmitBtnSelector = '#confirm-submit-popup-button';

        // Open Cancel Modal
        $(document).on('click', openCancelBtnSelector, function (e) {
            e.preventDefault();
            $cancelModal.addClass('active');
        });

        // Open Submit Modal
        $(document).on('click', openSubmitBtnSelector, function (e) {
            e.preventDefault();
            $submitModal.addClass('active');
        });

        // Close modals using cross icon (Cancel and Submit modals)
        $(".popup-form-cross-icon").on("click", function (e) {
            e.preventDefault(); // prevent default button behavior if needed
            $("#cancel-form-confirmation").removeClass("active");
            $("#confirm-submit-popup").removeClass("active");
        });

        // Close modals using No buttons (Cancel and Submit modals)
        $(".no-form-cancel").on("click", function (e) {
            e.preventDefault();
            $("#cancel-form-confirmation").removeClass("active");
            $("#confirm-submit-popup").removeClass("active");
        });

        $(".no-confirm-submit").on("click", function (e) {
            e.preventDefault();
            $("#confirm-submit-popup").removeClass("active");
        });

        // Close on outside click for Cancel Modal
        $(document).on('click', function (e) {
            if (!$cancelModal.hasClass('active')) return;
            if (!$(e.target).closest($cancelModal.add(openCancelBtnSelector)).length) {
                $cancelModal.removeClass('active');
            }
        });

        // Close on outside click for Submit Modal
        $(document).on('click', function (e) {
            if (!$submitModal.hasClass('active')) return;
            if (!$(e.target).closest($submitModal.add(openSubmitBtnSelector)).length) {
                $submitModal.removeClass('active');
            }
        });

        // Submit action (if needed)
        $("#confirm-submit").on("click", function () {
            // Handle submit logic here, if needed.
            // For example, you can submit the form or trigger an action on form submit.
            // Example: $("#yourForm").submit();
        });
    });



    $('input[type="text"]').on('input', function() {
    var maxLength = 150;
    var $input = $(this);
    var $errorMessage = $input.next('#error-message'); // Look for the error message next to the input
    var $formField = $input.closest('.form-field'); // Find the parent .form-field of the current input
    // Check if input exceeds maxLength
    if ($input.val().length > maxLength) {
      $input.val($input.val().substring(0, maxLength)); // Truncate the value to maxLength
      $formField.addClass('error-field-input'); // Add 'error' class to the parent .form-field
      // Append error message if it doesn't already exist
      if ($errorMessage.length === 0) {
        $('<div id="error-message">Max 150 characters allowed.</div>')
          .insertAfter($input); // Insert the error message after the input
      }
    } else {
      $formField.removeClass('error-field-input'); // Remove 'error' class if input is valid
      // Remove the error message if input length is valid
      if ($errorMessage.length > 0) {
        $errorMessage.remove();
      }
    }
});
jQuery('form#report_form').submit('submit', function(){
    const $form = $(this);
 let isValid = true;
    // Check if all required fields are filled
    $form.find("[required]").each(function () {
      const field = $(this);
      if (!field.val()) {
        // If the field is empty
        isValid = false;
        return false;
      }
    });
    // Extra validation for Game Info Website
    const $websiteInput = $("#game-info-website");
    if ($websiteInput.length) {
      const value = $websiteInput.val().trim();
      const $fieldWrapper = $websiteInput.closest(
        ".form-field, .agqa-popup-form-field"
      );
      if (/\s/.test(value)) {
        isValid = false;
        $websiteInput.addClass("error-field");
        if ($fieldWrapper.find(".error-message").length === 0) {
          $fieldWrapper.append(
            `<div class="error-message">Official Website must not contain spaces. Please re-enter.</div>`
          );
        }
      } else if (value && !/^[a-zA-Z0-9.-]+\.[a-z]{2,}$/i.test(value)) {
        isValid = false;
        $websiteInput.addClass("error-field");
        if ($fieldWrapper.find(".error-message").length === 0) {
          $fieldWrapper.append(
            `<div class="error-message">Please enter a valid domain(e.g. .com, .net).</div>`
          );
        }
      }
    }
    if (!isValid) {
      return;
    }
    
    const $successMsg = $(
    '<div class="submitted-successfully">Successful submission</div>'
);
    $form.append($successMsg);

// Hide after 3 seconds
setTimeout(function () {
    $successMsg.fadeOut(400, function () {
    $(this).remove();
    });
    $('.agqa-popup-form.agqa-report-popup-form').removeClass('active')
}, 3000);
});
$('input[type="text"]').on('input', function() {
    var maxLength = 150;
    var $input = $(this);
    var $errorMessage = $input.next('#error-message'); // Look for the error message next to the input
    var $formField = $input.closest('.form-field'); // Find the parent .form-field of the current input
    // Check if input exceeds maxLength
    if ($input.val().length > maxLength) {
      $input.val($input.val().substring(0, maxLength)); // Truncate the value to maxLength
      $formField.addClass('error-field-input'); // Add 'error' class to the parent .form-field
      // Append error message if it doesn't already exist
      if ($errorMessage.length === 0) {
        $('<div id="error-message">Max 150 characters allowed.</div>')
          .insertAfter($input); // Insert the error message after the input
      }
    } else {
      $formField.removeClass('error-field-input'); // Remove 'error' class if input is valid
      // Remove the error message if input length is valid
      if ($errorMessage.length > 0) {
        $errorMessage.remove();
      }
    }
});
});

