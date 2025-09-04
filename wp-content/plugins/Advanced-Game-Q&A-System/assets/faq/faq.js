jQuery(document).ready(function() {

  // ==========================
  // 1. Initialize Froala Editor
  // ==========================
  jQuery('.editor-faq').each(function() {
    var editor = new FroalaEditor(this, {
      toolbarButtons: [
        'bold', 'italic', 'underline', 'strikeThrough',
        'fontFamily', 'fontSize', 'color', 'paragraphFormat', 'align',
        'formatOL', 'formatUL', 'outdent', 'indent', 'clearFormatting',
        'insertLink', 'undo', 'redo', 'fullscreen', 'html'
      ],
      imageUpload: false,
      videoUpload: false,
      fileUpload: false
    });

    const maxChars = 3000;
    let typingTimer;
    const typingDelay = 2000;

    editor.events.on('input', function() {
      const $editorContent = editor.$el.find('.fr-view');
      const $charCounter = jQuery(this).closest('.form-field-editor').find('.char-counter');
      const $currentCount = $charCounter.find('.current-count');
      const $formResponse = jQuery(this).closest('.form-field-editor').find('.form-response');

      // Get text content
      let text = $editorContent.text();
      let length = text.length;

      // Limit to maxChars
      if(length > maxChars) {
        editor.html.set(text.substring(0, maxChars));
        text = editor.html.get();
        length = text.length;
      }

      // Update counter
      $currentCount.text(length);

      // Reset messages
      if($formResponse.hasClass('success')) {
        $formResponse.text('').removeClass('success');
        $charCounter.removeClass('show-message');
      }

      // Empty input
      if(length === 0) {
        $formResponse.text('').removeClass('error success');
        $charCounter.removeClass('show-message');
        return;
      }

      // At limit
      if(length === maxChars) {
        $formResponse.text('Unable to enter more characters').removeClass('success').addClass('error');
        $charCounter.addClass('show-message');
        return;
      }

      // Under limit → show success after typing stops
      clearTimeout(typingTimer);
      typingTimer = setTimeout(function() {
        if(editor.$el.find('.fr-view').text().length < maxChars) {
          $formResponse.text('Successfully submitted').removeClass('error').addClass('success');
          $charCounter.addClass('show-message');
        }
      }, typingDelay);
    });
  });

  // ==========================
  // 2. FAQ Accordion Toggle
  // ==========================
  jQuery('.faq-accordion-head').click(function() {
    jQuery('.faq-accordion-body').not(jQuery(this).next()).slideUp();
    jQuery(this).next('.faq-accordion-body').stop(true,true).slideToggle();
    jQuery(this).toggleClass('active');
  });

// ==========================
// 3. Like/Dislike Buttons
// ==========================
let userSelections = {};  // This will track the state for each faqId

jQuery('.like-button').click(function() {
  let faqId = jQuery(this).data('faq-id');
  let likeCountSpan = jQuery(this).find('.like-coounting');
  let unlikeButton = jQuery(this).closest('.faq-accordion').find('.unlike-button');
  let unlikeCountSpan = unlikeButton.find('.unlike-coounting');

  // If the like button is clicked, handle state changes
  if (userSelections[faqId] === 'like') {
    // If "like" is already active, deactivate it
    likeCountSpan.text(parseInt(likeCountSpan.text()) - 1);
    jQuery(this).removeClass('active');
    userSelections[faqId] = null;  // Reset the state
  } else {
    // If "dislike" was active, deactivate it
    if (userSelections[faqId] === 'dislike') {
      unlikeCountSpan.text(parseInt(unlikeCountSpan.text()) - 1);
      jQuery(unlikeButton).removeClass('active');
    }

    // Activate "like" button
    likeCountSpan.text(parseInt(likeCountSpan.text()) + 1);
    jQuery(this).addClass('active');
    userSelections[faqId] = 'like';  // Set the state to 'like'
  }
});

jQuery('.unlike-button').click(function() {
  let faqId = jQuery(this).data('faq-id');
  let dislikeCountSpan = jQuery(this).find('.unlike-coounting');
  let likeButton = jQuery(this).closest('.faq-accordion').find('.like-button');
  let likeCountSpan = likeButton.find('.like-coounting');

  // If the dislike button is clicked, handle state changes
  if (userSelections[faqId] === 'dislike') {
    // If "dislike" is already active, deactivate it
    dislikeCountSpan.text(parseInt(dislikeCountSpan.text()) - 1);
    jQuery(this).removeClass('active');
    userSelections[faqId] = null;  // Reset the state
  } else {
    // If "like" was active, deactivate it
    if (userSelections[faqId] === 'like') {
      likeCountSpan.text(parseInt(likeCountSpan.text()) - 1);
      jQuery(likeButton).removeClass('active');
    }

    // Activate "dislike" button
    dislikeCountSpan.text(parseInt(dislikeCountSpan.text()) + 1);
    jQuery(this).addClass('active');
    userSelections[faqId] = 'dislike';  // Set the state to 'dislike'
  }
});

  // ==========================
  // 4. Copy Button
  // ==========================
  jQuery('.copy-button').click(function() {
    var question = jQuery(this).closest('.faq-accordion').find('.faq-accordion-head h2').text();
    var answer = jQuery(this).closest('.faq-accordion').find('.faq-accordion-body p').text();
    var textToCopy = 'Q: ' + question + ' Ans: ' + answer;

    var tempInput = document.createElement('textarea');
    tempInput.value = textToCopy;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    var successMessage = '<div class="copied-success">Copy Successful</div>';
    jQuery(this).closest('.faq-accordion').append(successMessage);
    setTimeout(function() {
      jQuery('.copied-success').fadeOut(500,function(){ jQuery(this).remove(); });
    },3000);
  });

  // ==========================
  // 6. Pagination
  // ==========================
  var itemsPerPage = 15;
  var totalItems = jQuery('.faq-accordion').length;
  var totalPages = Math.ceil(totalItems / itemsPerPage);

  jQuery('#pagination-demo').twbsPagination({
    totalPages: totalPages,
    visiblePages: 3,
    onPageClick: function(event,page) {
      jQuery('.faq-accordion').hide();
      jQuery('.faq-accordion[data-page="' + page + '"]').show();
    }
  });

  jQuery('.faq-accordion').each(function(index) {
    var page = Math.floor(index / itemsPerPage) + 1;
    jQuery(this).attr('data-page', page);
    if(page === 1) { jQuery(this).show(); } else { jQuery(this).hide(); }
  });

  // ==========================
  // 7. Search with Highlighting
  // ==========================
  jQuery("input[type='search']").on("input",function() {
    var query = jQuery(this).val().toLowerCase();

    if(query !== "") {
      jQuery(".faq-main-content").find("*").each(function() {
        var $node = jQuery(this);
        var text = $node.text();
        if($node.children().length === 0 && text.toLowerCase().includes(query)) {
          var newText = text.replace(new RegExp("\\b" + query + "\\b","gi"), function(match) {
            return '<span class="highlighted">' + match + '</span>';
          });
          $node.html(newText);
        }
      });
    } else {
      jQuery(".faq-main-content").find(".highlighted").each(function() {
        var $highlightedNode = jQuery(this);
        $highlightedNode.replaceWith($highlightedNode.text());
      });
    }
  });

  // 3sep 2025 (Usama)

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
  if (!jQuery("#custom-faq-field-popup-inner").length) {
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
});