jQuery(document).ready(function ($) {
  // console.log(cuim_ajax); // ✅ Check if this prints object

  // $.post(cuim_ajax.ajax_url, {
  //     action: 'test_ajax',
  //     security: cuim_ajax.nonce
  // }, function(response){
  //     console.log('RES' , response);
  // });

  function showModal(modal) {
    $(modal).fadeIn();
  }

  // $('.btn').on('click', function () {
  //     $('#cuim-create-ip-modal').fadeIn();
  // });
  $(".cuim-close").on("click", function () {
    $(".cuim-modal").fadeOut();
  });
  $(".cuim-show-ip-create").on("click", function () {
    $("#cuim-create-ip-modal").fadeIn();
  });
  $(".cuim-show-create").on("click", function () {
    showModal("#cuim-create-modal");
  });

  // Create User (Corrected)
  $("#cuim-create-form").on("submit", function (e) {
    e.preventDefault();

    var formData = $(this).serialize();

    $.post(ajaxurl, formData + "&action=cuim_create_user", function (response) {
      if (response.success) {
        $("#cuim-create-message").html(
          '<span style="color: green;">' + response.data + "</span>"
        );
        $("#cuim-create-form")[0].reset();
        location.reload();
      } else {
        $("#cuim-create-message").html(
          '<span style="color: red;">' + response.data + "</span>"
        );
      }
    });
  });
  // Edit User
  $(".cuim-edit-button").on("click", function () {
    const user = $(this).data("user"); // assumed to be a JS object

    $('#cuim-edit-form [name="user_id"]').val(user.ID);
    $('#cuim-edit-form [name="cuim_name"]').val(user.name);
    $('#cuim-edit-form [name="cuim_email"]').val(user.email);
    $('#cuim-edit-form [name="cuim_role"]').val(user.role); // set role
    $("#cuim-edit-modal").show();
  });

  // 💾 Save edited user via AJAX
  $("#cuim-edit-form").on("submit", function (e) {
    e.preventDefault();

    var formData = $(this).serialize();
    $.post(ajaxurl, formData + "&action=cuim_update_user", function (response) {
      if (response.success) {
        $("#cuim-edit-message").html(
          '<span style="color: green;">' + response.data + "</span>"
        );
        location.reload();
      } else {
        $("#cuim-edit-message").html(
          '<span style="color: red;">' + response.data + "</span>"
        );
      }
    });
  });

  let deleteUserId = 0;
  $(".cuim-delete").on("click", function () {
    let row = $(this).closest("tr");
    deleteUserId = row.data("user-id");
    $("#cuim-delete-email").text(row.find(".cuim-email").text());
    showModal("#cuim-delete-modal");
  });

  $("#cuim-confirm-delete").on("click", function () {
    $.post(
      cuim_ajax.ajax_url,
      {
        action: "cuim_delete_user",
        user_id: deleteUserId,
        security: cuim_ajax.nonce,
      },
      function (res) {
        if (res.success) {
          location.reload();
        } else {
          alert(res.data);
        }
      }
    );
  });

  // Save IP (Corrected)
  $("#cuim-ip-save").on("click", function () {
    const uid = $("#cuim-user-select").val();
    const ip = $("#cuim-ip-input").val();

    const ipData = {
      action: "cuim_save_ip",
      user_id: uid,
      ip: ip,
      security: cuim_ajax.nonce,
    };

    $.post(cuim_ajax.ajax_url, ipData, function (res) {
      $("#cuim-ip-status").text(res.data);
      if (res.success) loadIpList();
    });
  });
  //
  // function loadIpList() {
  //   $.post(
  //     cuim_ajax.ajax_url,
  //     {
  //       action: "cuim_get_ip_list",
  //       security: cuim_ajax.nonce,
  //     },
  //     function (res) {
  //       // console.log('IP LIST RESPONSE:', res); // Add this line
  //
  //       if (!res.success) {
  //         console.warn("IP list load failed:", res.data);
  //         return;
  //       }
  //       const tbody = $("#cuim-ip-list tbody").empty();
  //       res.data.forEach((row) => {
  //         if (!row.ip) return;
  //         tbody.append(`
  //               <tr data-user-id="${row.id}" data-user-email="${row.email}" data-ip="${row.ip}" class="${row.role}">
  //                   <td>${row.email}</td>
  //                   <td class="cuim-ip">${row.ip}</td>
  //                   <td style="text-align: center;">
  //                       <button class="cuim-edit-ip button sc_button_hover_slide_left"><i class="fas fa-pencil-alt"></i></button>
  //                       <button class="cuim-delete-ip button sc_button_hover_slide_left"> <i class="far fa-trash-alt"></i></button>
  //                   </td>
  //               </tr>
  //           `);
  //       });
  //     }
  //   );
  // }
  //
  // loadIpList();

  $(document).on("click", ".cuim-edit-ip", function () {
    const row = $(this).closest("tr");
    $("#cuim-edit-ip-user-id").val(row.data("user-id"));
    $("#cuim-edit-ip-input").val(row.data("ip"));
    showModal("#cuim-edit-ip-modal");
  });

  $("#cuim-edit-ip-form").on("submit", function (e) {
    e.preventDefault();
    $.post(
      cuim_ajax.ajax_url,
      $(this).serialize() + "&action=cuim_save_ip",
      function (res) {
        $("#cuim-edit-ip-message").text(res.data);
        if (res.success) {
          $(".cuim-modal").fadeOut();
          loadIpList();
        }
      }
    );
  });

  $(document).on("click", ".cuim-delete-ip", function () {
    const uid = $(this).closest("tr").data("user-id");
    $.post(
      cuim_ajax.ajax_url,
      {
        action: "cuim_delete_ip",
        user_id: uid,
        security: cuim_ajax.nonce,
      },
      function (res) {
        if (res.success) loadIpList();
      }
    );
  });

  jQuery(document).on("click", ".cuim-approve-user", function () {
    var userId = jQuery(this).data("user-id");
    var role = jQuery(this).data("requested-role");

    jQuery.post(
      ajaxurl,
      {
        action: "cuim_approve_user",
        security: cuim_ajax.nonce,
        user_id: userId,
        role: role,
      },
      function (response) {
        alert(response.data);
        if (response.success) location.reload();
      }
    );
  });

  $("#cuim-viewer-toggle").on("click", function () {
    const btn = $(this);
    btn.prop("disabled", true);
    $.post(
      cuim_ajax.ajax_url,
      {
        action: "cuim_toggle_viewer_mode",
      },
      function (response) {
        if (response.success) {
          const state = response.data === "1" ? "On" : "Off";
          btn.find("span").text(state);
          btn.toggleClass("active", response.data === "1");
          window.location.href = "/";
        } else {
          alert("❌ " + response.data);
        }
        btn.prop("disabled", false);
      }
    );
  });

  $(document).on("click", "[data-load-profile]", function (e) {
    e.preventDefault();
    $(".post_content.entry-content").html("<p>🔄 Loading profile...</p>");
    $.post(
      cuim_ajax.ajax_url,
      { action: "cuim_get_profile_html" },
      function (response) {
        if (response.success) {
          $(".post_content.entry-content").html(response.data.html);
        } else {
          $(".post_content.entry-content").html(
            '<p style="color:red;">❌ ' + response.data + "</p>"
          );
        }
      }
    );
  });

  $(document).on("submit", "#cuim-profile-page-form", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append("action", "cuim_save_profile");
    $.ajax({
      url: cuim_ajax.ajax_url,
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
    });
  });

  $(document).on("click", "#cuim-edit-name", function (e) {
    e.preventDefault();
    $("#cuim-edit-fields").slideToggle();
  });

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
  var totalItems = jQuery(".manage-user-template .custom-table-row").length;
  var totalPages = Math.ceil(totalItems / itemsPerPage);

  // If no rows exist, disable pagination and return
  if (totalItems === 0) {
    jQuery(".manage-user-template #pagination-demo").hide(); // Hide pagination if no items
    return;
  }

  jQuery(".manage-user-template #pagination-demo").twbsPagination({
    totalPages: totalPages,
    visiblePages: 3,
    onPageClick: function (event, page) {
      // Hide all rows first
      jQuery(".manage-user-template .custom-table-row").hide();

      // Show the rows for the current page
      jQuery(
        '.manage-user-template .custom-table-row[data-page="' + page + '"]'
      ).show();

      // Calculate the active items on the current page
      var totalActiveItems = jQuery(".custom-table-row.active").length;
      var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

      // Show/hide pagination links based on the active pages
      jQuery(".manage-user-template .pagination-ctn ul li.page-item")
        .nextAll()
        .not(".next")
        .show();

      jQuery(".manage-user-template .pagination-ctn ul li.page-item")
        .not(".prev, .next")
        .each(function () {
          var pageNumberss = parseInt(jQuery(this).text()); // Get the number of the page

          if (pageNumberss === totalActivePages && totalActivePages !== 0) {
            // Hide all <li> items that come after the last active page
            jQuery(this).nextAll().not(".next").hide();

            // Check if the "Next" button should be disabled
            var prevLi = jQuery(
              ".manage-user-template .pagination-ctn ul li.page-item.active"
            ).next();

            // Disable or enable the "Next" button based on the visibility of the next page
            if (prevLi.is(":hidden")) {
              jQuery(
                ".manage-user-template .pagination-ctn ul li.next"
              ).addClass("disabled"); // Disable Next button
            } else {
              jQuery(
                ".manage-user-template .pagination-ctn ul li.next"
              ).removeClass("disabled"); // Enable Next button
            }
          }
        });
    },
  });

  // Loop through each row and assign a page number based on its index
  jQuery(".manage-user-template .custom-table-row").each(function (index) {
    var page = Math.floor(index / itemsPerPage) + 1;
    jQuery(this).attr("data-page", page); // Assign page data attribute

    // Initially show or hide based on the page
    if (page === 1) {
      jQuery(this).show();
    } else {
      jQuery(this).hide();
    }
  });

  // $(".toggle-password").on("click", function (e) {
  //   e.preventDefault();
  //   alert("dff");
  //   var passwordField = $(this).siblings(
  //     'input[type="password"], input[type="text"]'
  //   ); // Get the password input inside the same .password-field container

  //   // Toggle password visibility
  //   var fieldType =
  //     passwordField.attr("type") === "password" ? "text" : "password";
  //   passwordField.attr("type", fieldType); // Toggle the password visibility

  //   // Toggle the button class and icon
  //   $(this).toggleClass("show-pass");

  //   // Optionally change the icon or text on the button based on visibility
  // });

  // setTimeout(function () {
  //   // Clear the date range input field
  //   $('input[name="daterange"]').val("");
  // }, 2000); // 3000 milliseconds = 3 seconds

  // // Initialize the date range picker with max 30 days selection
  // $('input[name="daterange"]').daterangepicker({
  //   opens: "right", // Position the calendar
  //   locale: {
  //     format: "YYYY/MM/DD", // Specify the date format
  //   },
  //   maxSpan: {
  //     days: 30, // Limit the date range selection to a maximum of 30 days
  //   },
  // });

  // // Handle the cancel or clear action
  // $('input[name="daterange"]').on(
  //   "cancel.daterangepicker",
  //   function (ev, picker) {
  //     $(this).val(""); // Reset the input field to empty when the user cancels or clears the date range
  //   }
  // );
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
  // $(document).on("change", "#upload-file-button", function () {
  //   console.log("File input changed");
  //   var file = this.files && this.files[0];
  //   if (!file) {
  //     console.warn("No file selected");
  //     return;
  //   }

  //   // Spec: JPG only, ≤ 2 MB
  //   if (file.size > 2 * 1024 * 1024) {
  //     alert("Max size 2 MB");
  //     this.value = "";
  //     return;
  //   }

  //   // MIME type check for JPG only (ensures it's a JPEG image)
  //   var isJpg =
  //     file.type === "image/jpeg" ||
  //     file.name.toLowerCase().endsWith(".jpg") ||
  //     file.name.toLowerCase().endsWith(".jpeg");
  //   if (!isJpg) {
  //     alert("Only JPG images are allowed");
  //     this.value = "";
  //     return;
  //   }

  //   var { $modal, imgEl } = els();
  //   if (!$modal.length || !imgEl) {
  //     console.error("Cropper modal/image elements not found in DOM.");
  //     return;
  //   }

  //   var reader = new FileReader();
  //   reader.onerror = function (e) {
  //     console.error("FileReader error", e);
  //     alert("Could not read the image.");
  //   };
  //   reader.onload = function (e) {
  //     // Show modal and start Cropper
  //     imgEl.src = e.target.result;
  //     $modal.css("display", "flex");

  //     try {
  //       if (cropper) cropper.destroy();
  //     } catch (e) {}
  //     cropper = new Cropper(imgEl, {
  //       aspectRatio: 1,
  //       viewMode: 1,
  //       autoCropArea: 1,
  //     });
  //   };
  //   reader.readAsDataURL(file);
  // });
  $(document).on("change", "#upload-file-button", function () {
    console.log("File input changed");
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

    // MIME type check for JPG only
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
      // Create temporary image to check dimensions
      var tempImg = new Image();
      tempImg.onload = function () {
        if (tempImg.width > 128 || tempImg.height > 128) {
          alert("Image dimensions cannot exceed 128 × 128 pixels.");
          $("#upload-file-button").val(""); // Reset file input
          return;
        }

        // Show modal and start Cropper
        imgEl.src = e.target.result;
        $modal.css("display", "flex");

        try {
          if (cropper) cropper.destroy();
        } catch (err) {}

        cropper = new Cropper(imgEl, {
          aspectRatio: 1,
          viewMode: 1,
          autoCropArea: 1,
        });
      };
      tempImg.src = e.target.result;
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

  $(".add-button").on("click", function () {
    $(".add-manage-ip-form").addClass("active");
  });

  // Close popup on cross icon
  $(".manage-ip-cross-icon").on("click", function () {
    $(".add-manage-ip-form").removeClass("active");
  });

  $(".manage-ip-edit-button").on("click", function () {
    $(this).siblings(".edit-manage-ip-form").addClass("active");
  });

  // Close popup on cross icon
  $(".manage-ip-cross-icon").on("click", function () {
    $(".edit-manage-ip-form").removeClass("active");
  });

  $(".manage-ip-form-buttons .cancel-button").on("click", function () {
    $(this).siblings(".cancel-form-confirmation").addClass("active");
  });

  // Close popup on cross icon
  $(".popup-form-cross-icon").on("click", function () {
    $(".cancel-form-confirmation").removeClass("active");
  });

  $(".delete-user-button").on("click", function () {
    $(this).siblings(".delete-popup").addClass("active");
  });

  $("#edit-ip-btn").on("click", function (e) {
    e.preventDefault();
    $(".confirm-submit-popup").addClass("active");
  });

  // Close popup on cross icon
  $(".popup-form-cross-icon, .delete-manage-ip .no-cancel").on(
    "click",
    function () {
      $(".delete-popup").removeClass("active");
    }
  );

  // Close popup on cross icon
  $(".no-form-cancel").on("click", function () {
    $(".cancel-form-confirmation").removeClass("active");
  });

  // Close popup on cross icon
  $(".submit-cross-icon,.no-confirm-submit").on("click", function () {
    $(".confirm-submit-popup").removeClass("active");
  });

  $(".yes-cancel").on("click", function () {
    $(".cancel-form-confirmation").removeClass("active");
    $(".add-manage-ip-form").removeClass("active");
    $(".edit-manage-ip-form").removeClass("active");
  });

  $(".notification-button").on("click", function () {
    $(".notification-popup").toggleClass("active");
  });
});
