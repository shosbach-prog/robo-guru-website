/******/ (() => { // webpackBootstrap
/**
 * Activity Sharing JavaScript
 *
 * @package BuddyBoss_SEO
 * @since 1.0.0
 */

(function ($) {
  'use strict';

  var BuddyBossActivitySharing = {
    dropdown: null,
    modal: null,
    linkModal: null,
    activityModal: null,
    currentActivityId: null,
    currentShareType: null,
    currentPage: 1,
    hasMorePages: false,
    isLoadingThreads: false,
    playVideoInModal: false,
    /**
     * Initialize
     */
    init: function init() {
      this.dropdown = $('#buddyboss-share-dropdown');
      this.modal = $('#buddyboss-share-modal');
      this.linkModal = $('#buddyboss-share-link-modal');
      this.activityModal = $('#buddyboss-share-activity-modal');

      // Debug: Check if elements exist
      if (this.dropdown.length === 0) {
        console.warn('BuddyBoss Share: Dropdown element not found');
      }
      this.bindEvents();
      this.initSelectedRecipients();
    },
    /**
     * Bind event listeners
     */
    bindEvents: function bindEvents() {
      // Share button click - show dropdown
      $(document).on('click', '.button.share.bp-secondary-action', this.handleShareClick.bind(this));

      // Click outside dropdown to close
      $(document).on('click', this.handleOutsideClick.bind(this));

      // Dropdown option click
      $(document).on('click', '.share-dropdown-item', this.handleDropdownOptionClick.bind(this));

      // Close modal
      $(document).on('click', '.buddyboss-modal-close, .buddyboss-modal-overlay', this.closeModal.bind(this));

      // Close activity modal
      $(document).on('click', '.buddyboss-share-modal-close, .buddyboss-share-modal-overlay', this.closeActivityModal.bind(this));

      // Cancel activity modal
      $(document).on('click', '.share-activity-cancel', this.closeActivityModal.bind(this));

      // Submit share to activity feed
      $(document).on('click', '.share-activity-submit', this.handleActivityShareSubmit.bind(this));

      // Share platform button click (for Share as Link)
      $(document).on('click', '.share-platform-btn', this.handlePlatformClick.bind(this));

      // Copy link button click
      $(document).on('click', '.share-link-copy-btn', this.handleCopyLink.bind(this));

      // Submit share
      $(document).on('click', '.share-submit-btn', this.handleShareSubmit.bind(this));

      // Search functionality
      $(document).on('input', '.share-search-input', this.handleSearch.bind(this));

      // Message modal - close button
      $(document).on('click', '.share-message-close', this.closeModal.bind(this));

      // Message modal - send button
      $(document).on('click', '.share-message-send-btn', this.handleMessageSend.bind(this));

      // Message modal - search functionality
      $(document).on('input', '.share-message-search-input', this.handleMessageSearch.bind(this));

      // ESC key to close modals
      $(document).on('keydown', this.handleKeyPress.bind(this));

      // Additional cleanup listeners for BuddyBoss native form
      $(document).on('reset', '#whats-new-form', this.handleFormReset.bind(this));
      $(document).on('click', '.bp-activity-privacy__close', this.handleFormReset.bind(this));

      // Shared activity modal - click on shared activity preview to open modal
      $(document).on('click', '.activity-content  .shared-activity-preview, .shared-activity-message .shared-activity-preview', this.handleSharedActivityClick.bind(this));

      // Prevent media click handlers from firing inside shared activity preview
      // Ensure to catch the event before BuddyBoss platform handlers fire
      // NOTE: Using document-level capture handler because CSS pointer-events approach didn't work
      // TODO: Consider refactoring to direct event delegation on .shared-activity-preview for better maintainability
      var self = this;
      var mediaClickHandler = function mediaClickHandler(e) {
        var target = e.target;
        var $target = $(target);

        // FIRST: Check if the click is inside a shared activity preview
        // If not, return immediately without any processing - let normal handlers work
        var $preview = $target.closest('.shared-activity-preview');
        if ($preview.length === 0) {
          return; // Not inside a shared activity preview - let normal handlers work
        }

        // Skip if target is the preview itself
        if ($target.is('.shared-activity-preview')) {
          return;
        }

        // Check if this is in messages context - allow video theater links to work normally in messages
        var $sharedActivityMessage = $target.closest('.shared-activity-message');
        var isInMessages = $sharedActivityMessage.length > 0;

        // Special handling for .bb-open-video-theatre
        // Only allow video theater links to work normally if in messages context
        // For activity feed/group feed/profile feed, we should intercept video theater links
        if ($target.is('a.bb-open-video-theatre') || $target.closest('a.bb-open-video-theatre').length > 0) {
          if (isInMessages) {
            return; // In messages - allow video theater links to work normally
          }
          // In activity feed/group feed/profile feed - intercept and open activity modal instead
          // Fall through to media element handling below
        }

        // Special handling for enlarge button - never intercept this
        if ($target.is('.vjs-icon-square, .enlarge_button') || $target.closest('.vjs-icon-square, .enlarge_button').length > 0) {
          // The BuddyBoss handler will trigger a click on .bb-open-video-theatre
          // But jQuery's .trigger() doesn't create a proper event that bubbles through capture phase
          // So we need to dispatch a native click event as a fallback
          var $enlargeBtn = $target.is('.vjs-icon-square, .enlarge_button') ? $target : $target.closest('.vjs-icon-square, .enlarge_button');
          var $videoContainer = $enlargeBtn.closest('.video-js').parent();
          var $videoTheatreLink = $videoContainer.find('.bb-open-video-theatre');
          if ($videoTheatreLink.length > 0) {
            // Use setTimeout to ensure this happens after the BuddyBoss trigger attempt
            setTimeout(function () {
              var videoTheatreElement = $videoTheatreLink[0];
              if (videoTheatreElement) {
                // Dispatch a native click event that will properly bubble through capture phase
                var nativeClickEvent = new MouseEvent('click', {
                  bubbles: true,
                  cancelable: true,
                  view: window
                });
                videoTheatreElement.dispatchEvent(nativeClickEvent);
              }
            }, 50); // Small delay to let BuddyBoss trigger happen first
          }
          return; // Never intercept enlarge button - let it work normally
        }

        // Special handling for single videos in messages - don't intercept clicks on the video element itself
        // Allow single videos to play directly
        if (isInMessages) {
          var $videoWrap = $target.closest('.bb-activity-video-wrap');
          if ($videoWrap.length > 0 && $videoWrap.hasClass('bb-video-length-1')) {
            // Check if click is on the video element itself or its controls
            var isVideoElement = $target.is('video, .video-js, .single-activity-video') || $target.closest('video, .video-js, .single-activity-video, .vjs-control-bar, .vjs-big-play-button').length > 0;

            // If clicking on the video element or controls, don't intercept
            if (isVideoElement) {
              return; // Let single videos play directly in messages
            }
          }
        }

        // Check if it's a video play button
        var isVideoPlayButton = $target.is('.vjs-big-play-button') || $target.closest('.vjs-big-play-button').length > 0;

        // Check if it's a media element (including video theater links in activity feed/group feed/profile feed)
        var isMediaElement = $target.is('a.bb-open-media-theatre, a.bb-open-document-theatre, a.bb-open-video-theatre, a.entry-img, img, video, audio') || $target.closest('a.bb-open-media-theatre, a.bb-open-document-theatre, a.bb-open-video-theatre, a.entry-img, .activity-media-elem, .document-activity, .media-activity, .bb-activity-video-wrap, .bb-activity-video-elem').length > 0;
        if (isMediaElement || isVideoPlayButton) {
          // Prevent default and stop propagation in capture phase
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();

          // Store flag if video play button was clicked
          if (isVideoPlayButton) {
            self.playVideoInModal = true;
          }

          // Manually trigger the shared activity preview click
          // Use setTimeout to ensure this happens after the event is fully stopped
          setTimeout(function () {
            $preview.trigger('click');
          }, 0);
        }
      };

      // Store handler for cleanup if needed
      document._buddybossSharingMediaHandler = mediaClickHandler;
      document.addEventListener('click', mediaClickHandler, true); // true = use capture phase

      // Clean up shared activity modal class when modal is closed
      $(document).on('click', '.bb-activity-model-wrapper .bb-model-close-button', this.cleanupSharedActivityModalClass.bind(this));
    },
    /**
     * Handle share button click - show dropdown
     */
    handleShareClick: function handleShareClick(e) {
      e.preventDefault();
      e.stopPropagation();
      var $btn = $(e.currentTarget);
      this.currentActivityId = $btn.data('activity-id');
      // Close any open dropdowns first
      this.closeDropdown();

      // Position dropdown near the button
      var btnOffset = $btn.offset();
      var btnHeight = $btn.outerHeight();
      this.dropdown.css({
        position: 'absolute',
        top: btnOffset.top + btnHeight,
        left: btnOffset.left,
        display: 'block'
      });
    },
    /**
     * Handle click outside dropdown
     */
    handleOutsideClick: function handleOutsideClick(e) {
      if (!$(e.target).closest('.button.share.bp-secondary-action, .buddyboss-share-dropdown').length) {
        this.closeDropdown();
      }
    },
    /**
     * Close dropdown
     */
    closeDropdown: function closeDropdown() {
      this.dropdown.hide();
    },
    /**
     * Handle dropdown option click
     */
    handleDropdownOptionClick: function handleDropdownOptionClick(e) {
      e.preventDefault();
      e.stopPropagation();
      var $btn = $(e.currentTarget);
      this.currentShareType = $btn.data('share-type');

      // Close dropdown
      this.closeDropdown();

      // Handle based on share type
      if (this.currentShareType === 'link') {
        // Show link platforms modal
        this.openLinkModal();
      } else if (this.currentShareType === 'feed') {
        // Check if custom message is enabled
        if (buddybossSharingFrontend.enableCustomMsg) {
          // Show activity feed modal
          this.openActivityModal();
        } else {
          // Share directly without modal
          this.shareDirectly();
        }
      } else if (this.currentShareType === 'message') {
        // For message type, open special message modal
        this.openMessageModal();
      } else if (this.currentShareType === 'group') {
        // For group type, open group selection modal
        this.openGroupModal();
      } else if (this.currentShareType === 'profile') {
        // For profile type, open friends selection modal
        this.openFriendsModal();
      } else {
        // For other types - show generic modal
        this.openComposeModal();
      }
    },
    /**
     * Open link platforms modal
     */
    openLinkModal: function openLinkModal() {
      // Show modal with loading state
      $('.share-link-url-input').val('Loading...');
      // Remove active class while link is being generated
      $('.share-link-platforms').removeClass('active');
      this.linkModal.fadeIn(300);
      $("body").css("overflow", "hidden");

      // Fetch the actual activity permalink from backend
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_get_activity_permalink',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: this.currentActivityId
        },
        success: function success(response) {
          if (response.success && response.data.permalink) {
            $('.share-link-url-input').val(response.data.permalink);
          } else {
            $('.share-link-url-input').val(window.location.href);
          }
          // Add active class once link is generated
          $('.share-link-platforms').addClass('active');
        },
        error: function error() {
          // Fallback to trying to get URL from DOM
          // Get URL from the input field (already loaded by openLinkModal)
          var activityUrl = $('.share-link-url-input').val();
          $('.share-link-url-input').val(activityUrl);
          // Add active class once link is generated (even on error)
          $('.share-link-platforms').addClass('active');
        }
      });
    },
    /**
     * Open compose modal
     */
    openComposeModal: function openComposeModal() {
      this.modal.fadeIn(300);
      $('body').css('overflow', 'hidden');
      this.loadShareContent();
    },
    /**
     * Open message modal directly (not inside generic modal)
     */
    openMessageModal: function openMessageModal() {
      var _this = this;
      // Remove any existing message modal container
      $('.buddyboss-share-message-modal-container').remove();

      // Create modal container and append to body
      var $container = $('<div class="buddyboss-share-message-modal-container" style="display: none;"></div>');
      $('body').append($container);
      // Load message modal content via AJAX
      $container.html('<div class="share-content-loading"><span class="spinner is-active"></span></div>');
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_get_share_modal_content',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: this.currentActivityId,
          share_type: 'message'
        },
        success: function success(response) {
          if (response.success) {
            $container.html(response.data.content);

            // Show modal
            $container.fadeIn(300);
            $('body').css('overflow', 'hidden');

            // Load recent threads
            _this.loadRecentThreads();
          } else {
            _this.showNotification(response.data.message || buddybossSharingFrontend.i18n.failedLoadModal, 'error');
            $container.remove();
          }
        },
        error: function error(xhr, status, _error) {
          _this.showNotification('An error occurred. Please try again.', 'error');
          $container.remove();
        }
      });
    },
    /**
     * Close modal
     */
    closeModal: function closeModal(e) {
      if (e) {
        var $target = $(e.target);
        if (!$target.hasClass('buddyboss-modal-close') && !$target.hasClass('buddyboss-modal-overlay') && !$target.hasClass('share-message-close') && $target.closest('.buddyboss-modal-close').length === 0 && $target.closest('.share-message-close').length === 0) {
          return;
        }
      }

      // Check if we're in an activity sharing context and cleanup
      if (this.currentActivityId && (this.currentShareType === 'feed' || this.currentShareType === 'group' || this.currentShareType === 'profile')) {
        // Check if the shared activity preview exists in the form
        if ($('#whats-new-form').find('.buddyboss-shared-activity-preview').length > 0) {
          // Call cleanup function based on share type
          if (this.currentShareType === 'feed') {
            this.cleanupSharedActivityForm();
          } else if (this.currentShareType === 'group') {
            this.cleanupSharedActivityFormForGroup();
          } else if (this.currentShareType === 'profile') {
            this.cleanupSharedActivityFormForFriend();
          }
        }
      }
      this.modal.fadeOut(300);
      this.linkModal.fadeOut(300);
      $('.buddyboss-share-message-modal-container').fadeOut(300, function () {
        $(this).remove();
      });
      $('body').css('overflow', '');
      this.currentActivityId = null;
      this.currentShareType = null;
    },
    /**
     * Open activity feed modal
     */
    openActivityModal: function openActivityModal() {
      var _this2 = this;
      // Clear any existing draft before opening share form
      this.clearDraftActivity();

      // Add body class early to handle UI changes
      $('body').addClass('bb-sharing-active');

      // Check if form exists, if not load it first
      var $form = $('#whats-new-form');
      var $formContainer = $('#bp-nouveau-activity-form');
      if (!$form.length || !$formContainer.length) {
        // Form doesn't exist, load it via AJAX
        this.loadActivityPostForm().then(function () {
          // Form loaded, now open it
          _this2.openBuddyBossPostForm();
          // Load and inject shared activity content
          _this2.loadAndInjectSharedActivity();
        })["catch"](function (error) {
          console.error('Failed to load activity post form:', error);
          _this2.showNotification(buddybossSharingFrontend.i18n.error || 'Failed to load post form', 'error');
        });
      } else {
        // Form exists, proceed normally
        this.openBuddyBossPostForm();
        // Load and inject shared activity content
        this.loadAndInjectSharedActivity();
      }
    },
    /**
     * Load activity post form via AJAX (for single activity pages)
     */
    loadActivityPostForm: function loadActivityPostForm() {
      return new Promise(function (resolve, reject) {
        // Check if form already exists
        var $existingForm = $('#whats-new-form');
        if ($existingForm.length) {
          // Form already exists, resolve immediately
          resolve();
          return;
        }

        // Check if we're on a single activity page
        var isSingleActivity = $('body').hasClass('activity-singular');
        if (!isSingleActivity) {
          // Not on single activity page, resolve immediately
          resolve();
          return;
        }

        // Check if form container exists, if not create it
        var $formContainer = $('#bp-nouveau-activity-form');
        if (!$formContainer.length) {
          // Create form container in the single activity edit form wrap or body
          var $editFormWrap = $('#bp-nouveau-single-activity-edit-form-wrap');
          if ($editFormWrap.length) {
            $formContainer = $('<div id="bp-nouveau-activity-form" class="activity-update-form"></div>');
            $editFormWrap.append($formContainer);
            $editFormWrap.show(); // Make sure the wrapper is visible
          } else {
            // Fallback: append to body
            $formContainer = $('<div id="bp-nouveau-activity-form" class="activity-update-form"></div>');
            $('body').append($formContainer);
          }
        }

        // Load form template via AJAX
        $.ajax({
          url: buddybossSharingFrontend.ajaxUrl,
          type: 'POST',
          data: {
            action: 'buddyboss_get_activity_post_form',
            nonce: buddybossSharingFrontend.nonce
          },
          success: function success(response) {
            if (response.success && response.data.form_html) {
              // Inject the form HTML
              $formContainer.html(response.data.form_html);

              // Wait for the platform's JavaScript to initialize the form
              // The platform's JS checks for .activity-update-form on page load
              // Wait for it to process the newly injected form
              var attempts = 0;
              var maxAttempts = 20; // 2 seconds max wait
              var _checkFormInitialized = function checkFormInitialized() {
                var $form = $('#whats-new-form');
                if ($form.length) {
                  // Form is initialized, resolve
                  resolve();
                } else if (attempts < maxAttempts) {
                  // Form not yet initialized, check again
                  attempts++;
                  setTimeout(_checkFormInitialized, 100);
                } else {
                  // Timeout - form might not initialize automatically
                  // Try to manually trigger initialization if platform JS is available
                  if (typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.Activity && bp.Nouveau.Activity.postForm && typeof bp.Nouveau.Activity.postForm.postFormView === 'function') {
                    // Try to manually initialize
                    try {
                      bp.Nouveau.Activity.postForm.postFormView();
                      // Wait a bit more for initialization
                      setTimeout(function () {
                        if ($('#whats-new-form').length) {
                          resolve();
                        } else {
                          reject(new Error('Form initialization timeout'));
                        }
                      }, 500);
                    } catch (e) {
                      reject(new Error('Failed to initialize form: ' + e.message));
                    }
                  } else {
                    reject(new Error('Form initialization timeout - platform JS not available'));
                  }
                }
              };

              // Start checking after a short delay
              setTimeout(_checkFormInitialized, 200);
            } else {
              var _response$data;
              reject(new Error(((_response$data = response.data) === null || _response$data === void 0 ? void 0 : _response$data.message) || 'Failed to load form'));
            }
          },
          error: function error(xhr, status, _error2) {
            reject(new Error(_error2 || 'AJAX error'));
          }
        });
      });
    },
    /**
     * Open BuddyBoss native post form
     */
    openBuddyBossPostForm: function openBuddyBossPostForm() {
      // Focus on the textarea to trigger BuddyBoss's displayFull
      var $textarea = $('#whats-new');
      var $form = $('#whats-new-form');
      var $formWrapper = $form.closest('.activity-update-form');
      var $editFormWrap = $('#bp-nouveau-single-activity-edit-form-wrap');

      // If form is inside the hidden edit form wrap, show it
      if ($editFormWrap.length && $editFormWrap.is(':hidden')) {
        $editFormWrap.show();
      }

      // Ensure form wrapper is visible
      if ($formWrapper.length && $formWrapper.is(':hidden')) {
        $formWrapper.show();
      }

      // Ensure form is visible
      if ($form.length && $form.is(':hidden')) {
        $form.show();
      }
      if ($textarea.length) {
        // Wait a bit for form to be ready, then focus
        setTimeout(function () {
          // Check if form wrapper needs modal-popup class
          if ($formWrapper.length && !$formWrapper.hasClass('modal-popup')) {
            $formWrapper.addClass('modal-popup');
            $('body').addClass('activity-modal-open');
          }

          // Ensure form is still visible after adding classes
          if ($editFormWrap.length) {
            $editFormWrap.show();
          }
          if ($formWrapper.length) {
            $formWrapper.show();
          }
          if ($form.length) {
            $form.show();
          }

          // Focus the textarea to trigger displayFull
          $textarea.focus();
        }, 100);
      } else {
        console.warn('BuddyBoss post form not found');
      }
    },
    /**
     * Load and inject shared activity into BuddyBoss form
     */
    loadAndInjectSharedActivity: function loadAndInjectSharedActivity() {
      var _this3 = this;
      // Wait for form to be visible
      setTimeout(function () {
        $.ajax({
          url: buddybossSharingFrontend.ajaxUrl,
          type: 'POST',
          data: {
            action: 'buddyboss_get_activity_content',
            nonce: buddybossSharingFrontend.nonce,
            activity_id: _this3.currentActivityId
          },
          success: function success(response) {
            if (response.success) {
              _this3.injectSharedActivityToForm(response.data.content);
            }
          },
          error: function error() {
            console.error(buddybossSharingFrontend.i18n.failedLoadActivity);
          }
        });
      }, 300);
    },
    /**
     * Inject shared activity content into BuddyBoss form
     */
    injectSharedActivityToForm: function injectSharedActivityToForm(content) {
      var $form = $('#whats-new-form');
      // Add custom class for activity sharing form
      $form.addClass('activity-share-form');

      // Store the shared activity ID in the form
      if (!$form.find('#buddyboss-shared-activity-id').length) {
        $form.append("<input type=\"hidden\" id=\"buddyboss-shared-activity-id\" value=\"".concat(this.currentActivityId, "\">"));
      } else {}

      // Inject the shared activity preview below the textarea
      var $textarea = $('#whats-new');
      var $previewContainer = $form.find('.buddyboss-shared-activity-preview');
      if ($previewContainer.length === 0) {
        $previewContainer = $('<div class="buddyboss-shared-activity-preview"></div>');
        $textarea.parent().after($previewContainer);
      }
      $previewContainer.html(content);

      // Add loading indicator for preview content
      this.addLoadingIndicatorToPreview($previewContainer);

      // Initialize lazy loading for images
      this.initializeLazyLoading($previewContainer);

      // Initialize Video.js for any videos in preview
      this.initializeVideoJS($previewContainer);

      // Disable media/video/document/poll upload buttons
      this.disableUploadButtons();

      // Remove featured image button from modal header
      this.removeFeaturedImageButton();

      // Hook into form submission
      this.hookFormSubmission();

      // Trigger validation to enable submit button when preview is added
      if (typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.Activity && bp.Nouveau.Activity.postForm && bp.Nouveau.Activity.postForm.postForm) {
        bp.Nouveau.Activity.postForm.postForm.postValidate();
      }
    },
    /**
     * Initialize Video.js for videos in shared activity preview
     */
    initializeVideoJS: function initializeVideoJS($container) {
      // Check if videojs is available
      if (typeof videojs === 'undefined') {
        return;
      }

      // Use setTimeout to ensure DOM is fully rendered
      setTimeout(function () {
        // Find video elements that need initialization
        $container.find('.video-js.single-activity-video').each(function () {
          var $video = $(this);
          var videoId = $video.attr('id');
          if (!videoId) {
            return;
          }

          // Check if Video.js is already initialized for this element
          if ($video.hasClass('vjs-initialized')) {
            return;
          }
          try {
            // Find and hide the loader
            var $videoElem = $video.closest('.bb-activity-video-elem');
            var $loader = $videoElem.find('.bb-video-loader');

            // Initialize Video.js
            // The initialization callback fires when player is ready, so we handle everything there
            videojs(videoId, {
              'controls': true,
              'aspectRatio': '16:9',
              'fluid': true,
              'playbackRates': [0.5, 1, 1.5, 2],
              'fullscreenToggle': false
            }, function () {
              // This callback fires when player is ready
              var player = this;

              // Hide the loader once player is ready
              if ($loader.length) {
                $loader.hide();
              }

              // Trigger resize to ensure proper dimensions are set
              if (window.dispatchEvent) {
                window.dispatchEvent(new Event('resize'));
              }

              // Force player to handle resize
              if (player && player.trigger) {
                player.trigger('resize');
              }
            });
          } catch (error) {
            console.error('Error initializing Video.js:', error);
          }
        });
      }, 100);
    },
    /**
     * Initialize lazy loading for images in preview
     */
    initializeLazyLoading: function initializeLazyLoading($container) {
      // Process lazy images immediately since they're in a visible preview
      setTimeout(function () {
        var $lazyImages = $container.find('img.lazy[data-src]');
        $lazyImages.each(function () {
          var $img = $(this);
          var dataSrc = $img.attr('data-src');
          if (dataSrc) {
            // Set the actual image source
            $img.attr('src', dataSrc);
            $img.removeAttr('data-src');

            // Remove lazy class once loaded
            $img.on('load', function () {
              $img.removeClass('lazy');
            });

            // Trigger lazy load event for platform compatibility
            $(document).trigger('bp_nouveau_lazy_load', {
              element: this
            });

            // If image is already cached, trigger load immediately
            if ($img[0].complete) {
              $img.trigger('load');
            }
          }
        });

        // Try to use platform's lazy load function
        if (typeof bp !== 'undefined' && bp.Nouveau && typeof bp.Nouveau.lazyLoad === 'function') {
          bp.Nouveau.lazyLoad($container.find('img.lazy'));
        }
      }, 50);
    },
    /**
     * Disable media upload and poll buttons
     */
    disableUploadButtons: function disableUploadButtons() {
      // Simply hide the entire toolbar when sharing
      setTimeout(function () {
        var $toolbar = $('#whats-new-toolbar');
        if ($toolbar.length) {
          $toolbar.attr('data-share-disabled', 'true');
        } else {
          // Try again if not found
          setTimeout(function () {
            var $toolbar2 = $('#whats-new-toolbar');
            if ($toolbar2.length) {
              $toolbar2.attr('data-share-disabled', 'true');
            }
          }, 500);
        }
      }, 300);
    },
    /**
     * Remove featured image button from modal header
     */
    removeFeaturedImageButton: function removeFeaturedImageButton() {
      // Find featured image button in modal header
      var $featuredImageBtn = $('.bb-activity-post-feature-image-button');
      if ($featuredImageBtn.length) {
        // Remove button from DOM
        $featuredImageBtn.remove();
      }
    },
    /**
     * Hide the edit form wrap on single activity pages and remove modal classes
     */
    hideEditFormWrapOnSingleActivityPage: function hideEditFormWrapOnSingleActivityPage() {
      var $editFormWrap = $('#bp-nouveau-single-activity-edit-form-wrap');
      if ($editFormWrap.length && $('body').hasClass('activity-singular')) {
        $editFormWrap.hide();

        // Also remove modal classes
        var $formWrapper = $editFormWrap.find('.activity-update-form');
        if ($formWrapper.length) {
          $formWrapper.removeClass('modal-popup');
          $('body').removeClass('activity-modal-open');
        }
      }
    },
    /**
     * Hook into BuddyBoss form submission
     */
    hookFormSubmission: function hookFormSubmission() {
      var self = this;
      // Remove previous handler if exists
      $('#whats-new-form').off('submit.buddyboss-share');

      // Add new handler with native addEventListener to bind at capture phase
      var form = document.getElementById('whats-new-form');
      if (form) {
        // Remove any existing listener
        if (form._buddybossShareHandler) {
          form.removeEventListener('submit', form._buddybossShareHandler, true);
        }

        // Create and store handler
        form._buddybossShareHandler = function (e) {
          var sharedActivityId = document.getElementById('buddyboss-shared-activity-id');
          if (sharedActivityId && sharedActivityId.value) {
            // Stop ALL event propagation
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // Get the content from textarea (BuddyBoss uses contenteditable div)
            var $whatsNew = $('#whats-new');
            var customMessage = '';

            // Check if it's a contenteditable div or textarea
            if ($whatsNew.is('[contenteditable]')) {
              // Transform emoji images into emoji unicode (same as BuddyBoss Platform)
              $whatsNew.find('img.emojioneemoji, img.bb-rl-emojioneemoji').replaceWith(function () {
                return this.dataset.emojiChar;
              });

              // Use innerHTML like BuddyBoss Platform does to preserve emojis
              customMessage = $.trim($whatsNew[0].innerHTML.replace(/<div>/gi, '\n').replace(/<\/div>/gi, ''));
              customMessage = customMessage.replace(/&nbsp;/g, ' ');
            } else {
              customMessage = $whatsNew.val().trim();
            }

            // Get privacy from radio buttons (same way BuddyBoss does it at line 4344)
            var privacy = $('#whats-new-form').find('.bp-activity-privacy__input:checked').val() || 'public';
            // Submit via our AJAX handler
            self.submitSharedActivity(sharedActivityId.value, customMessage, privacy);
            return false;
          }
        };

        // Add listener at capture phase with highest priority
        form.addEventListener('submit', form._buddybossShareHandler, true);
      } else {
        console.error('Form #whats-new-form not found!');
      }
    },
    /**
     * Submit shared activity
     */
    submitSharedActivity: function submitSharedActivity(activityId, customMessage, privacy) {
      var _this4 = this;
      var $submitBtn = $('#aw-whats-new-submit');
      var originalBtnText = $submitBtn.text();

      // Disable submit button
      $submitBtn.prop('disabled', true).text('Posting...');

      // Extract topic_id and has_topic_selector if topic selector exists (same as BuddyBoss Platform)
      var topicId = 0;
      var hasTopicSelector = false;
      var topicSelector = $('#buddypress .whats-new-topic-selector .bb-topic-selector-list li');
      console.log('[BuddyBoss Share Debug] Topic selector elements found:', topicSelector.length);
      if (topicSelector.length) {
        hasTopicSelector = true;
        var selectedTopic = topicSelector.find('a.selected');
        console.log('[BuddyBoss Share Debug] Selected topic links:', selectedTopic.length);
        topicId = selectedTopic.data('topic-id') || 0;
        console.log('[BuddyBoss Share Debug] Final values - has_topic_selector:', hasTopicSelector, ', topic_id:', topicId);
      }
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_share_to_feed',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: activityId,
          custom_message: customMessage,
          privacy: privacy,
          has_topic_selector: hasTopicSelector,
          topic_id: topicId
        },
        success: function success(response) {
          if (response.success) {
            _this4.showNotification(response.data.message || buddybossSharingFrontend.i18n.sharedSuccess, 'success');

            // Update share count
            if (response.data.share_count) {
              _this4.updateShareCount(response.data.share_count);
            }

            // Inject activity into stream (same logic as BuddyBoss)
            // Don't inject on single activity pages - only on feed pages
            var isSingleActivityPage = $('body').hasClass('activity-singular');
            if (response.data.activity && typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.inject && !isSingleActivityPage) {
              var store = bp.Nouveau.getStorage('bp-activity');
              var searchTerms = $('[data-bp-search="activity"] input[type="search"]').val();
              var matches = {};
              var toPrepend = false;

              // Check if search terms match
              if (searchTerms) {
                searchTerms = new RegExp(searchTerms, 'im');
                matches = response.data.activity.match(searchTerms);
              }

              // Determine if we should prepend
              if (!searchTerms || matches) {
                toPrepend = !store.filter || 0 === parseInt(store.filter, 10) || 'activity_update' === store.filter || 'activity_share' === store.filter;
              }

              // Check scope for directory
              if (toPrepend && response.data.is_directory) {
                toPrepend = 'all' === store.scope;
              }

              // Only inject if activity belongs in current view
              if (toPrepend && !$('#activity-' + response.data.id).length) {
                // Make sure container exists
                if (!$('#activity-stream ul.activity-list').length) {
                  $('#activity-stream').html($('<ul></ul>').addClass('activity-list item-list bp-list'));
                }

                // Check for pinned activity
                var pinnedActivity = $('#activity-stream ul.activity-list li:first.bb-pinned');
                if (pinnedActivity.length > 0) {
                  bp.Nouveau.inject('#activity-stream ul.activity-list li:first.bb-pinned', response.data.activity, 'after');
                } else {
                  bp.Nouveau.inject('#activity-stream ul.activity-list', response.data.activity, 'prepend');
                }

                // Trigger scroll to load images
                jQuery(window).scroll();
              }
            }

            // On single activity pages, remove any activity that might have been added to the stream
            if (isSingleActivityPage && response.data.id) {
              // Remove the activity if it was added
              $('#activity-' + response.data.id).remove();
              // Also check for any activities with the share action type
              $('#activity-stream li.activity-item[data-bp-activity-id="' + response.data.id + '"]').remove();
            }

            // Clear draft activity
            _this4.clearDraftActivity();

            // Reset and close form
            _this4.cleanupSharedActivityForm();
            $('#whats-new-form').trigger('reset');
          } else {
            _this4.showNotification(response.data.message || buddybossSharingFrontend.i18n.error, 'error');
            $submitBtn.prop('disabled', false).text(originalBtnText);
          }
        },
        error: function error(xhr, status, _error3) {
          console.error('AJAX error:', {
            xhr: xhr,
            status: status,
            error: _error3
          });
          _this4.showNotification(buddybossSharingFrontend.i18n.error, 'error');
          $submitBtn.prop('disabled', false).text(originalBtnText);
        }
      });
    },
    /**
     * Clear draft activity data
     * To ensure that when a shared post is submitted, the draft is cleared
     */
    clearDraftActivity: function clearDraftActivity() {
      // Use BuddyBoss platform's resetDraftActivity function
      if (typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.Activity && bp.Nouveau.Activity.postForm && typeof bp.Nouveau.Activity.postForm.resetDraftActivity === 'function') {
        bp.Nouveau.Activity.postForm.resetDraftActivity(true);
      }
    },
    /**
     * Cleanup shared activity form elements
     */
    cleanupSharedActivityForm: function cleanupSharedActivityForm() {
      $('#buddyboss-shared-activity-id').remove();
      $('.buddyboss-shared-activity-preview').remove();
      $('#whats-new-form').off('submit.buddyboss-share');
      $('#whats-new-form').removeClass('activity-share-form');

      // Trigger validation to disable submit button when preview is removed
      if (typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.Activity && bp.Nouveau.Activity.postForm && bp.Nouveau.Activity.postForm.postForm) {
        bp.Nouveau.Activity.postForm.postForm.postValidate();
      }
      $('body').removeClass('bb-sharing-active');

      // Remove native event listener
      var form = document.getElementById('whats-new-form');
      if (form && form._buddybossShareHandler) {
        form.removeEventListener('submit', form._buddybossShareHandler, true);
        delete form._buddybossShareHandler;
      }

      // Show toolbar again
      var $toolbar = $('#whats-new-toolbar[data-share-disabled="true"]');
      if ($toolbar.length) {
        $toolbar.show().removeAttr('data-share-disabled');
      }

      // Hide the edit form wrap on single activity pages
      this.hideEditFormWrapOnSingleActivityPage();

      // Reset current state
      this.currentActivityId = null;
      this.currentShareType = null;
      this.selectedGroup = null;
      this.selectedFriend = null;
    },
    /**
     * Close activity modal
     */
    closeActivityModal: function closeActivityModal(e) {
      if (e) {
        e.preventDefault();
      }

      // Cleanup shared activity elements
      this.cleanupSharedActivityForm();

      // Reset BuddyBoss form
      $('#whats-new-form').trigger('reset');

      // Hide the edit form wrap on single activity pages
      this.hideEditFormWrapOnSingleActivityPage();
      this.currentActivityId = null;
      this.currentShareType = null;
    },
    /**
     * Load activity content for modal
     */
    loadActivityContent: function loadActivityContent() {
      var _this5 = this;
      var $preview = $('#share-activity-preview');
      $preview.html('<div class="loading"><span class="spinner is-active"></span></div>');
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_get_activity_content',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: this.currentActivityId
        },
        success: function success(response) {
          if (response.success) {
            $preview.html(response.data.content);
            // Add loading indicator for preview content
            _this5.addLoadingIndicatorToPreview($preview);
          } else {
            $preview.html('<p class="error">' + buddybossSharingFrontend.i18n.failedLoadActivity + '</p>');
          }
        },
        error: function error() {
          $preview.html('<p class="error">' + buddybossSharingFrontend.i18n.failedLoadActivity + '</p>');
        }
      });
    },
    /**
     * Add loading indicator to shared activity preview content
     */
    addLoadingIndicatorToPreview: function addLoadingIndicatorToPreview($container) {
      // Find shared-activity-content and activity-link-preview-container
      var $sharedContent = $container.find('.shared-activity-content');
      var $linkPreview = $container.find('.activity-link-preview-container');
      var $youtubeEmbed = $container.find('.bb-video-wrapper');

      // Add loading indicator to shared-activity-content if it exists
      if ($sharedContent.length) {
        $sharedContent.addClass('shared-activity-content-loading');
        $sharedContent.prepend('<span class="activity-url-scrapper-loading activity-ajax-loader">' + '<i class="bb-icon-l bb-icon-spinner animate-spin"></i> ' + (buddybossSharingFrontend.i18n.loadingPreview || 'Loading preview...') + '</span>');
      }

      // Add loading indicator to link preview container if it exists
      if ($linkPreview.length) {
        $linkPreview.addClass('activity-link-preview-loading');
        $linkPreview.prepend('<span class="activity-url-scrapper-loading activity-ajax-loader">' + '<i class="bb-icon-l bb-icon-spinner animate-spin"></i> ' + (buddybossSharingFrontend.i18n.loadingPreview || 'Loading preview...') + '</span>');
      }

      // Add loading indicator to YouTube embed if it exists
      if ($youtubeEmbed.length) {
        $youtubeEmbed.addClass('bb-video-wrapper-loading');
        $youtubeEmbed.prepend('<span class="activity-url-scrapper-loading activity-ajax-loader">' + '<i class="bb-icon-l bb-icon-spinner animate-spin"></i> ' + (buddybossSharingFrontend.i18n.loadingPreview || 'Loading preview...') + '</span>');
      }

      // Wait for images and videos to load, then remove loading indicator
      this.waitForPreviewContentToLoad($container);
    },
    /**
     * Wait for preview content (images/videos) to load and remove loading indicator
     */
    waitForPreviewContentToLoad: function waitForPreviewContentToLoad($container) {
      var _this6 = this;
      // First, force lazy images to load immediately
      this.forceLazyImagesToLoad($container);

      // Wait a bit for lazy loading to initialize
      setTimeout(function () {
        var $images = $container.find('img');
        var $videos = $container.find('video, iframe');
        var imagesLoaded = 0;
        var videosLoaded = 0;
        var totalImages = $images.length;
        var totalVideos = $videos.length;

        // Check if all content has loaded - define this BEFORE it's used
        var checkIfAllLoaded = function checkIfAllLoaded() {
          if (imagesLoaded >= totalImages && videosLoaded >= totalVideos) {
            setTimeout(function () {
              _this6.removeLoadingIndicator($container);
            }, 300); // Small delay to ensure smooth transition
          }
        };

        // If no images or videos, remove loading indicator immediately
        if (totalImages === 0 && totalVideos === 0) {
          setTimeout(function () {
            _this6.removeLoadingIndicator($container);
          }, 100);
          return;
        }

        // Track image loading
        if (totalImages > 0) {
          $images.each(function (index) {
            var $img = $(this);

            // Force load lazy images
            if ($img.hasClass('lazy') && $img.attr('data-src')) {
              var dataSrc = $img.attr('data-src');
              $img.attr('src', dataSrc).removeAttr('data-src').removeClass('lazy');
            }

            // If image is already loaded
            if ($img[0].complete && $img[0].naturalHeight !== 0) {
              imagesLoaded++;
              checkIfAllLoaded();
            } else {
              // Wait for image to load
              $img.one('load error', function () {
                imagesLoaded++;
                checkIfAllLoaded();
              });

              // Force trigger load if already cached
              if ($img[0].complete) {
                $img.trigger('load');
              }
            }
          });
        }

        // Track video/iframe loading
        if (totalVideos > 0) {
          $videos.each(function (index) {
            var $video = $(this);

            // For iframes (YouTube embeds), use multiple detection methods
            if ($video.is('iframe')) {
              var iframeLoaded = false;
              var iframeSrc = $video.attr('src');
              var iframeDataSrc = $video.attr('data-src');

              // Check if iframe has data-src (lazy loading)
              if (iframeDataSrc && !iframeSrc) {
                $video.attr('src', iframeDataSrc).removeAttr('data-src');
                iframeSrc = iframeDataSrc;
              }

              // If iframe doesn't have src yet, wait for it to be set
              if (!iframeSrc) {
                // Watch for src attribute changes
                var checkCount = 0;
                var maxChecks = 10;
                var _checkForSrc = function checkForSrc() {
                  var currentSrc = $video.attr('src') || $video.attr('data-src');
                  if (currentSrc) {
                    // If it's data-src, convert it to src
                    if ($video.attr('data-src') && !$video.attr('src')) {
                      $video.attr('src', currentSrc).removeAttr('data-src').removeAttr('data-lazy-type').removeClass('lazy');
                    }
                    // Src is set, now wait for it to load
                    setupIframeLoadDetection();
                  } else {
                    // Check again after a short delay (max 10 times = 2 seconds)
                    checkCount++;
                    if (checkCount < maxChecks) {
                      setTimeout(_checkForSrc, 200);
                    } else {
                      iframeLoaded = true;
                      videosLoaded++;
                      checkIfAllLoaded();
                    }
                  }
                };

                // Start checking for src
                setTimeout(_checkForSrc, 100);

                // Also set up load detection in case src gets set
                var setupIframeLoadDetection = function setupIframeLoadDetection() {
                  // Wait for load event
                  $video.one('load', function () {
                    if (!iframeLoaded) {
                      iframeLoaded = true;
                      videosLoaded++;
                      checkIfAllLoaded();
                    }
                  });

                  // For YouTube iframes, if src is set and visible, consider it loading
                  var isVisible = $video.is(':visible');

                  // Give it time to load - YouTube iframes may not fire load event reliably
                  setTimeout(function () {
                    if (!iframeLoaded) {
                      iframeLoaded = true;
                      videosLoaded++;
                      checkIfAllLoaded();
                    }
                  }, 1500);
                };
              } else {
                // Iframe already has src
                // Check if iframe is already loaded
                try {
                  var _$video$0$contentWind;
                  // Try to access iframe content (may fail due to CORS)
                  var iframeDoc = $video[0].contentDocument || ((_$video$0$contentWind = $video[0].contentWindow) === null || _$video$0$contentWind === void 0 ? void 0 : _$video$0$contentWind.document);
                  if (iframeDoc && iframeDoc.readyState === 'complete') {
                    iframeLoaded = true;
                    videosLoaded++;
                    checkIfAllLoaded();
                  }
                } catch (e) {
                  // CORS error - use load event instead
                }
                if (!iframeLoaded) {
                  // Wait for load event
                  $video.one('load', function () {
                    if (!iframeLoaded) {
                      iframeLoaded = true;
                      videosLoaded++;
                      checkIfAllLoaded();
                    }
                  });

                  // For YouTube iframes, if src is set and visible, consider it loading
                  var isVisible = $video.is(':visible');

                  // Give it time to load - YouTube iframes may not fire load event reliably
                  setTimeout(function () {
                    if (!iframeLoaded) {
                      iframeLoaded = true;
                      videosLoaded++;
                      checkIfAllLoaded();
                    }
                  }, 1500);
                }
              }
            } else {
              // For video elements
              $video.one('loadeddata canplay', function () {
                videosLoaded++;
                checkIfAllLoaded();
              });
            }
          });
        }

        // Fallback: Remove loading indicator after 3 seconds even if not all loaded
        setTimeout(function () {
          _this6.removeLoadingIndicator($container);
        }, 3000);
      }, 100);
    },
    /**
     * Force lazy images and iframes to load immediately
     */
    forceLazyImagesToLoad: function forceLazyImagesToLoad($container) {
      // Force lazy images to load
      var $lazyImages = $container.find('img.lazy[data-src]');
      $lazyImages.each(function () {
        var $img = $(this);
        var dataSrc = $img.attr('data-src');
        if (dataSrc) {
          // Set the actual image source
          $img.attr('src', dataSrc).removeAttr('data-src').removeClass('lazy');

          // Trigger lazy load event for platform compatibility
          $(document).trigger('bp_nouveau_lazy_load', {
            element: this
          });
        }
      });

      // Force lazy iframes to load
      var $lazyIframes = $container.find('iframe.lazy[data-src], iframe[data-lazy-type="iframe"][data-src]');
      $lazyIframes.each(function () {
        var $iframe = $(this);
        var dataSrc = $iframe.attr('data-src');
        if (dataSrc) {
          // Set the actual iframe source
          $iframe.attr('src', dataSrc).removeAttr('data-src').removeAttr('data-lazy-type').removeClass('lazy');

          // Trigger lazy load event for platform compatibility
          $(document).trigger('bp_nouveau_lazy_load', {
            element: this
          });
        }
      });

      // Also trigger platform's lazy load function if available
      if (typeof bp !== 'undefined' && bp.Nouveau && typeof bp.Nouveau.lazyLoad === 'function') {
        bp.Nouveau.lazyLoad($container.find('img.lazy, iframe.lazy'));
      }
    },
    /**
     * Remove loading indicator from preview content
     */
    removeLoadingIndicator: function removeLoadingIndicator($container) {
      $container.find('.activity-url-scrapper-loading').remove();
      $container.find('.shared-activity-content-loading').removeClass('shared-activity-content-loading');
      $container.find('.activity-link-preview-loading').removeClass('activity-link-preview-loading');
      $container.find('.bb-video-wrapper-loading').removeClass('bb-video-wrapper-loading');
    },
    /**
     * Handle activity share submit
     */
    handleActivityShareSubmit: function handleActivityShareSubmit(e) {
      var _this7 = this;
      e.preventDefault();
      var $btn = $(e.currentTarget);

      // Get form data
      var customMessage = $('#share-activity-message').val() || '';
      var privacy = $('#share-activity-privacy').val() || 'public';

      // Disable button and show loading
      $btn.prop('disabled', true).text('Posting...');

      // Submit via AJAX
      // Extract topic_id and has_topic_selector if topic selector exists (same as BuddyBoss Platform)
      var topicId = 0;
      var hasTopicSelector = false;
      var topicSelector = $('#buddypress .whats-new-topic-selector .bb-topic-selector-list li');
      console.log('[BuddyBoss Share Debug] Topic selector elements found:', topicSelector.length);
      if (topicSelector.length) {
        hasTopicSelector = true;
        var selectedTopic = topicSelector.find('a.selected');
        console.log('[BuddyBoss Share Debug] Selected topic links:', selectedTopic.length);
        topicId = selectedTopic.data('topic-id') || 0;
        console.log('[BuddyBoss Share Debug] Final values - has_topic_selector:', hasTopicSelector, ', topic_id:', topicId);
      }
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_share_to_feed',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: this.currentActivityId,
          custom_message: customMessage,
          privacy: privacy,
          has_topic_selector: hasTopicSelector,
          topic_id: topicId
        },
        success: function success(response) {
          if (response.success) {
            // Clear draft activity
            _this7.clearDraftActivity();
            _this7.showNotification(response.data.message || buddybossSharingFrontend.i18n.sharedSuccess, 'success');

            // Update share count
            if (response.data.share_count) {
              _this7.updateShareCount(response.data.share_count);
            }

            // Close modal
            setTimeout(function () {
              _this7.closeActivityModal();
            }, 1000);
          } else {
            _this7.showNotification(response.data.message || buddybossSharingFrontend.i18n.error, 'error');
            $btn.prop('disabled', false).text('Post');
          }
        },
        error: function error() {
          _this7.showNotification(buddybossSharingFrontend.i18n.error, 'error');
          $btn.prop('disabled', false).text('Post');
        }
      });
    },
    /**
     * Load share content via AJAX
     */
    loadShareContent: function loadShareContent() {
      var _this8 = this;
      var $contentArea = $('.share-content-area');
      $contentArea.html('<div class="share-content-loading"><span class="spinner is-active"></span></div>');
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_get_share_modal_content',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: this.currentActivityId,
          share_type: this.currentShareType
        },
        success: function success(response) {
          if (response.success) {
            $contentArea.html(response.data.content);

            // Update modal title based on share type
            _this8.updateModalTitle();

            // If message type, load recent threads
            if (_this8.currentShareType === 'message') {
              _this8.loadRecentThreads();
            }
          } else {
            _this8.showError(response.data.message || buddybossSharingFrontend.i18n.error);
          }
        },
        error: function error() {
          _this8.showError(buddybossSharingFrontend.i18n.error);
        }
      });
    },
    /**
     * Update modal title based on share type
     */
    updateModalTitle: function updateModalTitle() {
      var $title = $('.buddyboss-modal-title');
      var titleText = 'Create a post';
      switch (this.currentShareType) {
        case 'group':
          titleText = 'Share to a group';
          break;
        case 'profile':
          titleText = "Share to friend's profile";
          break;
        case 'message':
          titleText = 'Share to message';
          break;
      }
      $title.text(titleText);
    },
    /**
     * Share directly (without custom message)
     */
    shareDirectly: function shareDirectly() {
      var _this9 = this;
      // Extract topic_id and has_topic_selector if topic selector exists (same as BuddyBoss Platform)
      var topicId = 0;
      var hasTopicSelector = false;
      var topicSelector = $('#buddypress .whats-new-topic-selector .bb-topic-selector-list li');
      console.log('[BuddyBoss Share Debug] Topic selector elements found:', topicSelector.length);
      if (topicSelector.length) {
        hasTopicSelector = true;
        var selectedTopic = topicSelector.find('a.selected');
        console.log('[BuddyBoss Share Debug] Selected topic links:', selectedTopic.length);
        topicId = selectedTopic.data('topic-id') || 0;
        console.log('[BuddyBoss Share Debug] Final values - has_topic_selector:', hasTopicSelector, ', topic_id:', topicId);
      }
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_share_to_feed',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: this.currentActivityId,
          custom_message: '',
          has_topic_selector: hasTopicSelector,
          topic_id: topicId
        },
        success: function success(response) {
          if (response.success) {
            // Show success notification
            _this9.showNotification(response.data.message || buddybossSharingFrontend.i18n.sharedSuccess, 'success');

            // Update share count
            if (response.data.share_count) {
              _this9.updateShareCount(response.data.share_count);
            }

            // Inject activity into stream (same way BuddyBoss does it)
            // BUT: Don't inject on single activity pages - only on feed pages
            var isSingleActivityPage = $('body').hasClass('activity-singular');
            if (response.data.activity && typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.inject && !isSingleActivityPage) {
              var store = bp.Nouveau.getStorage('bp-activity');
              var searchTerms = $('[data-bp-search="activity"] input[type="search"]').val();
              var toPrepend = false;

              // Check if search terms match
              if (searchTerms) {
                var matches = response.data.activity.match(new RegExp(searchTerms, 'im'));
                if (!matches) toPrepend = false;else toPrepend = true;
              } else {
                toPrepend = true;
              }

              // Check filter
              if (toPrepend && store.filter && parseInt(store.filter, 10) !== 0 && store.filter !== 'activity_update' && store.filter !== 'activity_share') {
                toPrepend = false;
              }

              // Check scope
              if (toPrepend && response.data.is_directory && store.scope !== 'all') {
                toPrepend = false;
              }

              // Inject if appropriate
              if (toPrepend && !$('#activity-' + response.data.id).length) {
                if (!$('#activity-stream ul.activity-list').length) {
                  $('#activity-stream').html($('<ul></ul>').addClass('activity-list item-list bp-list'));
                }
                var pinnedActivity = $('#activity-stream ul.activity-list li:first.bb-pinned');
                if (pinnedActivity.length > 0) {
                  bp.Nouveau.inject('#activity-stream ul.activity-list li:first.bb-pinned', response.data.activity, 'after');
                } else {
                  bp.Nouveau.inject('#activity-stream ul.activity-list', response.data.activity, 'prepend');
                }
                jQuery(window).scroll();
              }
            }

            // On single activity pages, remove any activity that might have been added to the stream
            if (isSingleActivityPage && response.data.id) {
              // Remove the activity if it was added
              $('#activity-' + response.data.id).remove();
              // Also check for any activities with the share action type
              $('#activity-stream li.activity-item[data-bp-activity-id="' + response.data.id + '"]').remove();
            }
          } else {
            _this9.showNotification(response.data.message || buddybossSharingFrontend.i18n.error, 'error');
          }
        },
        error: function error() {
          _this9.showNotification(buddybossSharingFrontend.i18n.error, 'error');
        }
      });
    },
    /**
     * Share directly to group (without custom message)
     */
    shareDirectlyToGroup: function shareDirectlyToGroup() {
      var _this0 = this;
      if (!this.selectedGroup || !this.selectedGroup.id) {
        console.error('No group selected');
        return;
      }
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_share_to_group',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: this.currentActivityId,
          group_id: this.selectedGroup.id,
          custom_message: ''
        },
        success: function success(response) {
          if (response.success) {
            // Show success notification
            _this0.showNotification(response.data.message || 'Shared to group successfully!', 'success');

            // Update share count
            if (response.data.share_count) {
              _this0.updateShareCount(response.data.share_count);
            }

            // Reset selection
            _this0.selectedGroup = null;
          } else {
            _this0.showNotification(response.data.message || 'Failed to share to group.', 'error');
          }
        },
        error: function error() {
          _this0.showNotification('An error occurred. Please try again.', 'error');
        }
      });
    },
    /**
     * Share directly to friend's profile (without custom message)
     */
    shareDirectlyToFriend: function shareDirectlyToFriend() {
      var _this1 = this;
      if (!this.selectedFriend || !this.selectedFriend.id) {
        console.error('No friend selected');
        return;
      }
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_share_to_friend_profile',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: this.currentActivityId,
          friend_id: this.selectedFriend.id,
          custom_message: ''
        },
        success: function success(response) {
          if (response.success) {
            // Show success notification
            _this1.showNotification(response.data.message || buddybossSharingFrontend.i18n.sharedSuccess, 'success');

            // Update share count
            if (response.data.share_count) {
              _this1.updateShareCount(response.data.share_count);
            }

            // Reset selection
            _this1.selectedFriend = null;
          } else {
            _this1.showNotification(response.data.message || buddybossSharingFrontend.i18n.error, 'error');
          }
        },
        error: function error() {
          _this1.showNotification(buddybossSharingFrontend.i18n.error, 'error');
        }
      });
    },
    /**
     * Handle platform click (Share as Link)
     */
    handlePlatformClick: function handlePlatformClick(e) {
      var _this10 = this;
      e.preventDefault();
      var $btn = $(e.currentTarget);
      var platform = $btn.data('platform');
      // Get URL from the input field (already loaded by openLinkModal)
      var activityUrl = $('.share-link-url-input').val();
      var shareUrl = '';
      switch (platform) {
        case 'facebook':
          shareUrl = "https://www.facebook.com/sharer/sharer.php?u=".concat(encodeURIComponent(activityUrl));
          break;
        case 'twitter':
          shareUrl = "https://twitter.com/intent/tweet?url=".concat(encodeURIComponent(activityUrl));
          break;
        case 'linkedin':
          shareUrl = "https://www.linkedin.com/sharing/share-offsite/?url=".concat(encodeURIComponent(activityUrl));
          break;
        case 'whatsapp':
          shareUrl = "https://wa.me/?text=".concat(encodeURIComponent(activityUrl));
          break;
        case 'messenger':
          shareUrl = "https://www.facebook.com/dialog/send?link=".concat(encodeURIComponent(activityUrl), "&app_id=YOUR_APP_ID&redirect_uri=").concat(encodeURIComponent(window.location.href));
          break;
      }
      if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');

        // Close modal after opening share window
        setTimeout(function () {
          _this10.closeModal();
        }, 500);
      }
    },
    /**
     * Handle copy link button click
     */
    handleCopyLink: function handleCopyLink(e) {
      e.preventDefault();
      var $input = $('.share-link-url-input');
      var $btn = $(e.currentTarget);
      var originalText = $btn.find('span').text();

      // Select and copy the URL
      $input.select();
      document.execCommand('copy');

      // Show copied feedback
      $btn.find('span').text('Copied!');
      $btn.addClass('copied');

      // Reset button text after 2 seconds
      setTimeout(function () {
        $btn.find('span').text(originalText);
        $btn.removeClass('copied');
      }, 2000);
    },
    /**
     * Handle share submit
     */
    handleShareSubmit: function handleShareSubmit(e) {
      var _this11 = this;
      e.preventDefault();
      var $btn = $(e.currentTarget);

      // Get custom message - for message type, use dedicated textarea
      var customMessage = '';
      if (this.currentShareType === 'message') {
        customMessage = $('#share-message-content').val() || '';
      } else {
        customMessage = $('.share-custom-message').val() || '';
      }

      // Get share target - handle both single (radio) and multiple (checkbox) selection
      var shareTarget;
      if (this.currentShareType === 'message') {
        // For message type, collect multiple recipients (checkboxes)
        shareTarget = $('input[name="share_target[]"]:checked').map(function () {
          return $(this).val();
        }).get();
      } else {
        // For other types, get single radio value
        shareTarget = $('input[name="share_target"]:checked').val() || '';
      }

      // Validate if target is required
      if (this.currentShareType === 'group' || this.currentShareType === 'profile') {
        if (!shareTarget) {
          this.showError(buddybossSharingFrontend.i18n.selectTarget);
          return;
        }
      } else if (this.currentShareType === 'message') {
        if (!shareTarget || shareTarget.length === 0) {
          this.showError(buddybossSharingFrontend.i18n.selectRecipient);
          return;
        }
      }

      // Disable button and show loading
      $btn.prop('disabled', true).html('<span class="spinner is-active"></span> Sharing...');

      // Submit via AJAX
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_share_activity',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: this.currentActivityId,
          share_type: this.currentShareType,
          share_target: shareTarget,
          custom_message: customMessage
        },
        success: function success(response) {
          if (response.success) {
            // Clear draft activity
            _this11.clearDraftActivity();
            _this11.showSuccess(response.data.message || buddybossSharingFrontend.i18n.sharedSuccess);

            // Update share count
            if (response.data.share_count) {
              _this11.updateShareCount(response.data.share_count);
            }

            // Close modal
            setTimeout(function () {
              _this11.closeModal();
            }, 1500);
          } else {
            _this11.showError(response.data.message || buddybossSharingFrontend.i18n.error);
            $btn.prop('disabled', false).html('<i class="bb-icon-clock"></i> Post');
          }
        },
        error: function error() {
          _this11.showError(buddybossSharingFrontend.i18n.error);
          $btn.prop('disabled', false).html('<i class="bb-icon-clock"></i> Post');
        }
      });
    },
    /**
     * Handle search in lists
     */
    handleSearch: function handleSearch(e) {
      var searchTerm = $(e.currentTarget).val().toLowerCase();
      var $items = $('.share-target-item');
      if (!searchTerm) {
        $items.show();
        return;
      }
      $items.each(function () {
        var text = $(this).find('.target-name').text().toLowerCase();
        if (text.includes(searchTerm)) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });

      // Show/hide no results message
      var visibleCount = $items.filter(':visible').length;
      var $noResults = $('.no-results');
      if (visibleCount === 0 && $noResults.length === 0) {
        $('.share-target-list').append('<p class="no-results">No results found.</p>');
      } else if (visibleCount > 0) {
        $noResults.remove();
      }
    },
    /**
     * Handle keyboard events
     */
    handleKeyPress: function handleKeyPress(e) {
      // ESC key
      if (e.keyCode === 27) {
        var $messageModalContainer = $('.buddyboss-share-message-modal-container');
        if (this.activityModal.is(':visible')) {
          this.closeActivityModal();
        } else if ($messageModalContainer.is(':visible')) {
          this.closeModal();
        } else if (this.modal.is(':visible') || this.linkModal.is(':visible')) {
          this.closeModal();
        } else if (this.dropdown.is(':visible')) {
          this.closeDropdown();
        } else {
          // Check if we're in an activity sharing context and cleanup
          if (this.currentActivityId && (this.currentShareType === 'feed' || this.currentShareType === 'group' || this.currentShareType === 'profile')) {
            if ($('#whats-new-form').find('.buddyboss-shared-activity-preview').length > 0) {
              // Call cleanup function based on share type
              if (this.currentShareType === 'feed') {
                this.cleanupSharedActivityForm();
              } else if (this.currentShareType === 'group') {
                this.cleanupSharedActivityFormForGroup();
              } else if (this.currentShareType === 'profile') {
                this.cleanupSharedActivityFormForFriend();
              }
            }
          }
        }
      }
    },
    /**
     * Handle form reset
     */
    handleFormReset: function handleFormReset(e) {
      // Check if we're in an activity sharing context
      if (this.currentActivityId && (this.currentShareType === 'feed' || this.currentShareType === 'group' || this.currentShareType === 'profile')) {
        // Check if the shared activity preview exists in the form
        if ($('#whats-new-form').find('.buddyboss-shared-activity-preview').length > 0) {
          // Call cleanup function based on share type
          if (this.currentShareType === 'feed') {
            this.cleanupSharedActivityForm();
          } else if (this.currentShareType === 'group') {
            this.cleanupSharedActivityFormForGroup();
          } else if (this.currentShareType === 'profile') {
            this.cleanupSharedActivityFormForFriend();
          }
        }
      }
    },
    /**
     * Get activity URL
     */
    getActivityUrl: function getActivityUrl() {
      // Build activity permalink
      var activityPermalink = $("[data-activity-id=\"".concat(this.currentActivityId, "\"]")).closest('.activity-item').find('.activity-permalink');
      if (activityPermalink.length) {
        return activityPermalink.attr('href');
      }
      return window.location.href;
    },
    /**
     * Update share count in UI
     */
    updateShareCount: function updateShareCount(count) {
      // Update the share count
      var $activityStateShares = $(".activity-state-shares[data-activity-id=\"".concat(this.currentActivityId, "\"]"));
      var $sharesCountNumber = $activityStateShares.find('.shares-count-number');
      var $sharesCount = $activityStateShares.find('.shares-count');
      if ($sharesCountNumber.length > 0) {
        // Update the number
        $sharesCountNumber.text(count);

        // Update data attribute
        $sharesCount.attr('data-shares-count', count);

        // Update singular/plural label if needed
        if (count === 1) {
          $sharesCount.html('<span class="shares-count-number">' + count + '</span> Share');
        } else {
          $sharesCount.html('<span class="shares-count-number">' + count + '</span> Shares');
        }

        // Show/hide based on count
        if (count === 0) {
          $activityStateShares.hide();
        } else {
          $activityStateShares.show();
        }
      }
    },
    /**
     * Show notification using platform's toast system
     */
    showNotification: function showNotification(message) {
      var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'success';
      // Map type to platform's toast type
      var toastType = 'success';
      if (type === 'error') {
        toastType = 'error';
      } else if (type === 'warning') {
        toastType = 'warning';
      }

      // Use platform's toast notification system
      $(document).trigger('bb_trigger_toast_message', ['',
      // title (optional)
      message,
      // message
      toastType,
      // type
      null,
      // url (optional)
      true,
      // autoHide
      5 // autohideInterval in seconds
      ]);
    },
    /**
     * Show success message (in modal)
     */
    showSuccess: function showSuccess(message) {
      var $alert = $("\n\t\t\t\t<div class=\"buddyboss-alert buddyboss-alert-success\">\n\t\t\t\t\t<i class=\"bb-icon-check\"></i>\n\t\t\t\t\t".concat(message, "\n\t\t\t\t</div>\n\t\t\t"));
      $('.share-content-area').prepend($alert);
      setTimeout(function () {
        $alert.fadeOut(300, function () {
          $(this).remove();
        });
      }, 3000);
    },
    /**
     * Show error message (in modal)
     */
    showError: function showError(message) {
      var $alert = $("\n\t\t\t\t<div class=\"buddyboss-alert buddyboss-alert-error\">\n\t\t\t\t\t<i class=\"bb-icon-exclamation-triangle\"></i>\n\t\t\t\t\t".concat(message, "\n\t\t\t\t</div>\n\t\t\t"));
      $('.share-content-area').prepend($alert);
      setTimeout(function () {
        $alert.fadeOut(300, function () {
          $(this).remove();
        });
      }, 5000);
    },
    /**
     * Handle message send button click
     */
    handleMessageSend: function handleMessageSend(e) {
      var _this12 = this;
      e.preventDefault();
      var $btn = $(e.currentTarget);
      // Check if we have any selected recipients
      if (!this.selectedRecipients || this.selectedRecipients.length === 0) {
        this.showNotification('Please select at least one recipient or thread.', 'error');
        return;
      }

      // Separate thread IDs from member IDs based on the isThread flag
      var threadIds = [];
      var memberIds = [];
      this.selectedRecipients.forEach(function (recipient) {
        if (recipient.isThread) {
          threadIds.push(recipient.id);
        } else {
          memberIds.push(recipient.id);
        }
      });
      // Get activity ID from hidden field
      var activityId = $('#share-message-activity-id').val();
      if (!activityId) {
        this.showNotification('Activity ID not found.', 'error');
        return;
      }

      // Get additional message if textarea exists
      var additionalMessage = $('.share-message-textarea').val() || '';
      // Disable button and show loading
      $btn.prop('disabled', true).text('Sending...');
      // Submit via AJAX
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_share_to_message',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: activityId,
          thread_ids: threadIds,
          member_ids: memberIds,
          custom_message: additionalMessage
        },
        success: function success(response) {
          if (response.success) {
            _this12.showNotification(response.data.message || 'Message sent successfully!', 'success');

            // Update share count
            if (response.data.share_count) {
              _this12.updateShareCount(response.data.share_count);
            }

            // Clear selected recipients and reset chips
            _this12.selectedRecipients = [];
            _this12.renderSelectedRecipients();

            // Clear the textarea if it exists
            $('.share-message-textarea').val('');

            // Close modal
            setTimeout(function () {
              _this12.closeModal();
            }, 1000);
          } else {
            _this12.showNotification(response.data.message || buddybossSharingFrontend.i18n.failedSendMessage, 'error');
            $btn.prop('disabled', false).text('Send');
          }
        },
        error: function error() {
          _this12.showNotification('An error occurred. Please try again.', 'error');
          $btn.prop('disabled', false).text('Send');
        }
      });
    },
    /**
     * Load recent message threads
     */
    loadRecentThreads: function loadRecentThreads() {
      var _this13 = this;
      var append = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      // Prevent multiple simultaneous requests
      if (this.isLoadingThreads) {
        return;
      }
      var $loading = $('.share-message-loading');
      var $results = $('.share-message-results');

      // If not appending, reset pagination state
      if (!append) {
        this.currentPage = 1;
        this.hasMorePages = false;
        $results.empty();
      }
      this.isLoadingThreads = true;

      // Show loading
      $loading.show();

      // Use BuddyBoss native endpoint to get threads
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_get_recent_message_threads',
          nonce: buddybossSharingFrontend.messagesNonce || buddybossSharingFrontend.nonce,
          box: 'inbox',
          page: append ? this.currentPage + 1 : 1,
          per_page: 100
        },
        success: function success(response) {
          $loading.hide();
          _this13.isLoadingThreads = false;
          if (response.success && response.data && response.data.threads && response.data.threads.length > 0) {
            // Update pagination state
            _this13.currentPage = response.data.page || 1;
            _this13.hasMorePages = response.data.has_more || false;
            var html = '';
            var currentUserId = buddybossSharingFrontend.currentUserId;
            response.data.threads.forEach(function (thread) {
              // Build thread display based on type
              var avatarHtml = '';
              var nameHtml = '';
              var threadId = thread.thread_id;

              // Check if this is a group thread
              if (thread.is_group_thread && thread.group_avatar && thread.group_name) {
                // Display group icon and name
                avatarHtml = "<img src=\"".concat(thread.group_avatar, "\" alt=\"").concat(thread.group_name, "\" class=\"avatar group-avatar\" />");
                nameHtml = thread.group_name;
              } else if (thread.avatars && thread.avatars.length > 1) {
                // Multiple recipients - show 2 avatars
                if (thread.avatars.length === 2) {
                  avatarHtml = "\n\t\t\t\t\t\t\t\t\t\t<div class=\"thread-multiple-avatar\">\n\t\t\t\t\t\t\t\t\t\t\t<img src=\"".concat(thread.avatars[0].url, "\" alt=\"").concat(thread.avatars[0].name, "\" class=\"avatar\" />\n\t\t\t\t\t\t\t\t\t\t\t<img src=\"").concat(thread.avatars[1].url, "\" alt=\"").concat(thread.avatars[1].name, "\" class=\"avatar\" />\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t");
                } else {
                  // More than 2 avatars - show first with count badge
                  avatarHtml = "\n\t\t\t\t\t\t\t\t\t\t<div class=\"thread-avatar-with-count\">\n\t\t\t\t\t\t\t\t\t\t\t<span class=\"recipients-count\">".concat(thread.recipientsCount || thread.avatars.length, "</span>\n\t\t\t\t\t\t\t\t\t\t\t<img src=\"").concat(thread.avatars[0].url, "\" alt=\"").concat(thread.avatars[0].name, "\" class=\"avatar\" />\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t");
                }

                // Build names list (first 4 names + "X others")
                var recipientArray = Object.values(thread.recipients || {});
                var names = recipientArray.slice(0, 4).map(function (r) {
                  return r.name;
                });
                nameHtml = names.join(', ');
                if (thread.toOthers) {
                  nameHtml += ' ' + thread.toOthers;
                }
              } else {
                // Single recipient thread
                var _recipientArray = Object.values(thread.recipients || {});
                if (_recipientArray.length > 0) {
                  var recipient = _recipientArray[0];
                  avatarHtml = "<img src=\"".concat(recipient.avatar, "\" alt=\"").concat(recipient.name, "\" class=\"avatar\" />");
                  nameHtml = recipient.name;
                }
              }
              if (nameHtml) {
                html += "\n\t\t\t\t\t\t\t\t\t<label class=\"share-message-thread-item bb-share-modal-list-item\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"thread-avatar\">\n\t\t\t\t\t\t\t\t\t\t\t".concat(avatarHtml, "\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"thread-info\">\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"thread-name\">\n\t\t\t\t\t\t\t\t\t\t\t\t").concat(nameHtml, "\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"thread-checkbox\">\n\t\t\t\t\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\t\t\t\t\ttype=\"checkbox\"\n\t\t\t\t\t\t\t\t\t\t\t\tname=\"share_message_recipients[]\"\n\t\t\t\t\t\t\t\t\t\t\t\tvalue=\"").concat(threadId, "\"\n\t\t\t\t\t\t\t\t\t\t\t\tdata-thread-id=\"").concat(threadId, "\"\n\t\t\t\t\t\t\t\t\t\t\t\tclass=\"share-message-thread-checkbox\"\n\t\t\t\t\t\t\t\t\t\t\t/>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t\t");
              }
            });
            if (html) {
              // Append or replace based on mode
              if (append) {
                $results.append(html);
              } else {
                $results.html(html);
              }

              // Setup scroll listener after first load
              if (!append) {
                _this13.setupInfiniteScroll();
              }
            } else if (!append) {
              $results.html('<p class="no-members-found" style="text-align: center; padding: 24px; color: #8c8f94;">No recent conversations found.</p>');
            }
          } else if (!append) {
            $results.html('<p class="no-members-found" style="text-align: center; padding: 24px; color: #8c8f94;">No recent conversations found.</p>');
          }
        },
        error: function error() {
          $loading.hide();
          _this13.isLoadingThreads = false;
          if (!append) {
            $results.html('<p class="no-members-found" style="text-align: center; padding: 24px; color: #8c8f94;">Error loading recent conversations.</p>');
          }
        }
      });
    },
    /**
     * Setup infinite scroll for message threads list
     */
    setupInfiniteScroll: function setupInfiniteScroll() {
      var _this14 = this;
      var $membersList = $('.share-message-members-list');
      // Remove previous listener if exists
      $membersList.off('scroll.infiniteThreads');

      // Add scroll listener
      $membersList.on('scroll.infiniteThreads', function () {
        var scrollTop = $membersList.scrollTop();
        var scrollHeight = $membersList[0].scrollHeight;
        var clientHeight = $membersList.outerHeight();
        var distanceFromBottom = scrollHeight - scrollTop - clientHeight;
        // Check if near bottom (within 100px)
        if (distanceFromBottom < 100) {
          // Load more if has more pages and not currently loading
          if (_this14.hasMorePages && !_this14.isLoadingThreads) {
            _this14.loadRecentThreads(true); // true = append
          } else {}
        }
      });
    },
    /**
     * Handle message search
     */
    handleMessageSearch: function handleMessageSearch(e) {
      var _this15 = this;
      var searchTerm = $(e.currentTarget).val().trim();
      var $loading = $('.share-message-loading');
      var $results = $('.share-message-results');

      // Clear previous timeout if exists
      if (this.messageSearchTimeout) {
        clearTimeout(this.messageSearchTimeout);
      }

      // If search is empty, load recent threads
      if (!searchTerm) {
        this.loadRecentThreads();
        return;
      }

      // Debounce the search
      this.messageSearchTimeout = setTimeout(function () {
        // Show loading
        $loading.show();
        $results.empty();

        // Perform AJAX search
        $.ajax({
          url: buddybossSharingFrontend.ajaxUrl,
          type: 'POST',
          data: {
            action: 'buddyboss_search_members_for_message',
            nonce: buddybossSharingFrontend.nonce,
            search: searchTerm
          },
          success: function success(response) {
            $loading.hide();
            if (response.success && response.data.members && response.data.members.length > 0) {
              var html = '';
              response.data.members.forEach(function (member) {
                // Check if this member is already selected
                var isSelected = _this15.selectedRecipients && _this15.selectedRecipients.some(function (r) {
                  return r.id === member.id.toString();
                });
                html += '<label class="share-message-member-item bb-share-modal-list-item"><div class="member-avatar"><img src="' + member.avatar + '" alt="' + member.name + '" class="avatar" /></div><div class="member-name">' + member.name + '</div><div class="member-checkbox"><input type="checkbox" name="share_message_recipients[]" value="' + member.id + '" data-recipient-name="' + member.name + '" data-recipient-avatar="' + member.avatar + '" class="share-message-recipient-checkbox"' + (isSelected ? ' checked' : '') + ' /></div></label>';
              });
              $results.html(html);
            } else {
              $results.html('<p class="no-members-found" style="text-align: center; padding: 24px; color: #8c8f94;">No members found.</p>');
            }
          },
          error: function error() {
            $loading.hide();
            $results.html('<p class="no-members-found" style="text-align: center; padding: 24px; color: #8c8f94;">Error searching members. Please try again.</p>');
          }
        });
      }, 300); // 300ms debounce
    },
    /**
     * Initialize selected recipients tracking
     */
    initSelectedRecipients: function initSelectedRecipients() {
      var _this16 = this;
      this.selectedRecipients = [];

      // Listen for checkbox changes
      $(document).on('change', 'input[name="share_message_recipients[]"]', function (e) {
        var $checkbox = $(e.target);
        var recipientId = $checkbox.val();
        var recipientName = $checkbox.data('recipient-name') || $checkbox.closest('label').find('.thread-name, .member-name').text().trim();
        var recipientAvatar = $checkbox.data('recipient-avatar') || $checkbox.closest('label').find('img.avatar').first().attr('src');
        var isThread = $checkbox.hasClass('share-message-thread-checkbox');
        if ($checkbox.is(':checked')) {
          // Add to selected recipients
          _this16.addSelectedRecipient({
            id: recipientId,
            name: recipientName,
            avatar: recipientAvatar,
            isThread: isThread
          });
        } else {
          // Remove from selected recipients
          _this16.removeSelectedRecipient(recipientId);
        }
      });
    },
    /**
     * Add selected recipient
     */
    addSelectedRecipient: function addSelectedRecipient(recipient) {
      // Check if already exists
      var exists = this.selectedRecipients.some(function (r) {
        return r.id === recipient.id;
      });
      if (exists) {
        return;
      }
      this.selectedRecipients.push(recipient);
      this.renderSelectedRecipients();
    },
    /**
     * Remove selected recipient
     */
    removeSelectedRecipient: function removeSelectedRecipient(recipientId) {
      this.selectedRecipients = this.selectedRecipients.filter(function (r) {
        return r.id !== recipientId;
      });

      // Uncheck the checkbox if it exists
      $('input[name="share_message_recipients[]"][value="' + recipientId + '"]').prop('checked', false);
      this.renderSelectedRecipients();
    },
    /**
     * Render selected recipients as chips
     */
    renderSelectedRecipients: function renderSelectedRecipients() {
      var _this17 = this;
      var $container = $('.share-message-selected-container');
      if (this.selectedRecipients.length === 0) {
        $container.hide().empty();
        return;
      }
      var html = '';
      this.selectedRecipients.forEach(function (recipient) {
        // Use data-thread-id for threads, data-user-id for users
        var dataAttr = recipient.isThread ? 'data-thread-id' : 'data-user-id';
        html += '<div class="share-message-selected-chip" data-recipient-id="' + recipient.id + '" ' + dataAttr + '="' + recipient.id + '"><img src="' + recipient.avatar + '" alt="' + recipient.name + '" class="chip-avatar" /><span class="chip-name">' + recipient.name + '</span><button type="button" class="chip-remove" data-recipient-id="' + recipient.id + '"><i class="bb-icon-l bb-icon-times"></i></button></div>';
      });
      $container.html(html).show();

      // Add click handler for remove buttons
      $container.find('.chip-remove').off('click').on('click', function (e) {
        var recipientId = $(e.currentTarget).data('recipient-id').toString();
        _this17.removeSelectedRecipient(recipientId);
      });
    },
    /**
     * Save currently selected recipients
     */
    saveSelectedRecipients: function saveSelectedRecipients() {
      // This method is no longer needed as we track selections in real-time
      // Keeping it for backward compatibility
    },
    /**
     * Restore previously selected recipients
     */
    restoreSelectedRecipients: function restoreSelectedRecipients() {
      // Check checkboxes based on selectedRecipients array
      if (!this.selectedRecipients || this.selectedRecipients.length === 0) {
        return;
      }
      this.selectedRecipients.forEach(function (recipient) {
        $('input[name="share_message_recipients[]"][value="' + recipient.id + '"]').prop('checked', true);
      });
    },
    /**
     * Open group selection modal
     */
    openGroupModal: function openGroupModal() {
      var _this18 = this;
      // Remove any existing group modal container
      $('.buddyboss-share-group-modal-container').remove();

      // Create modal container with the group modal HTML structure
      var modalHtml = "\n\t\t\t\t<div class=\"buddyboss-share-group-modal-container bb-share-modal-container\" style=\"display: none;\">\n\t\t\t\t\t<div class=\"buddyboss-share-modal-overlay\"></div>\n\t\t\t\t\t<div class=\"buddyboss-share-group-modal bb-share-modal\">\n\t\t\t\t\t\t<div class=\"bb-share-modal-header\">\n\t\t\t\t\t\t\t<h3>Share to a group</h3>\n\t\t\t\t\t\t\t<button type=\"button\" class=\"share-group-close bb-share-modal-close\">\n\t\t\t\t\t\t\t\t<i class=\"bb-icon-l bb-icon-times\"></i>\n\t\t\t\t\t\t\t</button>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<div class=\"share-group-search bb-share-modal-search\">\n\t\t\t\t\t\t\t<i class=\"bb-icon-l bb-icon-search\"></i>\n\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\ttype=\"text\"\n\t\t\t\t\t\t\t\tclass=\"share-group-search-input\"\n\t\t\t\t\t\t\t\tplaceholder=\"Search for groups\"\n\t\t\t\t\t\t\t\tautocomplete=\"off\"\n\t\t\t\t\t\t\t/>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<div class=\"share-group-content bb-share-modal-content\">\n\t\t\t\t\t\t\t<div class=\"share-group-label bb-share-modal-label\">\n\t\t\t\t\t\t\t\tAll Groups\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t<div class=\"share-group-list\">\n\t\t\t\t\t\t\t\t<div class=\"share-group-loading\" style=\"display: none;\">\n\t\t\t\t\t\t\t\t\t<span class=\"spinner is-active\"></span>\n\t\t\t\t\t\t\t\t\t<p>Loading...</p>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"share-group-results\">\n\t\t\t\t\t\t\t\t\t<!-- Groups will be loaded via AJAX -->\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<!-- Hidden field to store activity ID -->\n\t\t\t\t\t\t<input type=\"hidden\" id=\"share-group-activity-id\" value=\"".concat(this.currentActivityId, "\" />\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t");
      $('body').append(modalHtml);
      var $container = $('.buddyboss-share-group-modal-container');

      // Show modal
      $container.fadeIn(300);
      $('body').css('overflow', 'hidden');

      // Bind close event
      $container.find('.share-group-close, .buddyboss-share-modal-overlay').on('click', function () {
        _this18.closeGroupModal();
      });

      // Load initial groups
      this.loadGroupsForSharing();

      // Setup search
      $container.find('.share-group-search-input').on('input', this.handleGroupSearch.bind(this));
    },
    /**
     * Close group selection modal
     */
    closeGroupModal: function closeGroupModal() {
      $('.buddyboss-share-group-modal-container').fadeOut(300, function () {
        $(this).remove();
      });
      $('body').css('overflow', '');
    },
    /**
     * Load groups for sharing
     */
    loadGroupsForSharing: function loadGroupsForSharing() {
      var _this19 = this;
      var searchTerm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      var page = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
      var $loading = $('.share-group-loading');
      var $results = $('.share-group-results');

      // Show loading
      $loading.show();
      if (page === 1) {
        $results.empty();
      }
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_get_groups_for_sharing',
          nonce: buddybossSharingFrontend.nonce,
          search: searchTerm,
          page: page
        },
        success: function success(response) {
          $loading.hide();
          if (response.success && response.data.groups && response.data.groups.length > 0) {
            var html = '';
            response.data.groups.forEach(function (group) {
              // Build meta text with status and type (if available)
              var metaText = group.status_label;
              if (group.type_label) {
                metaText += ' • ' + group.type_label;
              }
              html += "\n\t\t\t\t\t\t\t\t<div class=\"share-group-item bb-share-modal-list-item\" data-group-id=\"".concat(group.id, "\">\n\t\t\t\t\t\t\t\t\t<div class=\"group-avatar\">\n\t\t\t\t\t\t\t\t\t\t<img src=\"").concat(group.avatar, "\" alt=\"").concat(group.name, "\" class=\"avatar\" />\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"group-info\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"group-name\">").concat(group.name, "</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"group-meta\">").concat(metaText, "</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"bb-share-list-item-caret\">\n\t\t\t\t\t\t\t\t\t\t<i class=\"bb-icon-l bb-icon-angle-right\"></i>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t");
            });
            if (page === 1) {
              $results.html(html);
            } else {
              $results.append(html);
            }

            // Bind click events
            $results.find('.share-group-item').off('click').on('click', function (e) {
              var $item = $(e.currentTarget);
              var groupId = $item.data('group-id');
              var groupName = $item.find('.group-name').text();
              var groupAvatar = $item.find('.group-avatar img').attr('src');
              _this19.handleGroupSelection(groupId, groupName, groupAvatar);
            });
          } else if (page === 1) {
            var message = searchTerm ? 'No groups match your search.' : 'No groups found.';
            $results.html("\n\t\t\t\t\t\t\t<div class=\"no-groups-found\">\n\t\t\t\t\t\t\t\t<div class=\"no-groups-icon\">\n\t\t\t\t\t\t\t\t\t<i class=\"bb-icon-rf bb-icon-info-circle\"></i>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"no-groups-message\">".concat(message, "</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t"));
          }
        },
        error: function error() {
          $loading.hide();
          if (page === 1) {
            $results.html('<p class="no-groups-found" style="text-align: center; padding: 24px; color: #8c8f94;">Error loading groups. Please try again.</p>');
          }
        }
      });
    },
    /**
     * Handle group search
     */
    handleGroupSearch: function handleGroupSearch(e) {
      var _this20 = this;
      var searchTerm = $(e.currentTarget).val().trim();

      // Clear previous timeout if exists
      if (this.groupSearchTimeout) {
        clearTimeout(this.groupSearchTimeout);
      }

      // Debounce the search
      this.groupSearchTimeout = setTimeout(function () {
        _this20.loadGroupsForSharing(searchTerm, 1);
      }, 300); // 300ms debounce
    },
    /**
     * Handle group selection
     */
    handleGroupSelection: function handleGroupSelection(groupId, groupName, groupAvatar) {
      // Store selected group info
      this.selectedGroup = {
        id: groupId,
        name: groupName,
        avatar: groupAvatar
      };

      // Close group modal
      this.closeGroupModal();

      // Check if custom message is enabled
      if (buddybossSharingFrontend.enableCustomMsg) {
        // Add body class early to handle UI changes
        $('body').addClass('bb-sharing-active');

        // Open BuddyBoss native post form
        // Note: openBuddyBossPostFormWithGroup will handle form loading if needed
        // and will call loadAndInjectSharedActivityWithGroup after form is ready
        this.openBuddyBossPostFormWithGroup();
      } else {
        // Share directly without modal
        this.shareDirectlyToGroup();
      }
    },
    /**
     * Open BuddyBoss native post form with group pre-selected
     */
    openBuddyBossPostFormWithGroup: function openBuddyBossPostFormWithGroup() {
      var _this21 = this;
      // Clear any existing draft before opening share form
      this.clearDraftActivity();

      // Check if form exists, if not load it first
      var $form = $('#whats-new-form');
      var $formContainer = $('#bp-nouveau-activity-form');
      if (!$form.length || !$formContainer.length) {
        // Form doesn't exist, load it via AJAX
        this.loadActivityPostForm().then(function () {
          // Form loaded, now open it
          _this21.openBuddyBossPostForm();

          // Wait a moment for the form to expand, then set the group privacy
          setTimeout(function () {
            _this21.setGroupPrivacyInForm(_this21.selectedGroup);

            // Load and inject shared activity content after form is ready
            _this21.loadAndInjectSharedActivityWithGroup();
          }, 400);
        })["catch"](function (error) {
          console.error('Failed to load activity post form for group:', error);
          _this21.showNotification(buddybossSharingFrontend.i18n.error || 'Failed to load post form', 'error');
        });
      } else {
        // Form exists, proceed normally
        var $textarea = $('#whats-new');
        var $formWrapper = $form.closest('.activity-update-form');
        var $editFormWrap = $('#bp-nouveau-single-activity-edit-form-wrap');

        // If form is inside the hidden edit form wrap, show it
        if ($editFormWrap.length && $editFormWrap.is(':hidden')) {
          $editFormWrap.show();
        }

        // Ensure form wrapper is visible
        if ($formWrapper.length && $formWrapper.is(':hidden')) {
          $formWrapper.show();
        }

        // Ensure form is visible
        if ($form.length && $form.is(':hidden')) {
          $form.show();
        }
        if ($textarea.length) {
          // Wait a bit for form to be ready, then focus
          setTimeout(function () {
            // Check if form wrapper needs modal-popup class
            if ($formWrapper.length && !$formWrapper.hasClass('modal-popup')) {
              $formWrapper.addClass('modal-popup');
              $('body').addClass('activity-modal-open');
            }

            // Ensure form is still visible after adding classes
            if ($editFormWrap.length) {
              $editFormWrap.show();
            }
            if ($formWrapper.length) {
              $formWrapper.show();
            }
            if ($form.length) {
              $form.show();
            }

            // Focus the textarea to trigger displayFull
            $textarea.focus();

            // Wait a moment for the form to expand, then set the group privacy
            setTimeout(function () {
              _this21.setGroupPrivacyInForm(_this21.selectedGroup);

              // Load and inject shared activity content after form is ready
              _this21.loadAndInjectSharedActivityWithGroup();
            }, 300);
          }, 100);
        } else {
          console.warn('BuddyBoss post form not found');
        }
      }
    },
    /**
     * Set group privacy in BuddyBoss native form
     */
    setGroupPrivacyInForm: function setGroupPrivacyInForm(group) {
      // Access BuddyBoss's Backbone model for the post form
      if (typeof bp === 'undefined' || !bp.Nouveau || !bp.Nouveau.Activity || !bp.Nouveau.Activity.postForm) {
        console.warn('BuddyBoss Backbone model not available');
        return;
      }
      var model = bp.Nouveau.Activity.postForm.model;
      if (!model) {
        console.warn('BuddyBoss post form model not found');
        return;
      }
      // Set the required model properties for group posting
      model.set('item_id', group.id);
      model.set('privacy', 'group');
      model.set('item_name', group.name);
      model.set('group_name', group.name);
      model.set('group_image', group.avatar);
      model.set('object', 'groups');
      // Update the form UI
      var $whatsNewForm = $('#whats-new-form');

      // Update privacy status text
      $whatsNewForm.find('.bp-activity-privacy-status').text(group.name);

      // Update privacy point icon
      $whatsNewForm.find('#bp-activity-privacy-point').removeClass().addClass('group');

      // Update privacy icon to show group avatar (if available and not mystery group)
      if (group.avatar && !group.avatar.includes('mystery-group')) {
        $whatsNewForm.find('#bp-activity-privacy-point span.privacy-point-icon').removeClass('privacy-point-icon').addClass('group-privacy-point-icon').html("<img src=\"".concat(group.avatar, "\" alt=\"").concat(group.name, "\" />"));
      } else {
        $whatsNewForm.find('#bp-activity-privacy-point span.group-privacy-point-icon img').remove();
        $whatsNewForm.find('#bp-activity-privacy-point span.group-privacy-point-icon').removeClass('group-privacy-point-icon').addClass('privacy-point-icon');
      }

      // Remove focus-in classes
      $whatsNewForm.removeClass('focus-in--privacy focus-in--group');

      // Trigger Backbone event to update the privacy status
      if (typeof Backbone !== 'undefined') {
        Backbone.trigger('privacy:updatestatus');
      }
    },
    /**
     * Load and inject shared activity into BuddyBoss form (for group share)
     */
    loadAndInjectSharedActivityWithGroup: function loadAndInjectSharedActivityWithGroup() {
      var _this22 = this;
      // Wait for form to be visible
      setTimeout(function () {
        $.ajax({
          url: buddybossSharingFrontend.ajaxUrl,
          type: 'POST',
          data: {
            action: 'buddyboss_get_activity_content',
            nonce: buddybossSharingFrontend.nonce,
            activity_id: _this22.currentActivityId
          },
          success: function success(response) {
            if (response.success) {
              // Store the group ID as well
              _this22.injectSharedActivityToFormWithGroup(response.data.content);
            }
          },
          error: function error() {
            console.error(buddybossSharingFrontend.i18n.failedLoadActivity);
          }
        });
      }, 400);
    },
    /**
     * Inject shared activity content into BuddyBoss form (with group context)
     */
    injectSharedActivityToFormWithGroup: function injectSharedActivityToFormWithGroup(content) {
      var $form = $('#whats-new-form');
      // Store the shared activity ID in the form
      if (!$form.find('#buddyboss-shared-activity-id').length) {
        $form.append("<input type=\"hidden\" id=\"buddyboss-shared-activity-id\" value=\"".concat(this.currentActivityId, "\">"));
      }

      // Store the selected group ID
      if (!$form.find('#buddyboss-shared-group-id').length) {
        $form.append("<input type=\"hidden\" id=\"buddyboss-shared-group-id\" value=\"".concat(this.selectedGroup.id, "\">"));
      }

      // Inject the shared activity preview below the textarea
      var $textarea = $('#whats-new');
      var $previewContainer = $form.find('.buddyboss-shared-activity-preview');
      if ($previewContainer.length === 0) {
        $previewContainer = $('<div class="buddyboss-shared-activity-preview"></div>');
        $textarea.parent().after($previewContainer);
      }
      $previewContainer.html(content);

      // Initialize lazy loading for images
      this.initializeLazyLoading($previewContainer);

      // Initialize Video.js for any videos in preview
      this.initializeVideoJS($previewContainer);

      // Disable media/video/document/poll upload buttons
      this.disableUploadButtons();

      // Hook into form submission
      this.hookFormSubmissionForGroup();

      // Trigger validation to enable submit button when preview is added
      if (typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.Activity && bp.Nouveau.Activity.postForm && bp.Nouveau.Activity.postForm.postForm) {
        bp.Nouveau.Activity.postForm.postForm.postValidate();
      }
    },
    /**
     * Hook into BuddyBoss form submission for group share
     */
    hookFormSubmissionForGroup: function hookFormSubmissionForGroup() {
      var self = this;
      // Remove previous handler if exists
      $('#whats-new-form').off('submit.buddyboss-share-group');

      // Add new handler with native addEventListener to bind at capture phase
      var form = document.getElementById('whats-new-form');
      if (form) {
        // Remove any existing listener
        if (form._buddybossShareGroupHandler) {
          form.removeEventListener('submit', form._buddybossShareGroupHandler, true);
        }

        // Create and store handler
        form._buddybossShareGroupHandler = function (e) {
          var sharedActivityId = document.getElementById('buddyboss-shared-activity-id');
          var sharedGroupId = document.getElementById('buddyboss-shared-group-id');
          if (sharedActivityId && sharedActivityId.value && sharedGroupId && sharedGroupId.value) {
            // Stop ALL event propagation
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // Get the content from textarea
            var $whatsNew = $('#whats-new');
            var customMessage = '';
            if ($whatsNew.is('[contenteditable]')) {
              // Transform emoji images into emoji unicode (same as BuddyBoss Platform)
              $whatsNew.find('img.emojioneemoji, img.bb-rl-emojioneemoji').replaceWith(function () {
                return this.dataset.emojiChar;
              });

              // Use innerHTML like BuddyBoss Platform does to preserve emojis
              customMessage = $.trim($whatsNew[0].innerHTML.replace(/<div>/gi, '\n').replace(/<\/div>/gi, ''));
              customMessage = customMessage.replace(/&nbsp;/g, ' ');
            } else {
              customMessage = $whatsNew.val().trim();
            }
            // Submit via our AJAX handler
            self.submitSharedActivityToGroup(sharedActivityId.value, sharedGroupId.value, customMessage);
            return false;
          }
        };

        // Add listener at capture phase
        form.addEventListener('submit', form._buddybossShareGroupHandler, true);
      }
    },
    /**
     * Submit shared activity to group
     */
    submitSharedActivityToGroup: function submitSharedActivityToGroup(activityId, groupId, customMessage) {
      var _this23 = this;
      var $submitBtn = $('#aw-whats-new-submit');
      var originalBtnText = $submitBtn.text();

      // Disable submit button
      $submitBtn.prop('disabled', true).text('Posting...');
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_share_to_group',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: activityId,
          group_id: groupId,
          custom_message: customMessage
        },
        success: function success(response) {
          if (response.success) {
            _this23.showNotification(response.data.message || 'Shared to group successfully!', 'success');

            // Update share count
            if (response.data.share_count) {
              _this23.updateShareCount(response.data.share_count);
            }

            // Clear draft activity
            _this23.clearDraftActivity();

            // Reset and close form
            _this23.cleanupSharedActivityFormForGroup();
            $('#whats-new-form').trigger('reset');
          } else {
            _this23.showNotification(response.data.message || 'Failed to share to group.', 'error');
            $submitBtn.prop('disabled', false).text(originalBtnText);
          }
        },
        error: function error(xhr, status, _error4) {
          console.error('AJAX error:', {
            xhr: xhr,
            status: status,
            error: _error4
          });
          _this23.showNotification('An error occurred. Please try again.', 'error');
          $submitBtn.prop('disabled', false).text(originalBtnText);
        }
      });
    },
    /**
     * Cleanup shared activity form elements for group share
     */
    cleanupSharedActivityFormForGroup: function cleanupSharedActivityFormForGroup() {
      $('#buddyboss-shared-activity-id').remove();
      $('#buddyboss-shared-group-id').remove();
      $('.buddyboss-shared-activity-preview').remove();
      $('#whats-new-form').off('submit.buddyboss-share-group');
      $('body').removeClass('bb-sharing-active');

      // Remove native event listener
      var form = document.getElementById('whats-new-form');
      if (form && form._buddybossShareGroupHandler) {
        form.removeEventListener('submit', form._buddybossShareGroupHandler, true);
        delete form._buddybossShareGroupHandler;
      }

      // Show toolbar again
      var $toolbar = $('#whats-new-toolbar[data-share-disabled="true"]');
      if ($toolbar.length) {
        $toolbar.show().removeAttr('data-share-disabled');
      }

      // Hide the edit form wrap on single activity pages
      this.hideEditFormWrapOnSingleActivityPage();

      // Reset group selection
      this.selectedGroup = null;

      // Trigger validation to disable submit button when preview is removed
      if (typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.Activity && bp.Nouveau.Activity.postForm && bp.Nouveau.Activity.postForm.postForm) {
        bp.Nouveau.Activity.postForm.postForm.postValidate();
      }
    },
    /**
     * Open friends selection modal
     */
    openFriendsModal: function openFriendsModal() {
      var _this24 = this;
      // Remove any existing modal
      $('.buddyboss-share-friends-modal-container').remove();

      // Create modal HTML
      var modalHTML = "\n\t\t\t\t<div class=\"buddyboss-share-friends-modal-container bb-share-modal-container\" style=\"display: none;\">\n\t\t\t\t\t<div class=\"buddyboss-share-modal-overlay\"></div>\n\t\t\t\t\t<div class=\"buddyboss-share-friends-modal bb-share-modal\">\n\t\t\t\t\t\t<div class=\"share-friends-header bb-share-modal-header\">\n\t\t\t\t\t\t\t<h3>Share to friend's profile</h3>\n\t\t\t\t\t\t\t<button type=\"button\" class=\"share-friends-close bb-share-modal-close\">\n\t\t\t\t\t\t\t\t<i class=\"bb-icon-l bb-icon-times\"></i>\n\t\t\t\t\t\t\t</button>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<div class=\"share-friends-search bb-share-modal-search\">\n\t\t\t\t\t\t\t<i class=\"bb-icon-l bb-icon-search\"></i>\n\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\ttype=\"text\"\n\t\t\t\t\t\t\t\tclass=\"share-friends-search-input\"\n\t\t\t\t\t\t\t\tplaceholder=\"Search for members\"\n\t\t\t\t\t\t\t\tautocomplete=\"off\"\n\t\t\t\t\t\t\t/>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<div class=\"share-friends-content bb-share-modal-content\">\n\t\t\t\t\t\t\t<div class=\"share-friends-label bb-share-modal-label\">\n\t\t\t\t\t\t\t\tAll Friends\n\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t<div class=\"share-friends-list\">\n\t\t\t\t\t\t\t\t<div class=\"share-friends-loading\" style=\"display: none;\">\n\t\t\t\t\t\t\t\t\t<span class=\"spinner is-active\"></span>\n\t\t\t\t\t\t\t\t\t<p>Loading...</p>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"share-friends-results\">\n\t\t\t\t\t\t\t\t\t<!-- Friends will be loaded via AJAX -->\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t<!-- Hidden field to store activity ID -->\n\t\t\t\t\t\t<input type=\"hidden\" id=\"share-friends-activity-id\" value=\"".concat(this.currentActivityId, "\" />\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t");
      $('body').append(modalHTML);
      var $modal = $('.buddyboss-share-friends-modal-container');
      $modal.fadeIn(300);
      $('body').css('overflow', 'hidden');

      // Bind close button
      $modal.find('.share-friends-close, .buddyboss-share-modal-overlay').on('click', function () {
        _this24.closeFriendsModal();
      });

      // Load friends
      this.loadFriendsForSharing();

      // Bind search input
      $modal.find('.share-friends-search-input').on('input', this.handleFriendSearch.bind(this));
    },
    /**
     * Close friends selection modal
     */
    closeFriendsModal: function closeFriendsModal() {
      $('.buddyboss-share-friends-modal-container').fadeOut(300, function () {
        $(this).remove();
      });
      $('body').css('overflow', '');
    },
    /**
     * Load friends for sharing
     */
    loadFriendsForSharing: function loadFriendsForSharing() {
      var _this25 = this;
      var searchTerm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      var page = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
      var $loading = $('.share-friends-loading');
      var $results = $('.share-friends-results');

      // Show loading
      $loading.show();
      if (page === 1) {
        $results.empty();
      }
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_get_friends_for_sharing',
          nonce: buddybossSharingFrontend.nonce,
          search: searchTerm,
          page: page
        },
        success: function success(response) {
          $loading.hide();
          if (response.success && response.data.friends && response.data.friends.length > 0) {
            var html = '';
            response.data.friends.forEach(function (friend) {
              html += "\n\t\t\t\t\t\t\t\t<div class=\"share-friends-item bb-share-modal-list-item\" data-user-id=\"".concat(friend.id, "\">\n\t\t\t\t\t\t\t\t\t<div class=\"friend-avatar\">\n\t\t\t\t\t\t\t\t\t\t<img src=\"").concat(friend.avatar, "\" alt=\"").concat(friend.name, "\" class=\"avatar\" />\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"friend-name\">").concat(friend.name, "</div>\n\t\t\t\t\t\t\t\t\t<div class=\"bb-share-list-item-caret\">\n\t\t\t\t\t\t\t\t\t\t<i class=\"bb-icon-l bb-icon-angle-right\"></i>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t");
            });
            if (page === 1) {
              $results.html(html);
            } else {
              $results.append(html);
            }

            // Bind click events
            $results.find('.share-friends-item').off('click').on('click', function (e) {
              var $item = $(e.currentTarget);
              var userId = $item.data('user-id');
              var userName = $item.find('.friend-name').text();
              var userAvatar = $item.find('.friend-avatar img').attr('src');
              _this25.handleFriendSelection(userId, userName, userAvatar);
            });
          } else if (page === 1) {
            var message = searchTerm ? 'Sorry, no members were found.' : 'No friends found.';
            $results.html("\n\t\t\t\t\t\t\t<div class=\"no-friends-found\">\n\t\t\t\t\t\t\t\t<div class=\"no-friends-icon\">\n\t\t\t\t\t\t\t\t\t<i class=\"bb-icon-rf bb-icon-info-circle\"></i>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"no-friends-message\">".concat(message, "</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t"));
          }
        },
        error: function error() {
          $loading.hide();
          if (page === 1) {
            $results.html("\n\t\t\t\t\t\t\t<div class=\"no-friends-found\">\n\t\t\t\t\t\t\t\t<div class=\"no-friends-icon\">\n\t\t\t\t\t\t\t\t\t<i class=\"bb-icon-rf bb-icon-info-circle\"></i>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"no-friends-message\">Error loading friends. Please try again.</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t");
          }
        }
      });
    },
    /**
     * Handle friend search
     */
    handleFriendSearch: function handleFriendSearch(e) {
      var _this26 = this;
      var searchTerm = $(e.currentTarget).val().trim();

      // Clear previous timeout if exists
      if (this.friendSearchTimeout) {
        clearTimeout(this.friendSearchTimeout);
      }

      // Debounce the search
      this.friendSearchTimeout = setTimeout(function () {
        _this26.loadFriendsForSharing(searchTerm, 1);
      }, 300); // 300ms debounce
    },
    /**
     * Handle friend selection
     */
    handleFriendSelection: function handleFriendSelection(userId, userName, userAvatar) {
      // Store selected friend info
      this.selectedFriend = {
        id: userId,
        name: userName,
        avatar: userAvatar
      };

      // Close friends modal
      this.closeFriendsModal();

      // Check if custom message is enabled
      if (buddybossSharingFrontend.enableCustomMsg) {
        // Add body class early to handle UI changes
        $('body').addClass('bb-sharing-active');

        // Open BuddyBoss native post form
        // Note: openBuddyBossPostFormWithFriend will handle form loading if needed
        // and will call loadAndInjectSharedActivityWithFriend after form is ready
        this.openBuddyBossPostFormWithFriend();
      } else {
        // Share directly without modal
        this.shareDirectlyToFriend();
      }
    },
    /**
     * Open BuddyBoss native post form with friend profile pre-selected
     */
    openBuddyBossPostFormWithFriend: function openBuddyBossPostFormWithFriend() {
      var _this27 = this;
      // Clear any existing draft before opening share form
      this.clearDraftActivity();

      // Check if form exists, if not load it first
      var $form = $('#whats-new-form');
      var $formContainer = $('#bp-nouveau-activity-form');
      if (!$form.length || !$formContainer.length) {
        // Form doesn't exist, load it via AJAX
        this.loadActivityPostForm().then(function () {
          // Form loaded, now open it
          _this27.openBuddyBossPostForm();

          // Wait a moment for the form to expand, then set the friend's profile as target
          setTimeout(function () {
            _this27.setFriendProfileInForm(_this27.selectedFriend);

            // Load and inject shared activity content after form is ready
            _this27.loadAndInjectSharedActivityWithFriend();
          }, 400);
        })["catch"](function (error) {
          console.error('Failed to load activity post form for friend:', error);
          _this27.showNotification(buddybossSharingFrontend.i18n.error || 'Failed to load post form', 'error');
        });
      } else {
        // Form exists, proceed normally
        var $textarea = $('#whats-new');
        var $formWrapper = $form.closest('.activity-update-form');
        var $editFormWrap = $('#bp-nouveau-single-activity-edit-form-wrap');

        // If form is inside the hidden edit form wrap, show it
        if ($editFormWrap.length && $editFormWrap.is(':hidden')) {
          $editFormWrap.show();
        }

        // Ensure form wrapper is visible
        if ($formWrapper.length && $formWrapper.is(':hidden')) {
          $formWrapper.show();
        }

        // Ensure form is visible
        if ($form.length && $form.is(':hidden')) {
          $form.show();
        }
        if ($textarea.length) {
          // Wait a bit for form to be ready, then focus
          setTimeout(function () {
            // Check if form wrapper needs modal-popup class
            if ($formWrapper.length && !$formWrapper.hasClass('modal-popup')) {
              $formWrapper.addClass('modal-popup');
              $('body').addClass('activity-modal-open');
            }

            // Ensure form is still visible after adding classes
            if ($editFormWrap.length) {
              $editFormWrap.show();
            }
            if ($formWrapper.length) {
              $formWrapper.show();
            }
            if ($form.length) {
              $form.show();
            }

            // Focus the textarea to trigger displayFull
            $textarea.focus();

            // Wait a moment for the form to expand, then set the friend's profile as target
            setTimeout(function () {
              _this27.setFriendProfileInForm(_this27.selectedFriend);

              // Load and inject shared activity content after form is ready
              _this27.loadAndInjectSharedActivityWithFriend();
            }, 300);
          }, 100);
        } else {
          console.warn('BuddyBoss post form not found');
        }
      }
    },
    /**
     * Set friend profile in BuddyBoss native form
     */
    setFriendProfileInForm: function setFriendProfileInForm(friend) {
      var $whatsNewForm = $('#whats-new-form');

      // Find the post-in dropdown/select element
      var $postIn = $whatsNewForm.find('select[name="whats-new-post-in"]');
      if (!$postIn.length) {
        console.warn('Post-in select element not found');
        return;
      }

      // Set the value to the friend's user ID
      $postIn.val(friend.id).trigger('change');
    },
    /**
     * Load and inject shared activity into BuddyBoss form (for friend share)
     */
    loadAndInjectSharedActivityWithFriend: function loadAndInjectSharedActivityWithFriend() {
      var _this28 = this;
      // Wait for form to be visible
      setTimeout(function () {
        $.ajax({
          url: buddybossSharingFrontend.ajaxUrl,
          type: 'POST',
          data: {
            action: 'buddyboss_get_activity_content',
            nonce: buddybossSharingFrontend.nonce,
            activity_id: _this28.currentActivityId
          },
          success: function success(response) {
            if (response.success) {
              // Store the friend ID as well
              _this28.injectSharedActivityToFormWithFriend(response.data.content);
            }
          },
          error: function error() {
            console.error(buddybossSharingFrontend.i18n.failedLoadActivity);
          }
        });
      }, 300);
    },
    /**
     * Inject shared activity to form with friend info
     */
    injectSharedActivityToFormWithFriend: function injectSharedActivityToFormWithFriend(activityContent) {
      var $form = $('#whats-new-form');

      // Add hidden field for shared activity ID
      if (!$form.find('#buddyboss-shared-activity-id').length) {
        $form.append("<input type=\"hidden\" id=\"buddyboss-shared-activity-id\" value=\"".concat(this.currentActivityId, "\">"));
      }

      // Add hidden field for friend ID
      if (!$form.find('#buddyboss-shared-friend-id').length) {
        $form.append("<input type=\"hidden\" id=\"buddyboss-shared-friend-id\" value=\"".concat(this.selectedFriend.id, "\">"));
      }

      // Inject the shared activity preview
      var $textarea = $('#whats-new');
      var $preview = $form.find('.buddyboss-shared-activity-preview');
      if (!$preview.length) {
        $preview = $('<div class="buddyboss-shared-activity-preview"></div>');
        $textarea.parent().after($preview);
      }
      $preview.html(activityContent);

      // Initialize Video.js for any videos in preview
      this.initializeVideoJS($preview);

      // Disable upload buttons
      this.disableUploadButtons();

      // Hook form submission
      this.hookFormSubmissionForFriend();

      // Trigger validation to enable submit button when preview is added
      if (typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.Activity && bp.Nouveau.Activity.postForm && bp.Nouveau.Activity.postForm.postForm) {
        bp.Nouveau.Activity.postForm.postForm.postValidate();
      }
    },
    /**
     * Hook form submission for friend share
     */
    hookFormSubmissionForFriend: function hookFormSubmissionForFriend() {
      var _this29 = this;
      // Remove jQuery event listener
      $('#whats-new-form').off('submit.buddyboss-share-friend');

      // Remove previous native listener if exists
      var form = document.getElementById('whats-new-form');
      if (!form) {
        console.error('Form #whats-new-form not found!');
        return;
      }

      // Remove existing handler
      if (form._buddybossShareFriendHandler) {
        form.removeEventListener('submit', form._buddybossShareFriendHandler, true);
      }

      // Add new native listener at capture phase
      form._buddybossShareFriendHandler = function (e) {
        var sharedActivityId = document.getElementById('buddyboss-shared-activity-id');
        var sharedFriendId = document.getElementById('buddyboss-shared-friend-id');
        if (sharedActivityId && sharedActivityId.value && sharedFriendId && sharedFriendId.value) {
          // Prevent default submission
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();

          // Get custom message
          var $message = $('#whats-new');
          var customMessage = '';
          if ($message.is('[contenteditable]')) {
            customMessage = $message.text().trim();
          } else {
            customMessage = $message.val().trim();
          }
          // Submit via AJAX
          _this29.submitSharedActivityToFriend(sharedActivityId.value, sharedFriendId.value, customMessage);
          return false;
        }
      };
      form.addEventListener('submit', form._buddybossShareFriendHandler, true);
    },
    /**
     * Submit shared activity to friend's profile
     */
    submitSharedActivityToFriend: function submitSharedActivityToFriend(activityId, friendId, customMessage) {
      var _this30 = this;
      var $submitBtn = $('#aw-whats-new-submit');
      var originalText = $submitBtn.text();
      $submitBtn.prop('disabled', true).text('Posting...');
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_share_to_friend_profile',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: activityId,
          friend_id: friendId,
          custom_message: customMessage
        },
        success: function success(response) {
          if (response.success) {
            _this30.showNotification(response.data.message || buddybossSharingFrontend.i18n.sharedSuccess, 'success');

            // Update share count if provided
            if (response.data.share_count) {
              _this30.updateShareCount(response.data.share_count);
            }

            // Clear draft activity
            _this30.clearDraftActivity();

            // Cleanup form
            _this30.cleanupSharedActivityFormForFriend();

            // Reset form
            $('#whats-new-form').trigger('reset');
          } else {
            _this30.showNotification(response.data.message || buddybossSharingFrontend.i18n.error, 'error');
            $submitBtn.prop('disabled', false).text(originalText);
          }
        },
        error: function error(xhr, status, _error5) {
          console.error('AJAX error:', {
            xhr: xhr,
            status: status,
            error: _error5
          });
          _this30.showNotification(buddybossSharingFrontend.i18n.error, 'error');
          $submitBtn.prop('disabled', false).text(originalText);
        }
      });
    },
    /**
     * Cleanup shared activity form for friend share
     */
    cleanupSharedActivityFormForFriend: function cleanupSharedActivityFormForFriend() {
      // Remove hidden fields
      $('#buddyboss-shared-activity-id').remove();
      $('#buddyboss-shared-friend-id').remove();

      // Remove preview
      $('.buddyboss-shared-activity-preview').remove();

      // Remove event listener
      $('#whats-new-form').off('submit.buddyboss-share-friend');
      $('body').removeClass('bb-sharing-active');

      // Remove native event listener
      var form = document.getElementById('whats-new-form');
      if (form && form._buddybossShareFriendHandler) {
        form.removeEventListener('submit', form._buddybossShareFriendHandler, true);
        delete form._buddybossShareFriendHandler;
      }

      // Show toolbar again
      var $toolbar = $('#whats-new-toolbar[data-share-disabled="true"]');
      if ($toolbar.length) {
        $toolbar.show().removeAttr('data-share-disabled');
      }

      // Hide the edit form wrap on single activity pages
      this.hideEditFormWrapOnSingleActivityPage();

      // Reset friend selection

      // Trigger validation to disable submit button when preview is removed
      if (typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.Activity && bp.Nouveau.Activity.postForm && bp.Nouveau.Activity.postForm.postForm) {
        bp.Nouveau.Activity.postForm.postForm.postValidate();
      }
      this.selectedFriend = null;
    },
    /**
     * Handle click on shared activity preview - open in modal
     */
    handleSharedActivityClick: function handleSharedActivityClick(e) {
      // Check if the click originated from a media element (image, video, document links, etc.)
      var $target = $(e.target);
      var isMediaClick = $target.closest('a.bb-open-media-theatre, a.bb-open-document-theatre, a.entry-img, img, video, audio, .activity-media-elem, .document-activity, .media-activity, .bb-activity-video-wrap, .bb-activity-video-elem').length > 0;

      // Check if the click is on the "read more" link - allow it to work normally
      var isReadMoreLink = $target.is('a.shared-activity-read-more') || $target.closest('a.shared-activity-read-more').length > 0;

      // If it's a "read more" link, don't interfere - let it navigate normally
      if (isReadMoreLink) {
        return; // Allow the link to work normally
      }

      // If it's a media element click, prevent its default behavior and stop propagation
      if (isMediaClick) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
      } else {
        // For other clicks, just prevent default and stop propagation normally
        e.preventDefault();
        e.stopPropagation();
      }
      var $preview = $(e.currentTarget);
      //const activityId = $preview.closest('.shared-activity-preview').data('activity-id');
      var activityId = $preview.data('activity-id');
      if (!activityId) {
        console.warn('No activity ID found on shared activity preview');
        return;
      }
      this.openSharedActivityModal(activityId);
    },
    /**
     * Open shared activity modal using platform's modal system
     */
    openSharedActivityModal: function openSharedActivityModal(activityId) {
      var _this31 = this;
      // Load activity data via AJAX first
      $.ajax({
        url: buddybossSharingFrontend.ajaxUrl,
        type: 'POST',
        data: {
          action: 'buddyboss_load_shared_activity_modal',
          nonce: buddybossSharingFrontend.nonce,
          activity_id: activityId
        },
        success: function success(response) {
          if (response.success && response.data) {
            // Create a temporary activity element with the correct ID format
            var tempActivity = $(response.data.activity_html);
            tempActivity.attr('id', 'activity-' + activityId);

            // Append to a hidden container
            var tempContainer = $('#bb-shared-activity-temp-container');
            if (tempContainer.length === 0) {
              tempContainer = $('<div id="bb-shared-activity-temp-container" style="display: none;"></div>');
              $('body').append(tempContainer);
            }
            tempContainer.append(tempActivity);

            // Use platform's modal system
            if (typeof bp !== 'undefined' && bp.Nouveau && bp.Nouveau.Activity && bp.Nouveau.Activity.launchActivityPopup) {
              bp.Nouveau.Activity.launchActivityPopup(activityId, 0);

              // Add custom class to identify this as a shared activity modal
              setTimeout(function () {
                var $modal = $('#activity-modal');
                var $modalWrapper = $('.bb-activity-model-wrapper');
                if ($modal.length) {
                  $modal.addClass('bb-shared-activity-modal');
                }
                if ($modalWrapper.length) {
                  $modalWrapper.addClass('bb-shared-activity-modal');
                }

                // Clean up temporary element
                tempActivity.remove();

                // Force lazy images and media to load immediately in the modal
                if (typeof bp !== 'undefined' && bp.Nouveau && typeof bp.Nouveau.lazyLoad === 'function') {
                  // Trigger lazy load on modal content
                  bp.Nouveau.lazyLoad($modal.find('.lazy'));

                  // Also trigger scroll event as fallback
                  setTimeout(function () {
                    jQuery(window).scroll();
                  }, 50);
                }

                // If video play button is clicked in preview, trigger it in modal
                if (_this31.playVideoInModal) {
                  _this31.playVideoInModal = false;

                  // Wait a bit more for video to be initialized in modal
                  setTimeout(function () {
                    var $modalVideoPlayButton = $modal.find('.vjs-big-play-button');
                    if ($modalVideoPlayButton.length > 0) {
                      $modalVideoPlayButton.trigger('click');
                    } else {
                      // Try to find and click via videojs player instance
                      var $videoElement = $modal.find('.video-js');
                      if ($videoElement.length > 0) {
                        var videoId = $videoElement.attr('id');
                        if (videoId && typeof videojs !== 'undefined') {
                          var player = videojs.getPlayer(videoId);
                          if (player) {
                            player.play();
                          }
                        }
                      }
                    }
                  }, 300);
                }
              }, 100);
            } else {
              console.error('Platform activity modal system not available');
              tempActivity.remove();
              _this31.playVideoInModal = false;
              alert('Activity modal system not available');
            }
          } else {
            var _response$data2;
            console.error('Failed to load activity data:', response);
            _this31.playVideoInModal = false;
            alert(((_response$data2 = response.data) === null || _response$data2 === void 0 ? void 0 : _response$data2.message) || 'Failed to load activity');
          }
        },
        error: function error(xhr, status, _error6) {
          console.error('AJAX error loading shared activity:', {
            xhr: xhr,
            status: status,
            error: _error6
          });
          _this31.playVideoInModal = false;
          alert('Failed to load activity. Please try again.');
        }
      });
    },
    /**
     * Clean up shared activity modal class when modal is closed
     */
    cleanupSharedActivityModalClass: function cleanupSharedActivityModalClass() {
      var $modal = $('#activity-modal');
      var $modalWrapper = $('.bb-activity-model-wrapper');
      if ($modal.length && $modal.hasClass('bb-shared-activity-modal')) {
        $modal.removeClass('bb-shared-activity-modal');
      }
      if ($modalWrapper.length && $modalWrapper.hasClass('bb-shared-activity-modal')) {
        $modalWrapper.removeClass('bb-shared-activity-modal');
      }

      // Reset video play flag
      this.playVideoInModal = false;

      // Close the dropdown if it's open when modal is closed
      this.closeDropdown();
    }
  };

  // Initialize on document ready
  $(document).ready(function () {
    BuddyBossActivitySharing.init();
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=activity-sharing.js.map