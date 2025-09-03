jQuery(document).ready(function() {
  jQuery('.faq-accordion-head').click(function() {
    // Close all other open accordion bodies
    jQuery('.faq-accordion-body').not(jQuery(this).next()).slideUp();
    
    // Toggle the clicked accordion body
    jQuery(this).next('.faq-accordion-body').stop(true, true).slideToggle();

    // Toggle the 'active' class on the clicked header
    jQuery(this).toggleClass('active');
  });
  let userSelections = {};

  // Handle click on Like button
  jQuery('.like-button').click(function() {
    let faqId = jQuery(this).data('faq-id');
    let likeCountSpan = jQuery(this).find('.like-coounting');
    let unlikeButton = jQuery(this).closest('.faq-accordion').find('.unlike-button');
    let unlikeCountSpan = unlikeButton.find('.unlike-coounting');

    // If the user has already clicked Like
    if (userSelections[faqId] === 'like') {
      // User is changing from Like to None
      likeCountSpan.text(parseInt(likeCountSpan.text()) - 1);
      jQuery(this).removeClass('active');  // Remove 'active' class from Like
      userSelections[faqId] = null;  // Reset the user's selection
    } else if (userSelections[faqId] === 'dislike') {
      // User is changing from Dislike to Like
      unlikeCountSpan.text(parseInt(unlikeCountSpan.text()) - 1);  // Decrease Dislike count
      likeCountSpan.text(parseInt(likeCountSpan.text()) + 1);       // Increase Like count
      jQuery(this).addClass('active');  // Add 'active' class to Like
      unlikeButton.removeClass('active');  // Remove 'active' class from Dislike
      userSelections[faqId] = 'like';  // Update user's selection to Like
    } else {
      // User is selecting Like for the first time
      likeCountSpan.text(parseInt(likeCountSpan.text()) + 1);
      jQuery(this).addClass('active');  // Add 'active' class to Like
      userSelections[faqId] = 'like';  // Set user's selection to Like
    }
  });

  // Handle click on Dislike button
  jQuery('.unlike-button').click(function() {
    let faqId = jQuery(this).data('faq-id');
    let dislikeCountSpan = jQuery(this).find('.unlike-coounting');
    let likeButton = jQuery(this).closest('.faq-accordion').find('.like-button');
    let likeCountSpan = likeButton.find('.like-coounting');

    // If the user has already clicked Dislike
    if (userSelections[faqId] === 'dislike') {
      // User is changing from Dislike to None
      dislikeCountSpan.text(parseInt(dislikeCountSpan.text()) - 1);
      jQuery(this).removeClass('active');  // Remove 'active' class from Dislike
      userSelections[faqId] = null;  // Reset the user's selection
    } else if (userSelections[faqId] === 'like') {
      // User is changing from Like to Dislike
      likeCountSpan.text(parseInt(likeCountSpan.text()) - 1);  // Decrease Like count
      dislikeCountSpan.text(parseInt(dislikeCountSpan.text()) + 1);  // Increase Dislike count
      jQuery(this).addClass('active');  // Add 'active' class to Dislike
      likeButton.removeClass('active');  // Remove 'active' class from Like
      userSelections[faqId] = 'dislike';  // Update user's selection to Dislike
    } else {
      // User is selecting Dislike for the first time
      dislikeCountSpan.text(parseInt(dislikeCountSpan.text()) + 1);
      jQuery(this).addClass('active');  // Add 'active' class to Dislike
      userSelections[faqId] = 'dislike';  // Set user's selection to Dislike
    }
  });
      jQuery('.copy-button').click(function() {
        // Get the question (h2 text) and the answer (p text) dynamically
        var question = jQuery(this).closest('.faq-accordion').find('.faq-accordion-head h2').text();
        var answer = jQuery(this).closest('.faq-accordion').find('.faq-accordion-body p').text();

        // Format the copied text as "Q: <Question> Ans: <Answer>"
        var textToCopy = 'Q: ' + question + ' Ans: ' + answer;

        // Create a temporary text area to hold the content for copying
        var tempInput = document.createElement('textarea');
        tempInput.value = textToCopy;

        // Append the textarea to the document body
        document.body.appendChild(tempInput);

        // Select the text and copy it to clipboard
        tempInput.select();
        document.execCommand('copy');

        // Remove the temporary input element from the document
        document.body.removeChild(tempInput);

        // Append the success message after copying
        var successMessage = '<div class="copied-success">Copy Successful</div>';
        jQuery(this).closest('.faq-accordion').append(successMessage);

        // Optionally remove the success message after 3 seconds
        setTimeout(function() {
            jQuery('.copied-success').fadeOut(500, function() {
                jQuery(this).remove();
            });
        }, 3000);
    });
        jQuery('.delete-button').click(function() {
        // Get the unique reference for the clicked .faq-accordion item
        var faqAccordionItem = jQuery(this).closest('.faq-accordion');

        // Hide or delete the specific .faq-accordion item
        faqAccordionItem.fadeOut(500, function() {
            // Optionally, remove the item from DOM after fading out
            faqAccordionItem.remove();
        });
    });
   
   
    jQuery("input[type='search']").on("input", function() {
        var query = jQuery(this).val().toLowerCase();  // Get the search query

        // If the query is not empty
        if (query !== "") {
            // Loop through all elements inside the FAQ content
            jQuery(".faq-main-content").find("*").each(function() {
                var $node = jQuery(this);
                var text = $node.text();  // Get the text content of the node

                // Only modify text nodes (not images, buttons, etc.)
                if ($node.children().length === 0 && text.toLowerCase().includes(query)) {
                    // Highlight matching text by wrapping it in <span class="highlighted">
                    var newText = text.replace(new RegExp("\\b" + query + "\\b", "gi"), function(match) {
                        return '<span class="highlighted">' + match + '</span>';
                    });

                    // Set the new text with highlighted part
                    $node.html(newText);
                }
            });
        } else {
            // Clear highlight if the input is empty, but don't overwrite the entire text
            jQuery(".faq-main-content").find(".highlighted").each(function() {
                var $highlightedNode = jQuery(this);
                // Remove the highlight, keep the text
                $highlightedNode.replaceWith($highlightedNode.text());
            });
        }
    });

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
      if (!jQuery(e.target).closest("#custom-faq-field-popup-inner").length) {
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
      setTimeout(function() {
        // Append success message to the body or a specific container
        jQuery(".faq-main-content").append('<div class="success-message">Successfully Deleted</div>');

        // Hide the success message after 3 seconds
        setTimeout(function() {
          jQuery(".success-message").fadeOut(function() {
            jQuery(this).remove(); // Remove the message from the DOM after it fades out
          });
        }, 1500); // 3 seconds after showing the message
      }, 200); // Show the message 0.5 seconds after clicking "Yes"
    });

   const maxLength = 100;

    // Create or get the message element
    function createMessage(input) {
      let msg = input.closest('.faq-template').find('#filter-search-msg');
      if (msg.length === 0) {
        msg = jQuery('<div>', { 
          id: 'filter-search-msg',
          class: 'field-helper',
          role: 'status',
          'aria-live': 'polite'
        }).insertAfter(input);
      }
      return msg;
    }

    // Real-time typing event
    jQuery('#filter-search').on('input', function() {
      const input = jQuery(this);
      const msg = createMessage(input);

      if (input.val().length > maxLength) {
        input.val(input.val().slice(0, maxLength)); // Trim if it exceeds max length
        msg.text('Unable to input more characters.').addClass('is-error');
      } else {
        msg.removeClass('is-error').text(''); // Clear message if valid
      }
    });

    // On form submit, prevent submission if the input exceeds 100 characters
    jQuery('.faq-template').on('click', '#agqa-game-filter', function(e) {
      const input = jQuery(this).closest('.faq-template').find('#filter-search');
      const msg = createMessage(input);

      if (input.val().length > maxLength) {
        e.preventDefault(); // Prevent form submission
        msg.text('Unable to input more characters.').addClass('is-error');
      } else {
        msg.removeClass('is-error').text(''); // Clear message if valid
      }
    });

  });
  
document.querySelectorAll('.editor-faq').forEach(function(el) {
  // Initialize Froala Editor
  var editor = new FroalaEditor(el, {
    // Toolbar setup
    toolbarButtons: {
      moreText: {
        buttons: [
          'bold', 'italic', 'underline', 'strikeThrough',
          'fontFamily', 'fontSize', 'color', 'paragraphFormat', 'align',
          'formatOL', 'formatUL', 'outdent', 'indent', 'clearFormatting'
        ]
      },
      moreRich: {
        buttons: [
          'insertLink'   // ✅ allow links only
        ]
      },
      moreMisc: {
        buttons: ['undo', 'redo', 'fullscreen', 'html']
      }
    },

    // Disable uploads
    imageUpload: false,
    videoUpload: false,
    fileUpload: false,

    // Ensure editor re-initializes after pasting
    events: {
      'paste.after': function() {
        // Reset toolbar or any custom actions to avoid the toolbars from hiding
        setTimeout(() => {
          editor.refresh();
        }, 100);
      }
    }
  });

  // Manually refresh the editor after every paste event to prevent hiding
  el.addEventListener('paste', function() {
    setTimeout(function() {
      editor.refresh(); // Refresh the editor to show tools again
    }, 100);  // Give a small delay to allow paste operation to complete
  });
});
