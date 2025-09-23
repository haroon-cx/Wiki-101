jQuery(document).ready(function ($) {
  // console.log(cuim_ajax); // ✅ Check if this prints object

  // $(document).on("click", "#cuim-edit-name", function (e) {
  //   e.preventDefault();
  //   $("#cuim-edit-fields").slideToggle();
  // });

  $(".custom-table-row").each(function () {
    const $row = $(this);
    const $loginHistoryIconRow = $row.find(".login-history-icon");
    const $closeButtonRow = $row.find(".close-button");
    const $popupRow = $row.find(".login-history-popup");
    const $popupInnerRow = $row.find(".login-history-popup-inner");

    // Open the login history popup for the specific row
    $loginHistoryIconRow.on("click", function (e) {
      e.stopPropagation();
      $popupRow.addClass("active");
    });

    // Close the login history popup for the specific row
    $closeButtonRow.on("click", function (e) {
      e.preventDefault(); // Prevent form submission if inside a form
      e.stopPropagation();
      $popupRow.removeClass("active");
    });

    // Close popup when clicking outside the popup inner area
    $(document).on("click", function (e) {
      if (
        !$(e.target).closest($popupInnerRow).length && // Ensure the click is outside the inner popup
        $popupRow.hasClass("active") // Ensure the popup is active
      ) {
        $popupRow.removeClass("active"); // Close the popup for this specific row
      }
    });
  });

  // ==========================
  // 6. Pagination
  // ==========================

  var itemsPerPage = 15;
  var totalItems = jQuery(".custom-table-row").length;
  var totalPages = Math.ceil(totalItems / itemsPerPage);

  jQuery("#pagination-demo").twbsPagination({
    totalPages: totalPages,
    visiblePages: 3,
    onPageClick: function (event, page) {
      jQuery(".custom-table-row").hide();
      jQuery('.custom-table-row[data-page="' + page + '"]').show();
      var totalActiveItems = jQuery(".custom-table-row.active").length;
      var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

      // Loop through each page <li> (exclude Prev/Next)
      // Loop through each page <li> (exclude Prev/Next)
      jQuery(".pagination-ctn ul li.page-item").nextAll().not(".next").show();
      jQuery(".pagination-ctn ul li.page-item")
        .not(".prev, .next")
        .each(function () {
          var pageNumberss = parseInt(jQuery(this).text()); // Get the number of the page

          if (pageNumberss === totalActivePages && totalActivePages !== 0) {
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

            // Break the loop since we found the match
            // return false;
          }
        });
    },
  });

  jQuery(".custom-table-row").each(function (index) {
    var page = Math.floor(index / itemsPerPage) + 1;
    jQuery(this).attr("data-page", page);
    if (page === 1) {
      jQuery(this).show();
    } else {
      jQuery(this).hide();
    }
  });

  $(".toggle-password").on("click", function () {
    var passwordField = $(this).siblings('input[type="password"]'); // Get the password input inside the same .password-field container

    // Toggle password visibility
    var fieldType =
      passwordField.attr("type") === "password" ? "text" : "password";
    passwordField.attr("type", fieldType); // Toggle the password visibility

    // Toggle the button class and icon
    $(this).toggleClass("show-pass");
  });

  setTimeout(function () {
    // Clear the date range input field
    $('input[name="daterange"]').val("");
  }, 2000); // 3000 milliseconds = 3 seconds

  // Initialize the date range picker with max 30 days selection
  $('input[name="daterange"]').daterangepicker({
    opens: "right", // Position the calendar
    locale: {
      format: "YYYY/MM/DD", // Specify the date format
    },
    maxSpan: {
      days: 30, // Limit the date range selection to a maximum of 30 days
    },
  });

  // Handle the cancel or clear action
  $('input[name="daterange"]').on(
    "cancel.daterangepicker",
    function (ev, picker) {
      $(this).val(""); // Reset the input field to empty when the user cancels or clears the date range
    }
  );
});

jQuery(function ($) {
  var cropper = null;

  // Helpers to access current DOM nodes (since HTML may be injected later)
  function els() {
    return {
      $input: $("#upload-file-button"),
      $modal: $("#cropper-modal"),
      imgEl: document.getElementById("cropper-image"),
      $preview: $("#cuim-avatar-preview"),
    };
  }

  // 1) Change handler (delegated) — fires even if HTML is added later
  $(document).on("change", "#upload-file-button", function () {
    console.log("File input changed ✅");
    var file = this.files && this.files[0];
    if (!file) {
      console.warn("No file selected");
      return;
    }

    // Spec: JPG only, ≤ 2 MB
    if (file.size > 2 * 1024 * 1024) {
      alert("Max size 2 MB");
      this.value = "";
      return;
    }

    // MIME type check for JPG only (ensures it's a JPEG image)
    var isJpg =
      file.type === "image/jpeg" ||
      file.name.toLowerCase().endsWith(".jpg") ||
      file.name.toLowerCase().endsWith(".jpeg");
    if (!isJpg) {
      alert("Only JPG images are allowed");
      this.value = "";
      return;
    }

    var { $modal, imgEl } = els();
    if (!$modal.length || !imgEl) {
      console.error("Cropper modal/image elements not found in DOM.");
      return;
    }

    var reader = new FileReader();
    reader.onerror = function (e) {
      console.error("FileReader error", e);
      alert("Could not read the image.");
    };
    reader.onload = function (e) {
      // Show modal and start Cropper
      imgEl.src = e.target.result;
      $modal.css("display", "flex");

      try {
        if (cropper) cropper.destroy();
      } catch (e) {}
      cropper = new Cropper(imgEl, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 1,
      });
    };
    reader.readAsDataURL(file);
  });

  // 2) Crop
  $(document).on("click", "#crop-btn", function () {
    if (!cropper) {
      console.warn("Cropper not initialized");
      return;
    }

    var { $modal, $preview, $input } = els();
    var canvas = cropper.getCroppedCanvas({ width: 128, height: 128 });
    if (!canvas) {
      alert("Cropping failed.");
      return;
    }

    canvas.toBlob(
      function (blob) {
        // Update preview (front-end) — you can also send blob/base64 to server if needed
        var url = URL.createObjectURL(blob);
        $preview.attr("src", url);

        // Cleanup
        try {
          cropper.destroy();
        } catch (e) {}
        cropper = null;
        $modal.hide();
        // reset input so same file can be picked again
        if ($input && $input.length) $input.val("");
      },
      "image/jpeg",
      0.92
    );
  });

  // 3) Cancel
  $(document).on("click", "#cancel-btn", function () {
    var { $modal, $input } = els();
    try {
      if (cropper) cropper.destroy();
    } catch (e) {}
    cropper = null;
    $modal.hide();
    if ($input && $input.length) $input.val("");
  });

  // 4) Optional: basic preview fallback (for quick sanity check)
  // Uncomment to verify change event + FileReader, even if Cropper missing
  /*
  $(document).on('change', '#upload-file-button', function(){
    var file = this.files && this.files[0];
    if(!file) return;
    var r = new FileReader();
    r.onload = function(e){ $('#cuim-avatar-preview').attr('src', e.target.result); };
    r.readAsDataURL(file);
  });
  */

  // 1) Toggle the active class on .cuim-profile-dropdown when .cuim-profile-box is clicked
  $(".cuim-profile-box").on("click", function () {
    $(".cuim-profile-dropdown").toggleClass("active"); // Toggle the active class
  });

  // 2) Open the profile form when .cuim-edit-profile-button is clicked
  $(".cuim-edit-profile-button").on("click", function () {
    // Check if .cuim-profile-form-inner is hidden or not
    if ($(".cuim-profile-form-inner").is(":hidden")) {
      $(".cuim-profile-form-inner").show(); // Make the profile form visible
    }

    // Show the profile form wrapper
    $(".cuim-profile-form-wrapper").addClass("active"); // Show the profile form
  });

  // 3) Show the reset password popup and hide the profile form inner when .reset-password-button is clicked
  $(".reset-password-button").on("click", function () {
    $(".cuim-profile-form-inner").hide(100); // Hide the inner profile form
    $(".reset-password-popup").addClass("active"); // Show the reset password popup
  });

  // 4) Close the profile form when popup close button or cancel button is clicked
  $(
    ".popup-cross-icon, .edit-form-buttons .cancel-button, .reset-form-buttons .cancel-button"
  ).on("click", function () {
    $(".cuim-profile-form-wrapper").removeClass("active");
    $(".reset-password-popup").removeClass("active");
  });
});
