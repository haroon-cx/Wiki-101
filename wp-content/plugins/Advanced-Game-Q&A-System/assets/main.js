// assets/main.js

jQuery(document).ready(function ($) {
  // Open the report popup when clicking the Report button
  $(".report-button").click(function () {
    var questionId = $(this).data("question-id");
    $("#report-question-popup-" + questionId).fadeIn();
  });
  // Check if the current page URL matches the desired URLs
  if (window.location.href === "http://wiki.local/complatint/") {
    // Add the class 'current_page_item' to the menu item
    $("li.menu-item-54").addClass("current_page_item");
  }

  /* Api Card slide effect script */

  $(".api-toggle-header").on("click", function (e) {
    e.stopPropagation(); // Prevent bubbling to document

    const $card = $(this).closest(".api-card-container-box"); // Get the closest card
    const $details = $(this).siblings(".api-details"); // Get the details section

    // Hide all other .api-details
    $(".api-details").not($details).stop(true, true).slideUp();

    // Remove .active from all other cards
    $(".api-card-container-box").not($card).removeClass("active");

    // Toggle active state on the clicked card
    $card.toggleClass("active");

    // Toggle the clicked details section
    $details.stop(true, true).slideToggle();
  });

  // Prevent clicks inside .api-details from bubbling to document
  $(".api-details").on("click", function (e) {
    e.stopPropagation();
  });

  // Click anywhere else in the document closes all .api-details and removes active classes
  $(document).on("click", function () {
    $(".api-details").stop(true, true).slideUp();
    $(".api-card-container-box").removeClass("active");
  });

  const $logoField = $(".agqa-popup-form-field.required");
  const $dropArea = $logoField.find(".custom-upload-area");
  const $browseLink = $dropArea.find(".browse-link");
  const $filePreview = $logoField.find(".file-preview");
  const $fileInput = $logoField.find("#upload-logo-drag");
  const $hiddenInput = $logoField.find("input[name='upload-file']");
  const MAX_SIZE = 2 * 1024 * 1024; // 2MB

  // Click to open file selector
  $browseLink.on("click", function () {
    $fileInput.trigger("click");
  });

  // Drag events
  $browseLink.on("dragover", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $dropArea.addClass("dragging");
    $browseLink.text("Drop here");
  });

  $browseLink.on("dragleave", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $dropArea.removeClass("dragging");
    $browseLink.text("Upload Logo");
  });

  $dropArea.on("drop", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $dropArea.removeClass("dragging");
    $browseLink.text("Upload Logo");

    const file = e.originalEvent.dataTransfer.files[0];
    handleFile(file);
  });

  // // File selection
  $fileInput.on("change", function () {
    handleFile(this.files[0]);
  });

  // File validation + base64 preview
  function handleFile(file) {
    if (!file) return;

    if (file.type !== "image/png") {
      alert("Only PNG format is supported.");
      return;
    }

    if (file.size > MAX_SIZE) {
      alert("File size must be 2MB or less.");
      return;
    }

    // Set file to original input
    const fileInput = document.getElementById("upload-logo-drag");
    if (fileInput) {
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      fileInput.files = dataTransfer.files;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = function (e) {
      $filePreview.html(`<span>${file.name}</span>`).show();
      $hiddenInput.val(e.target.result);
    };
    reader.readAsDataURL(file);
  }

  /*  Append Dropdown Icon Script (Usama)  */

  const $menuItems = $(
    ".sidebar .widget ul#menu-main-menu .menu-item-has-children"
  );

  // Auto-open if it has current item
  $menuItems.each(function () {
    if ($(this).find(".current-menu-item, .current_page_item").length) {
      openMenu($(this), false); // No animation
      $(this).data("persistent", true); // Mark as persistent (due to current item)
    }
  });

  // Hover functionality (desktop)
  $menuItems.hover(
    function () {
      const $li = $(this);
      if (!$li.hasClass("active")) openMenu($li);
    },
    function () {
      const $li = $(this);
      if (!$li.data("persistent")) closeMenu($li); // Don't close if marked persistent
    }
  );

  // Click toggle (mobile or desktop)
  $menuItems.children("a").on("click", function (e) {
    const $li = $(this).parent();
    // Optional: prevent link navigation
    // e.preventDefault();
    if ($li.hasClass("active")) {
      $li.data("persistent", false); // Clear persistent flag
      closeMenu($li);
    } else {
      openMenu($li);
    }
  });

  function openMenu($li, animate = true) {
    $li.addClass("active");
    const $submenu = $li.children(".sub-menu");

    if (animate) {
      $submenu.stop(true, true).slideDown(300);
      $submenu
        .children("li")
        .css("opacity", 0)
        .each(function (i) {
          $(this).delay(200).animate({ opacity: 1 }, 300);
        });
    } else {
      $submenu.show().children("li").css("opacity", 1);
    }
  }

  function closeMenu($li) {
    $li
      .removeClass("active")
      .children(".sub-menu")
      .stop(true, true)
      .slideUp(300);
  }

  function setupMenuBehavior() {
    const $menuItems = $(".menu > li.has-submenu");

    // Unbind previous events to avoid duplicates on resize
    $menuItems.off("mouseenter mouseleave click");
    $(document).off("click.mobile");
    $(".menu").off("click");

    const isDesktop = window.matchMedia("(min-width: 1025px)").matches;

    if (isDesktop) {
      // Desktop: Hover behavior
      $menuItems
        .on("mouseenter", function () {
          openMenu($(this));
        })
        .on("mouseleave", function () {
          closeMenu($(this));
        });
    } else {
      // Mobile: Click toggle behavior
      $menuItems.on("click", function (e) {
        e.stopPropagation();

        const $clickedItem = $(this);

        if ($clickedItem.hasClass("active")) {
          closeMenu($clickedItem);
        } else {
          // Optional: Close other open menus
          $menuItems.not($clickedItem).each(function () {
            closeMenu($(this));
          });

          openMenu($clickedItem);
        }
      });

      // Close menus when clicking outside
      $(document).on("click.mobile", function () {
        $menuItems.each(function () {
          closeMenu($(this));
        });
      });

      // Prevent document click from firing when clicking inside menu
      $(".menu").on("click", function (e) {
        e.stopPropagation();
      });
    }
  }

  $(".hamburger-menu").on("click", function () {
    const $hamburger = $(this);
    const $widget = $(".widget_area");

    // Toggle active class on hamburger
    $hamburger.toggleClass("active");

    // Toggle active + slide animation on widget_area
    if ($widget.hasClass("active")) {
      $widget.removeClass("active").stop(true, true).slideUp(300);
    } else {
      $widget.addClass("active").stop(true, true).slideDown(300);
    }
  });

  // Initialize on page load
  $(document).ready(function () {
    setupMenuBehavior();
  });

  // Reapply on window resize
  $(window).on("resize", function () {
    setupMenuBehavior();
  });

  $(".widget_nav_menu .menu-item-has-children").append(
    '<span class="submenu-toggle"></span>'
  );

  // Close popup
  $(".pdf-close, .pdf-modal-overlay").on("click", function (e) {
    if ($(e.target).is(".pdf-modal-overlay") || $(e.target).is(".pdf-close")) {
      $(".pdf-modal-overlay").fadeOut(300, function () {
        $(".pdf-frame").attr("src", "");
      });
    }
  });

  $(".cancel-button").on("click", function (e) {
    e.preventDefault(); // prevent default button behavior if needed
    $(".agqa-popup-form").removeClass("active");
  });

  // Accordion Toggle + Arrow Rotation Logic
  $(".section-header").each(function () {
    const $header = $(this);
    const $section = $header.parent();
    const $body = $section.find(".section-body").first();
    const $toggleBtn = $header.find(".agqa-status-toggle").first();
    const $arrowImg = $toggleBtn.find(".custom-chevron").first();

    // Click event
    $header.on("click", function () {
      const isOpen = $section.hasClass("open");

      if (isOpen) {
        $section.removeClass("open");
        $header.attr("aria-expanded", "false");
        $body.stop(true, true).slideUp(300);
        if ($arrowImg.length) $arrowImg.css("transform", "rotate(0deg)");
      } else {
        $section.addClass("open");
        $header.attr("aria-expanded", "true");
        $body.stop(true, true).slideDown(300);
        if ($arrowImg.length) $arrowImg.css("transform", "rotate(180deg)");
      }
    });

    // Keyboard toggle
    $header.on("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        $header.click();
      }
    });
  });

  /* New Categories Popup script */

  // Open popup
  $(
    ".agqa-popup-form-ctn .add-category-button,.api-report-button,.report-button"
  ).on("click", function (e) {
    e.stopPropagation();
    $(".agqa-popup-form").addClass("active");
  });

  // Close popup on cross icon
  $(".popup-form-cross-icon").on("click", function (e) {
    e.stopPropagation();
    $(".agqa-popup-form").removeClass("active");
  });

  // Close when clicking outside popup inner
  $(document).on("click", function (e) {
    if (!$(e.target).closest(".agqa-popup-form-inner").length) {
      $(".agqa-popup-form").removeClass("active");
    }
  });

  // Close the report popup when clicking the Cancel button
  $(".cancel-button").click(function () {
    $(this).closest(".report-popup").fadeOut();
  });

  // 👍 Like Button (Only updates like_count in the database)
  $(".like-btn").on("click", function () {
    const answerId = $(this).data("answer-id");

    // Toggle the 'liked' state for the like button
    $(this).toggleClass("liked");

    // Send AJAX request to increment the like count in the database
    $.post(
      "/wp-admin/admin-ajax.php",
      {
        action: "agqa_like_answer",
        answer_id: answerId,
        nonce: agqa_ajax.nonce,
      },
      function (data) {
        if (data.success) {
          // Like is successfully incremented in the database, no need to update frontend count
          console.log("Like added");
        }
      },
      "json"
    );
  });

  // const $tabs = $(".tab");
  // const $panels = $("[role='tabpanel']");

  // $tabs.on("click", function () {
  //   const $clickedTab = $(this);
  //   const panelId = $clickedTab.attr("aria-controls");
  //   const $panelToShow = $("#" + panelId);

  //   // Deactivate all tabs
  //   $tabs.removeClass("active")
  //     .attr("aria-selected", "false")
  //     .attr("tabindex", "-1");

  //   // Activate clicked tab
  //   $clickedTab.addClass("active")
  //     .attr("aria-selected", "true")
  //     .attr("tabindex", "0");

  //   // Hide all panels with fadeOut
  //   $panels.each(function () {
  //     const $panel = $(this);
  //     if (!$panel.attr("hidden")) {
  //       $panel.fadeOut(300, function () {
  //         $panel.attr("hidden", true);
  //       });
  //     } else {
  //       $panel.hide();
  //     }
  //   });

  //   // Show selected panel with fadeIn and prevent scroll
  //   setTimeout(function () {
  //     $panelToShow
  //       .attr("hidden", false)
  //       .hide()
  //       .fadeIn(400)
  //       .focus({ preventScroll: true }); // ✅ Prevent scroll on focus
  //   }, 300);
  // });

  // // Keyboard navigation for tabs
  // $tabs.on("keydown", function (e) {
  //   const index = $tabs.index(this);
  //   let $nextTab;

  //   if (e.key === "ArrowRight") {
  //     e.preventDefault();
  //     $nextTab = $tabs.eq((index + 1) % $tabs.length);
  //     $nextTab.focus();
  //   } else if (e.key === "ArrowLeft") {
  //     e.preventDefault();
  //     $nextTab = $tabs.eq((index - 1 + $tabs.length) % $tabs.length);
  //     $nextTab.focus();
  //   }
  // });

  // 👎 Dislike Button (Only updates dislike_count in the database)
  $(".dislike-btn").on("click", function () {
    const answerId = $(this).data("answer-id");

    // Toggle the 'disliked' state for the dislike button
    $(this).toggleClass("disliked");

    // Send AJAX request to increment the dislike count in the database
    $.post(
      "/wp-admin/admin-ajax.php",
      {
        action: "agqa_dislike_answer",
        answer_id: answerId,
        nonce: agqa_ajax.nonce,
      },
      function (data) {
        if (data.success) {
          // Dislike is successfully incremented in the database
          console.log("Dislike added");
        }
      },
      "json"
    );
  });

  // 📋 Copy Button with Clipboard API + Fallback
  $(document).on("click", ".copy-btn", function () {
    const button = this;

    // ✅ Support both `data-text` (preferred) and `data` (fallback)
    const textToCopy =
      $(this).data("text") || $(this).attr("data") || $(this).text().trim();

    if (!textToCopy) {
      alert("No text to copy.");
      return;
    }

    // Trigger copy action
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(textToCopy).then(() => {
        button.innerHTML = "✅ Copied!";
        setTimeout(() => {
          button.innerHTML = '<i class="fa-regular fa-clone"></i>';
        }, 3000);
      });
    } else {
      // Old browser fallback
      const textarea = document.createElement("textarea");
      textarea.value = textToCopy;
      textarea.style.position = "fixed";
      textarea.style.left = "-9999px";
      document.body.appendChild(textarea);
      textarea.focus();
      textarea.select();

      try {
        document.execCommand("copy");
        button.innerHTML = "✅ Copied!";
        setTimeout(() => {
          button.innerHTML = '<i class="fa-regular fa-clone"></i>';
        }, 3000);
      } catch {
        alert("Copy failed");
      }

      document.body.removeChild(textarea);
    }
  });

  // === ⛔ REMOVE THIS OLD FUNCTION === //
  // function openAgqaPopup(answerId) { ... }

  // ✅ Use this NEW version
  function openPopup(type, answerId) {
    if (type === "report") {
      $("#report_answer_id").val(answerId);
      $("#agqa-report-popup").fadeIn();
    } else if (type === "dislike") {
      $("#dislike_answer_id").val(answerId);
      $("#agqa-dislike-popup").fadeIn();
    }
  }

  // ✅ Close popups on overlay or close button
  $(".popup-close, .popup-overlay, .agqa-popup-close, .agqa-popup-overlay").on(
    "click",
    function () {
      $(".agqa-popup").fadeOut();
    }
  );

  // ✅ Show/hide "Other" field for dislike form
  $("#dislike-other-checkbox").on("change", function () {
    $("#dislike-other-text").slideToggle(this.checked);
  });

  // ✅ Bind report button
  $(".report-btn").on("click", function () {
    const answerId = $(this).data("answer-id");
    openPopup("report", answerId);
  });

  // ✅ Bind dislike button — AJAX + popup
  $(".dislike-btn").on("click", function () {
    const answerId = $(this).data("answer-id");
    $(this).toggleClass("disliked");

    // Update in DB
    $.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_dislike_answer",
        answer_id: answerId,
        nonce: agqa_ajax.nonce,
      },
      function (data) {
        if (data.success) console.log("Dislike recorded");
      },
      "json"
    );

    openPopup("dislike", answerId);
  });

  // ✅ Submit Report Form via AJAX
  $("#agqa-report-form").on("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append("action", "agqa_submit_feedback");
    formData.append("type", "report");
    formData.append("nonce", agqa_ajax.nonce);

    $.ajax({
      url: agqa_ajax.ajax_url,
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (resp) {
        if (resp.success) {
          alert(resp.data.message);
          $("#agqa-report-popup").fadeOut();
          $("#agqa-report-form")[0].reset();
        } else {
          alert(resp.data.message || "Failed");
        }
      },
    });
  });

  // ✅ Submit Dislike Form via AJAX
  $("#agqa-dislike-form").on("submit", function (e) {
    e.preventDefault();

    const reasons = [];
    $('#agqa-dislike-form input[name="reasons[]"]:checked').each(function () {
      reasons.push($(this).val());
    });
    const otherText = $("#dislike-other-text").val();

    const data = {
      action: "agqa_submit_feedback",
      nonce: agqa_ajax.nonce,
      type: "dislike",
      answer_id: $("#dislike_answer_id").val(),
      reason: reasons.join(", ") + (otherText ? ` | Other: ${otherText}` : ""),
      details: otherText,
    };

    $.post(
      agqa_ajax.ajax_url,
      data,
      function (resp) {
        if (resp.success) {
          alert(resp.data.message);
          $("#agqa-dislike-popup").fadeOut();
          $("#agqa-dislike-form")[0].reset();
        } else {
          alert(resp.data.message || "Failed");
        }
      },
      "json"
    );
  });
  $(".custom-checkbox").removeClass("checkbox_label");
  const nonce = agqa_ajax.nonce;
  function fetchCategories() {
    $.post(
      agqa_ajax.ajax_url,
      { action: "agqa_get_categories", nonce },
      function (res) {
        if (res.success) {
          const catSelect = $("#agqa-admin-cat-select")
            .empty()
            .append('<option value="">Select Category</option>');
          res.data.forEach((c) => {
            catSelect.append(`<option value="${c.id}">${c.name}</option>`);
          });
        }
      }
    );
  }

  function truncateWords(text, limit = 20) {
    const words = text.split(/\s+/);
    if (words.length > limit) {
      return words.slice(0, limit).join(" ") + "...";
    }
    return text;
  }

  function fetchPosts() {
    $.post(
      agqa_ajax.ajax_url,
      { action: "agqa_get_posts", nonce },
      function (res) {
        if (res.success) {
          const list = $("#agqa-post-list").empty();
          const postSelect = $("#agqa-admin-post-select")
            .empty()
            .append('<option value="">Select Post</option>');
          res.data.forEach((p) => {
            const truncatedContent = truncateWords(p.content, 20);
            const box = $(`<div class="agqa-post-box">
                       <div class="agqa-post-title">
                       <h4>${p.title}</h4>
                        <p>${truncatedContent}</p>
                        </div>
                        <div class="agqa-post-image">
                        <img src="${p.image_url}" alt="">
                        </div>
                    </div>`);
            box.click(() => {
              window.location.href = `/post/?id=${p.id}`;
            });
            list.append(box);
            postSelect.append(`<option value="${p.id}">${p.title}</option>`);
          });
        }
      }
    );
  }

  function fetchComplaints() {
    if (!agqa_ajax.is_admin) return;
    $.post(
      agqa_ajax.ajax_url,
      { action: "agqa_get_complaints", nonce },
      function (res) {
        const wrap = $("#agqa-admin-complaints").empty();
        if (res.success) {
          res.data.forEach((c) => {
            const card = $(`<div class="agqa-complaint-box">
                        <p><strong>Answer:</strong> ${c.answer_text}</p>
                        <p><strong>Reason:</strong> ${c.reason}</p>
                        <textarea placeholder="Admin Note"></textarea>
                        <button class="approve-btn">✅ Approve</button>
                        <button class="reject-btn">❌ Reject</button>
                    </div>`);
            card
              .find(".approve-btn")
              .click(() =>
                moderateComplaint(c.id, "approved", card.find("textarea").val())
              );
            card
              .find(".reject-btn")
              .click(() =>
                moderateComplaint(c.id, "rejected", card.find("textarea").val())
              );
            wrap.append(card);
          });
        }
      }
    );
  }

  function moderateComplaint(id, decision, note) {
    $.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_moderate_complaint",
        complaint_id: id,
        decision,
        note,
        nonce,
      },
      function (res) {
        if (res.success) {
          alert("Complaint processed");
          fetchComplaints();
        }
      }
    );
  }

  let agqaSearchXHR = null;

  $("#agqa-search-input").on("input", function () {
    const term = $(this).val().trim();

    // Agar input 2 se chhota ho to turant results clear kar do aur AJAX cancel karo
    if (term.length < 1) {
      if (agqaSearchXHR) {
        agqaSearchXHR.abort();
        agqaSearchXHR = null;
      }
      $("#agqa-search-results").empty();
      return;
    }

    // Purana AJAX request cancel kar do agar chal raha ho
    if (agqaSearchXHR) {
      agqaSearchXHR.abort();
    }

    agqaSearchXHR = $.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_search_all",
        term: term,
        nonce: agqa_ajax.nonce,
      },
      function (res) {
        const box = $("#agqa-search-results").empty();
        if (res.success && res.data.length > 0) {
          res.data.forEach((row) => {
            box.append(`<div class="agqa-search-result" data-question-id="${
              row.question_id
            }">
                    <strong>${row.type.toUpperCase()}</strong> in <em>${
              row.post_title
            }</em>:<br>
                    ${row.content}
                </div>`);
          });
        } else {
          box.append(
            '<div class="agqa-search-no-results">No results found.</div>'
          );
        }
      }
    ).always(function () {
      agqaSearchXHR = null;
    });
  });

  $("#agqa-search-results").on("click", ".agqa-search-result", function () {
    const qid = $(this).data("question-id");
    if (qid) {
      window.location.href = "/question/?id=" + qid;
    }
  });

  $("#agqa-submit-answer").click(function () {
    const question_id = $("#agqa-answer-form").data("question-id");
    const content = $("#agqa-answer-text").val();
    $.post(
      agqa_ajax.ajax_url,
      { action: "agqa_submit_answer", question_id, content, nonce },
      function (res) {
        if (res.success) {
          $("#agqa-answer-text").val("");
          loadAnswers(question_id);
        }
      }
    );
  });

  $("#agqa-submit-complaint").click(function () {
    const answer_id = $("#agqa-complaint-answer-list").val();
    const reason = $("input[name='complaint_reason']:checked").val();
    const note = $('textarea[name="note"]').val();
    $.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_submit_complaint",
        answer_id,
        reason,
        note,
        nonce,
      },
      function (res) {
        if (res.success) {
          alert("Complaint submitted");
          $("#agqa-complaint-reason").val("");
        } else {
          alert("Error in submission");
        }
      }
    );
  });

  $(document).on("click", ".report-question-button", function () {
    const question_id = $(this).data("question-id");
    $("#agqa-complaint-question-list").val(question_id);
    $("#report-question-popup-" + question_id).fadeIn();
  });

  $(document).on("click", "#submit-report-question", function (e) {
    e.preventDefault();

    const question_id = $("#agqa-complaint-question-list").val();
    const reason = $("input[name='complaint_reason']:checked").val();
    const note = $("textarea[name='note']").val();
    // Check if all required fields are filled
    if (!question_id || !reason) {
      alert("Please select a reason and provide a valid question ID.");
      return;
    }

    // Make the AJAX request to submit the complaint
    $.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_submit_question_complaint",
        question_id: question_id,
        reason: reason,
        note: note,
        nonce: agqa_ajax.nonce,
      },
      function (res) {
        if (res.success) {
          alert("Complaint submitted successfully");
          $("#report-question-popup-" + question_id).hide();
        } else {
          alert("Error in submission");
        }
      }
    );
  });

  $("#agqa-admin-add-cat").click(function () {
    const name = $("#agqa-admin-cat-name").val();
    $.post(
      agqa_ajax.ajax_url,
      { action: "agqa_add_category", name, nonce },
      function () {
        $("#agqa-admin-cat-name").val("");
        fetchCategories();
      }
    );
  });

  $("#agqa-admin-add-post").click(function () {
    const category_id = $("#agqa-admin-cat-select").val();
    const title = $("#agqa-admin-post-title").val().trim();
    const content = $("#agqa-admin-post-content").val().trim();
    const image_url = $("#agqa-admin-post-image").val().trim();

    // Validation check for empty fields
    if (!title || !content || !image_url) {
      alert("Please fill in all fields before adding the game.");
      return;
    }

    $.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_add_post",
        category_id,
        title,
        content,
        image_url,
        nonce,
      },
      function (res) {
        if (res.data.status === "success") {
          alert(res.data.message);
          $("div#agqa-add-game-modal").hide();
        }
        $(
          "#agqa-admin-post-title, #agqa-admin-post-content, #agqa-admin-post-image"
        ).val("");
        fetchPosts();
      }
    );
  });

  $("#agqa-admin-add-question").click(function () {
    const post_id = $("#agqa-admin-post-select").val();
    const question = $("#agqa-admin-question").val();
    $.post(
      agqa_ajax.ajax_url,
      { action: "agqa_add_question", post_id, question, nonce },
      function () {
        $("#agqa-admin-question").val("");
      }
    );
  });

  $("#agqa-admin-post-image").on("click", function (e) {
    e.preventDefault();

    let image_frame = wp.media({
      title: "Select or Upload Image",
      button: {
        text: "Use this image",
      },
      multiple: false,
    });

    image_frame.on("select", function () {
      let attachment = image_frame.state().get("selection").first().toJSON();
      $("#agqa-admin-post-image").val(attachment.url);
    });

    image_frame.open();
  });

  $(document).on("click", ".agqa-edit-game-btn", function () {
    const gameContainer = $(this).closest(".agqa-game-container");
    editGame(gameContainer); // pass container instead of button
  });

  $(document).on("click", ".agqa-hide-game-btn", function () {
    const gameContainer = $(this).closest(".agqa-game-container");
    toggleGameVisibility(gameContainer, "hide");
  });

  $(document).on("click", ".agqa-show-game-btn", function () {
    const gameContainer = $(this).closest(".agqa-game-container");
    toggleGameVisibility(gameContainer, "show");
  });

  $("#agqa-save-game-button").on("click", function () {
    const title = $("#agqa-edit-game-title").val();
    const image = $("#agqa-admin-post-image").val();
    const description = $("#agqa-edit-game-description").val();
    const urlParams = new URLSearchParams(window.location.search);
    const game_id = urlParams.get("id");

    $.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_edit_game_full",
        nonce: agqa_ajax.nonce,
        game_id: game_id,
        new_title: title,
        new_image: image,
        new_description: description,
      },
      function (res) {
        if (res.success) {
          alert("✅ Game updated successfully!");
          location.reload();
        } else {
          alert("❌ Failed to update game. Please try again.");
        }
      }
    );
  });

  function editGame(gameContainer) {
    $("#agqa-edit-game-modal").show();
  }

  function toggleGameVisibility(gameContainer, status) {
    const urlParams = new URLSearchParams(window.location.search);
    const game_id = urlParams.get("id");

    jQuery.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_toggle_game_visibility",
        nonce: agqa_ajax.nonce,
        game_id: game_id,
        status: status,
      },
      function (res) {
        if (res.success) {
          alert(
            "Game is now " + (status === "hide" ? "hidden" : "visible") + "."
          );
          location.reload(); // Refresh page to show updated content
        } else {
          alert("Failed to update visibility.");
        }
      }
    );
  }

  jQuery(document).on("change", ".agqa-status-dropdown", function () {
    const select = jQuery(this);
    const newStatus = select.val();
    const gameId = select.data("game-id");

    jQuery.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_update_status",
        nonce: agqa_ajax.nonce,
        game_id: gameId,
        status: newStatus,
      },
      function (res) {
        if (res.success) {
          alert("Status updated successfully!");
          window.location.href = `/post/?id=${gameId}`;
        } else {
          alert("Failed to update status.");
        }
      }
    );
  });

  // Open edit modal with current question text
  jQuery(document).on("click", ".agqa-edit-question-btn", function () {
    const questionId = jQuery(this).data("question-id");
    const questionText = jQuery(this).closest("li").find("a").text().trim();

    jQuery("#agqa-edit-question-id").val(questionId);
    jQuery("#agqa-edit-question-text").val(questionText);
    jQuery("#agqa-edit-question-modal").show();
  });

  // Save updated question via AJAX
  jQuery("#agqa-save-question-btn").on("click", function () {
    const questionId = jQuery("#agqa-edit-question-id").val();
    const newQuestion = jQuery("#agqa-edit-question-text").val().trim();

    if (!newQuestion) {
      alert("Question text cannot be empty.");
      return;
    }

    jQuery.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_edit_question",
        nonce: agqa_ajax.nonce,
        question_id: questionId,
        new_question: newQuestion,
      },
      function (res) {
        if (res.success) {
          alert("Question updated successfully.");
          location.reload();
        } else {
          alert("Failed to update question.");
        }
      }
    );
  });

  // Toggle status menu visibility
  jQuery(document).on("click", ".agqa-status-toggle", function (e) {
    e.stopPropagation();
    const menu = jQuery(this).siblings(".agqa-status-menu");
    jQuery(".agqa-status-menu").not(menu).hide();
    menu.toggle();
  });

  // Hide status menu when clicking outside
  jQuery(document).on("click", function () {
    jQuery(".agqa-status-menu").hide();
  });

  // Handle status item click
  jQuery(document).on("click", ".agqa-status-item", function () {
    const status = jQuery(this).data("status");
    const questionId = jQuery(this).parent().data("question-id");

    // AJAX call to update status
    jQuery.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_update_question_status",
        nonce: agqa_ajax.nonce,
        question_id: questionId,
        status: status,
      },
      function (res) {
        if (res.success) {
          alert(`Status updated to ${status}`);
          location.reload();
        } else {
          alert("Failed to update status");
        }
      }
    );

    jQuery(this).parent().hide();
  });

  $(document).on("click", ".agqa-toggle-visibility-btn", function () {
    const btn = $(this);
    const questionId = btn.data("question-id");
    const currentTitle = btn.attr("title").toLowerCase();
    const action = currentTitle.includes("hide") ? "hide" : "show";

    $.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_toggle_question_visibility",
        nonce: agqa_ajax.nonce,
        question_id: questionId,
        status: action,
      },
      function (res) {
        if (res.success) {
          alert(`Question is now ${action === "hide" ? "hidden" : "visible"}.`);
          location.reload();
        } else {
          alert("Failed to update visibility.");
        }
      }
    );
  });

  // Toggle dropdown menu on button click
  $(document).on("click", ".agqa-dropdown-toggle", function (e) {
    e.stopPropagation();
    const menu = $(this).siblings(".agqa-dropdown-menu");
    $(".agqa-dropdown-menu").not(menu).hide();
    menu.toggle();
  });

  // Close dropdown if clicking outside
  $(document).on("click", function () {
    $(".agqa-dropdown-menu").hide();
  });

  $(document).on("click", ".agqa-dropdown-item", function () {
    const action = $(this).data("action"); // dropdown item ka action
    const answerId = $(this).parent().find('input[name="answer_id"]').val();
    if (!action || !answerId) {
      alert("Invalid action or answer ID");
      return;
    }

    $.post(
      agqa_ajax.ajax_url,
      {
        action: "agqa_dropdown_action",
        nonce: agqa_ajax.nonce,
        answer_id: answerId,
        dropdown_action: action,
      },
      function (res) {
        if (res.success) {
          alert('Action "' + action + '" completed successfully!');
          location.reload();
        } else {
          alert("Action failed: " + (res.data || "Unknown error"));
        }
      }
    );
  });

  fetchCategories();
  fetchPosts();
  fetchComplaints();

  /**
   * Game Category Script Start
   */

  /**
   * Revenu Table Script
   */
  $("#insert_provider_form").submit(function (e) {
    e.preventDefault();
    const $form = $(this);
    var formDataBmodel = $form.serialize();
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
        const fieldName = field.attr("name") || field.prev("label").text();
        // alert(fieldName + " cannot be empty or just spaces.");
        return false; // Exit the loop and stop further validation
      }
      if (!field.val()) {
        // If the field is empty
        isValid = false;
        const fieldName = field.attr("name") || field.prev("label").text();
        return false;
      }
    });
    if (!isValid) {
      return;
    }
    // Split the serialized string by '&' and loop through each pair
    formDataBmodel.split("&").forEach(function (pair) {
      var [key, value] = pair.split("=");
      formDataObject[key] = decodeURIComponent(value);
    });

    // console.log($('input[type="file"]')[0].files);
    var formDataImage = new FormData();
    formDataImage.append("action", "ddmu_handle_upload");
    formDataImage.append("nonce", agqa_ajax.nonce);
    formDataImage.append("file", $('input[type="file"]')[0].files[0]);

    $.ajax({
      url: agqa_ajax.ajax_url,
      type: "POST",
      data: formDataImage,
      processData: false,
      contentType: false,
      success: function (response) {
        var parsedResponse = JSON.parse(response);

        if (parsedResponse.status === "success") {
          // Get the image URL from the upload response
          var imageUrl = parsedResponse.url;
          if (formDataObject["business-model"] == "Revenue") {
            agqaproviderrevnew(imageUrl);
          }
          if (formDataObject["business-model"] == "Sale") {
            agqaproviderresales(imageUrl);
          }
        } else {
          $("#ddmu-response").html("<p>" + parsedResponse.message + "</p>");
        }
      },
      error: function (xhr) {
        $("#ddmu-response").html(
          "<p>Something went wrong. Please try again.</p>"
        );
      },
    });
    // ✅ Define with access to $form
    function agqaproviderrevnew(imageUrl) {
      $('input[name="upload-file"]').val("");
      var formData = $form.serialize();
      formData += "&imageurl=" + encodeURIComponent(imageUrl);

      var nonce = agqa_ajax.nonce;

      $.ajax({
        type: "POST",
        url: agqa_ajax.ajax_url,
        data: {
          action: "insert_provider_data",
          form_data: formData,
          nonce: nonce,
        },
        success: function (response) {
          console.log(response);
          if (response.includes("Success")) {
            // alert("Game provider added successfully!");
            // alert("Provider data updated!");
            const $successMsg = $(
              '<div class="submitted-successfully">Game provider added successfully!</div>'
            );
            $form.append($successMsg);

            // Hide after 3 seconds
            setTimeout(function () {
              $successMsg.fadeOut(400, function () {
                $(this).remove();
              });
            }, 3000);
            jQuery(".agqa-popup-form-inner .popup-form-cross-icon").trigger(
              "click"
            );
            location.reload(); // Page reload after success message
          } else {
            // alert(response);
            // alert(response);
            $(".file-preview span").text("");

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
        error: function () {
          alert("An error occurred! Please try again later.");
        },
      });
    }
    function agqaproviderresales(imageUrl) {
      $('input[name="upload-file"]').val("");
      var formData = $form.serialize();
      formData += "&imageurl=" + encodeURIComponent(imageUrl);

      var nonce = agqa_ajax.nonce;

      $.ajax({
        type: "POST",
        url: agqa_ajax.ajax_url,
        data: {
          action: "insert_provider_sale_data",
          form_data: formData,
          nonce: nonce,
        },
        success: function (response) {
          console.log(response);
          if (response.includes("Success")) {
            // alert("Game provider added successfully!");
            const $successMsg = $(
              '<div class="submitted-successfully">Game provider added successfully!</div>'
            );
            $form.append($successMsg);

            // Hide after 3 seconds
            setTimeout(function () {
              $successMsg.fadeOut(400, function () {
                $(this).remove();
              });
            }, 3000);
            jQuery(".agqa-popup-form-inner .popup-form-cross-icon").trigger(
              "click"
            );
            location.reload(); // Page reload after success message
          } else {
            // alert(response);
            $(".file-preview span").text("");

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
        error: function () {
          alert("An error occurred! Please try again later.");
        },
      });
    }
  });
  /**
   * Edit Revenu Form Script
   */
  $("#edit-revnue-form").submit(function (e) {
    e.preventDefault();

    const $form = $(this);
    var formDataBmodel = $form.serialize();
    var formDataObject = {};
    // console.log($('input[type="file"]')[0].files[0] );
    var formDataImage = new FormData();
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
    // ✅ Extra validation for Game Info Website
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
    // Split the serialized string by '&' and loop through each pair
    formDataBmodel.split("&").forEach(function (pair) {
      var [key, value] = pair.split("=");
      formDataObject[key] = decodeURIComponent(value);
    });
    var formDataImage = new FormData();
    formDataImage.append("action", "ddmu_handle_upload");
    formDataImage.append("nonce", agqa_ajax.nonce);
    formDataImage.append("file", $('input[type="file"]')[0].files[0]);
    // console.log(formDataImage);
    // alert(response);
    const $successMsg = $(
      `<div class="submit-warning">Please Waiting...</div>`
    );
    $form.append($successMsg);

    $.ajax({
      url: agqa_ajax.ajax_url,
      type: "POST",
      data: formDataImage,
      processData: false,
      contentType: false,
      success: function (response) {
        // console.log(response);
        var parsedResponse = JSON.parse(response);

        if (parsedResponse.status === "success") {
          // Get the image URL from the upload response
          var imageUrls = parsedResponse.url;
          //           console.log(imageUrls);
          $successMsg.fadeOut(400, function () {
            $(this).remove();
          });
          if (formDataObject["business-model"] == "sale") {
            agqaEditSales(imageUrls);
          } else {
            // console.log(imageUrls);
            agqaEditRevnue(imageUrls);
          }
        } else {
          $("#ddmu-response").html("<p>" + parsedResponse.message + "</p>");
          $successMsg.fadeOut(400, function () {
            $(this).remove();
          });
          if (formDataObject["business-model"] == "sale") {
            imageUrls = "";
            agqaEditSales(imageUrls);
          } else {
            imageUrls = "";
            agqaEditRevnue(imageUrls);
          }
        }
      },
      error: function (xhr) {
        alert(xhr);
        $("#ddmu-response").html(
          "<p>Something went wrong. Please try again.</p>"
        );
      },
    });

    // ✅ Define with access to $form

    function agqaEditRevnue(imageUrls) {
      $('input[name="upload-file"]').val("");
      var formData = $form.serialize();
      var formData = $form.serialize();

      // Remove the 'upload-contract' parameter and its value
      formData = formData.replace(/&upload-contract=[^&]*/, "");

      // If 'upload-contract' is the first parameter, you might need to handle the leading "&"
      if (formData.startsWith("&")) {
        formData = formData.substring(1); // Remove the leading '&'
      }
      formData += "&imageurls=" + encodeURIComponent(imageUrls);

      //       console.log(formData);

      var nonce = agqa_ajax.nonce;
      $.ajax({
        type: "POST",
        url: agqa_ajax.ajax_url,
        data: {
          action: "handle_edit_revnue_form",
          form_data: formData,
          nonce: nonce,
        },
        success: function (response) {
          // console.log(response);
          if (response.includes("Success")) {
            // alert("Provider data updated!");
            const $successMsg = $(
              '<div class="submitted-successfully">Provider data updated!</div>'
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
    }

    /**
     * Edit Sale
     */
    function agqaEditSales(imageUrls) {
      // $('input[name="upload-file"]').val("");
      var formData = $form.serialize();
      formData += "&imageurls=" + encodeURIComponent(imageUrls);
      // console.log(formData);

      var nonce = agqa_ajax.nonce;
      $.ajax({
        type: "POST",
        url: agqa_ajax.ajax_url,
        data: {
          action: "handle_edit_sales_form",
          form_data: formData,
          nonce: nonce,
        },
        success: function (response) {
          // console.log(response);
          if (response.includes("Success")) {
            // alert("Provider data updated!");
            const $successMsg = $(
              '<div class="submitted-successfully">Provider data updated!</div>'
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
    }

    // END
  });
  /**
   * Add Revenue form
   */
  $("#add-revnue-form").submit(function (e) {
    e.preventDefault();
    const $form = $(this);
    var formDataBmodel = $form.serialize();
    // console.log(formDataBmodel);
    var formDataObject = {};
    // console.log($('input[type="file"]')[0].files[0] );
    var formDataImage = new FormData();
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
    // Split the serialized string by '&' and loop through each pair
    formDataBmodel.split("&").forEach(function (pair) {
      var [key, value] = pair.split("=");
      formDataObject[key] = decodeURIComponent(value);
    });
    var formDataImage = new FormData();
    formDataImage.append("action", "ddmu_handle_upload");
    formDataImage.append("nonce", agqa_ajax.nonce);
    formDataImage.append("file", $('input[type="file"]')[0].files[0]);
    // console.log(formDataImage);
    // alert(response);
    const $successMsg = $(
      `<div class="submit-warning">Please Waiting...</div>`
    );
    $form.append($successMsg);

    // Hide after 3 seconds
    setTimeout(function () {
      $successMsg.fadeOut(400, function () {
        $(this).remove();
      });
    }, 3000);
    $.ajax({
      url: agqa_ajax.ajax_url,
      type: "POST",
      data: formDataImage,
      processData: false,
      contentType: false,
      success: function (response) {
        // console.log(response);
        var parsedResponse = JSON.parse(response);
        if (parsedResponse.status === "success") {
          // Get the image URL from the upload response
          var imageUrls = parsedResponse.url;
          // console.log(formDataObject["business-model"]);
          if (formDataObject["business-model"] == "sale") {
            agqaAddSales(imageUrls);
          } else {
            // console.log(imageUrls);
            agqaAddRevnue(imageUrls);
          }
        } else {
          $("#ddmu-response").html("<p>" + parsedResponse.message + "</p>");
          if (formDataObject["business-model"] == "sale") {
            imageUrls = "";
            agqaAddSales(imageUrls);
          } else {
            imageUrls = "";
            agqaAddRevnue(imageUrls);
          }
        }
      },
      error: function (xhr) {
        $("#ddmu-response").html(
          "<p>Something went wrong. Please try again.</p>"
        );
      },
    });
    /**
     * Add Revenue Provider Script
     */
    function agqaAddRevnue(imageUrls) {
      $('input[name="upload-file"]').val("");
      var formData = $form.serialize();
      formData += "&imageurls=" + encodeURIComponent(imageUrls);
      formData +=
        "&provider-name=" +
        $(".agqa-main-game-type .custom-dropdown-selected-value").text();
      // console.log(formData);
      var nonce = agqa_ajax.nonce;
      $.ajax({
        type: "POST",
        url: agqa_ajax.ajax_url,
        data: {
          action: "add_revenue_provider_data",
          form_data: formData,
          nonce: nonce,
        },
        success: function (response) {
          // console.log(response);
          if (response.includes("Success")) {
            // alert("Provider data updated!");
            const $successMsg = $(
              '<div class="submitted-successfully">Provider data updated!</div>'
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
    }
    /**
     * ADD Sale Revenue Script
     */
    function agqaAddSales(imageUrls) {
      $('input[name="upload-file"]').val("");
      var formData = $form.serialize();
      formData += "&imageurls=" + encodeURIComponent(imageUrls);
      formData +=
        "&provider-name=" +
        $(".agqa-main-game-type .custom-dropdown-selected-value").text();
      // console.log(formData);
      var nonce = agqa_ajax.nonce;
      $.ajax({
        type: "POST",
        url: agqa_ajax.ajax_url,
        data: {
          action: "add_sale_provider_data",
          form_data: formData,
          nonce: nonce,
        },
        success: function (response) {
          console.log(response);
          if (response.includes("Success")) {
            // alert("Provider data updated!");
            const $successMsg = $(
              '<div class="submitted-successfully">Provider data updated!</div>'
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
          console.error("AJAX Error:", error);
          alert("An error occurred! Please try again later.");
        },
      });
    }
  });
  /**
   * Enable Disable Filter
   */
  $("#filter-form").submit(function (e) {
    e.preventDefault();

    var selectedFilter = $(".filter-selected-text").text().trim();
    // alert(getRevenueID);

    var currentUrl = window.location.href;
    var url = new URL(currentUrl);
    var params = new URLSearchParams(url.search);

    // Get the revenue parameter from the URL
    var revenueId = params.get("revenue");
    // alert(revenueId);

    // Loop through all the .api-card-container-box elements
    $(".api-card-container-box").each(function () {
      var cardRevenueId = $(this).data("revenue-id");
      var isEnabled = $(this).hasClass("Enabled");
      var isDisabled = $(this).hasClass("Disabled");

      // Check if the revenueId matches and apply the filter based on the selected filter
      if (cardRevenueId == revenueId) {
        // If it matches revenueId, show the element
        if (selectedFilter == "Enabled" && isEnabled) {
          $(this).show(); // Show the enabled element
          $(".section-found").hide();
        } else if (selectedFilter == "Disabled" && isDisabled) {
          $(this).show(); // Show the disabled element
          $(".section-found").hide();
        } else if (
          selectedFilter != "Enabled" &&
          selectedFilter != "Disabled"
        ) {
          // If no filter is selected, show all that match revenueId
          $(this).show();
          $(".section-found").hide();
        } else {
          $(this).hide(); // Hide if it doesn't match the selected filter
          $(".section-found").show();
        }
      } else {
        // Hide elements that don't match the revenueId
        $(this).hide();
      }
    });

    // Check if revenueId is null (i.e., no revenue parameter)
    if (revenueId == null) {
      var isEnabled = $(".api-card-container-box.Enabled").length > 0; // Check if there are any elements with the "Enabled" class
      var isDisabled = $(".api-card-container-box.Disabled").length > 0; // Check if there are any elements with the "Disabled" class

      // Hide the "not found" section by default
      $(".section-found").hide();

      // Show all elements by default
      $(".api-card-container-box").show();

      // If no Enabled or Disabled elements exist, show the "not found" message
      if (!isEnabled && !isDisabled) {
        $(".section-found").show();
      } else {
        // Filter the boxes based on the selected filter
        if (selectedFilter == "Enabled") {
          $(".api-card-container-box.Enabled").show();
          $(".api-card-container-box.Disabled").hide();
          // If no enabled boxes are found, show the "not found" message
          if ($(".api-card-container-box.Enabled:visible").length === 0) {
            $(".section-found").show();
          }
        }
        if (selectedFilter == "Disabled") {
          $(".api-card-container-box.Disabled").show();
          $(".api-card-container-box.Enabled").hide();
          // If no disabled boxes are found, show the "not found" message
          if ($(".api-card-container-box.Disabled:visible").length === 0) {
            $(".section-found").show();
          }
        }
      }
    }
  });
  /**
   * Game Category Filter
   */
  // $("#agqa-game-filter").click(function (e) {
  //   e.preventDefault();

  //   // Get the selected filter and the search text
  //   var selectedFilter = $("input.agqa-filter-select-hidden").val();
  //   var textFilter = $("#filter-search").val().toLowerCase();

  //   $(".provider-card").show();

  //   if (selectedFilter === "New Game Categories") {
  //     $(".provider-card").each(function () {
  //       if (!$(this).find(".agqa-new-label").length) {
  //         $(this).hide();
  //       }
  //     });
  //   } else if (selectedFilter === "API Not Filled in") {
  //     $(".provider-card").each(function () {
  //       if (!$(this).find(".agqa-api-not-filled").length) {
  //         $(this).hide();
  //       }
  //     });
  //   } else if (selectedFilter === "All") {
  //     $(".provider-card").show();
  //   }

  //   if (textFilter.length > 0) {
  //     $(".provider-card").each(function () {
  //       var title = $(this).attr("title").toLowerCase();
  //       if (title.indexOf(textFilter) === -1) {
  //         $(this).hide();
  //       }
  //     });
  //   }

  //   $(".section").each(function () {
  //     var hasVisibleCard = false;
  //     $(this)
  //       .find(".provider-card")
  //       .each(function () {
  //         if ($(this).css("display") !== "none") {
  //           hasVisibleCard = true;
  //         }
  //       });

  //     // Hide the section if there are no visible provider cards
  //     if (!hasVisibleCard) {
  //       $(this).hide();
  //     } else {
  //       $(this).show();
  //     }
  //   });

  //   // Check if each main section should be hidden based on its sections
  //   $(".agqa-main-section-card").each(function () {
  //     var hasVisibleSection = false;

  //     // Check if any of the sections inside the main section are visible
  //     $(this)
  //       .find(".section")
  //       .each(function () {
  //         if ($(this).css("display") !== "none") {
  //           hasVisibleSection = true; // Set flag to true if the section is visible
  //         }
  //       });

  //     // Hide the main section if none of its sections are visible
  //     if (!hasVisibleSection) {
  //       $(this).hide();
  //       $(this).find(".category-heading").hide();
  //     } else {
  //       $(this).show(); // Show the main section if any of its sections are visible
  //       $(this).find(".category-heading").show();
  //     }
  //   });
  //   // Check if there are any visible provider cards after filtering
  //   var anyVisibleProviderCards = $(".provider-card").is(":visible");

  //   // Show or hide the "No results found" message based on the visibility of provider cards
  //   if (!anyVisibleProviderCards) {
  //     $(".section-found").show(); // Show the no-results message
  //   } else {
  //     $(".section-found").hide(); // Hide the no-results message if there are results
  //   }
  // });
  // END

  $("#agqa-game-filter").click(function (e) {
    e.preventDefault();

    // Get the selected filter and the search text
    var selectedFilter = $("input.agqa-filter-select-hidden").val();
    var textFilter = $("#filter-search").val().toLowerCase();

    // Get the active tab and the corresponding section
    var activeTab = $(".tab.active");
    var activeSectionId = activeTab.attr("aria-controls"); // Get the ID of the section associated with the active tab
    var activeSection = $("#" + activeSectionId); // Find the section by ID

    // Check if the active tab is the "All" tab
    if (activeTab.attr("aria-controls") === "panel-all") {
      $(".provider-card").show();

      if (selectedFilter === "New Game Categories") {
        $(".provider-card").each(function () {
          if (!$(this).find(".agqa-new-label").length) {
            $(this).hide();
          }
        });
      } else if (selectedFilter === "API Not Filled in") {
        $(".provider-card").each(function () {
          if (!$(this).find(".agqa-api-not-filled").length) {
            $(this).hide();
          }
        });
      } else if (selectedFilter === "All") {
        $(".provider-card").show();
      }

      if (textFilter.length > 0) {
        $(".provider-card").each(function () {
          var title = $(this).attr("title").toLowerCase();
          if (title.indexOf(textFilter) === -1) {
            $(this).hide();
          }
        });
      }

      $(".section").each(function () {
        var hasVisibleCard = false;
        $(this)
          .find(".provider-card")
          .each(function () {
            if ($(this).css("display") !== "none") {
              hasVisibleCard = true;
            }
          });

        // Hide the section if there are no visible provider cards
        if (!hasVisibleCard) {
          $(this).hide();
        } else {
          $(this).show();
        }
      });

      // Check if each main section should be hidden based on its sections
      $(".agqa-main-section-card").each(function () {
        var hasVisibleSection = false;

        // Check if any of the sections inside the main section are visible
        $(this)
          .find(".section")
          .each(function () {
            if ($(this).css("display") !== "none") {
              hasVisibleSection = true; // Set flag to true if the section is visible
            }
          });

        // Hide the main section if none of its sections are visible
        if (!hasVisibleSection) {
          $(this).hide();
          $(this).find(".category-heading").hide();
        } else {
          $(this).show(); // Show the main section if any of its sections are visible
          $(this).find(".category-heading").show();
        }
      });
      // Check if there are any visible provider cards after filtering
      var anyVisibleProviderCards = $(".provider-card").is(":visible");

      // Show or hide the "No results found" message based on the visibility of provider cards
      if (!anyVisibleProviderCards) {
        $(".section-found").show(); // Show the no-results message
      } else {
        $(".section-found").hide(); // Hide the no-results message if there are results
      }
    } else {
      // Initially show all cards in the active section
      activeSection.find(".provider-card").show();

      // Apply selected filter (New Game Categories / API Not Filled In / All)
      if (selectedFilter === "New Game Categories") {
        activeSection.find(".provider-card").each(function () {
          if (!$(this).find(".agqa-new-label").length) {
            $(this).hide();
          }
        });
      } else if (selectedFilter === "API Not Filled in") {
        activeSection.find(".provider-card").each(function () {
          if (!$(this).find(".agqa-api-not-filled").length) {
            $(this).hide();
          }
        });
      } else if (selectedFilter === "All") {
        activeSection.find(".provider-card").show(); // Show all cards if "All" is selected
      }

      // Filter based on the search text
      if (textFilter.length > 0) {
        activeSection.find(".provider-card").each(function () {
          var title = $(this).attr("title").toLowerCase();
          if (title.indexOf(textFilter) === -1) {
            $(this).hide();
          }
        });
      }

      // Check visibility of cards and sections within the active section
      var anyVisibleProviderCardsInActiveSection = activeSection
        .find(".provider-card")
        .is(":visible");

      // Show or hide the "No results found" message based on the visibility of provider cards in the active section
      if (!anyVisibleProviderCardsInActiveSection) {
        activeSection.find(".section-found").show(); // Show the no-results message for the active section
      } else {
        activeSection.find(".section-found").hide(); // Hide the no-results message if there are results in the active section
      }

      // Check visibility of sections within the active section
      activeSection.find(".section").each(function () {
        var hasVisibleCard = false;
        $(this)
          .find(".provider-card")
          .each(function () {
            if ($(this).css("display") !== "none") {
              hasVisibleCard = true;
            }
          });

        // Hide the section if there are no visible provider cards
        if (!hasVisibleCard) {
          $(this).hide();
        } else {
          $(this).show();
        }
      });

      // Check if each main section inside the active section should be visible or not
      activeSection.find(".agqa-main-section-card").each(function () {
        var hasVisibleSection = false;

        $(this)
          .find(".section")
          .each(function () {
            if ($(this).css("display") !== "none") {
              hasVisibleSection = true;
            }
          });

        // Hide the main section if none of its sections are visible
        if (!hasVisibleSection) {
          $(this).hide();
          $(this).find(".category-heading").hide();
        } else {
          $(this).show();
          $(this).find(".category-heading").show();
        }
      });

      // Check if there are any visible provider cards after filtering
      var anyVisibleProviderCards = $(".provider-card").is(":visible");

      // Show or hide the "No results found" message based on the visibility of provider cards
      if (!anyVisibleProviderCards) {
        $(".section-found").show(); // Show the no-results message
      } else {
        $(".section-found").hide(); // Hide the no-results message if there are results
      }
    }
  });

  /**
   * Reorder Sort Revenue Script
   */
  $("#agqa-sort-revenue").submit(function (e) {
    e.preventDefault();
    const $form = $(this);
    var formData = $form.serialize();
    // console.log(formData);

    var nonce = agqa_ajax.nonce;
    $.ajax({
      type: "POST",
      url: agqa_ajax.ajax_url,
      data: {
        action: "save_user_revenue_sort_order",
        form_data: formData,
        nonce: nonce,
      },
      success: function (response) {
        // console.log(response.data);
        // alert("Provider data updated!");
        const $successMsg = $(
          `<div class="submitted-successfully">${response.data}</div>`
        );

        $form.append($successMsg);

        // Hide after 3 seconds
        setTimeout(function () {
          $successMsg.fadeOut(400, function () {
            $(this).remove();
          });
        }, 3000);
        location.reload();
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error); // Log the error for debugging
        alert("An error occurred! Please try again later.");
      },
    });
  });
  // END

  /**
   * Reorder Sort Sales Script
   */
  $("#agqa-sort-sales").submit(function (e) {
    e.preventDefault();
    const $form = $(this);
    var formData = $form.serialize();
    console.log(formData);

    var nonce = agqa_ajax.nonce;
    $.ajax({
      type: "POST",
      url: agqa_ajax.ajax_url,
      data: {
        action: "save_user_sales_sort_order",
        form_data: formData,
        nonce: nonce,
      },
      success: function (response) {
        // console.log(response.data);
        // alert("Provider data updated!");
        const $successMsg = $(
          `<div class="submitted-successfully">${response.data}</div>`
        );

        $form.append($successMsg);

        // Hide after 3 seconds
        setTimeout(function () {
          $successMsg.fadeOut(400, function () {
            $(this).remove();
          });
        }, 3000);
        location.reload();
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error); // Log the error for debugging
        alert("An error occurred! Please try again later.");
      },
    });
  });
  // END
});
