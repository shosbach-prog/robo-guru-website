/******/ (() => { // webpackBootstrap
/**
 * Admin JavaScript
 *
 * @package BuddyBoss_SEO
 * @since 1.0.0
 */

(function ($) {
  'use strict';

  var BuddyBossAdmin = {
    /**
     * Initialize
     */
    init: function init() {
      this.initImageUpload();
      this.initSyncCheckboxes();
      this.initPreviewUpdates();
    },
    /**
     * Image upload functionality
     */
    initImageUpload: function initImageUpload() {
      // Use delegation for dynamically added buttons
      $(document).on('click', '.buddyboss-sharing-upload-image', function (e) {
        e.preventDefault();

        // Create WordPress media frame
        var frame = wp.media({
          title: buddybossSeoAdmin.i18n.uploadImage,
          button: {
            text: buddybossSeoAdmin.i18n.selectImage
          },
          multiple: false,
          library: {
            type: 'image'
          }
        });

        // When image is selected
        frame.on('select', function () {
          var attachment = frame.state().get('selection').first().toJSON();

          // Update hidden input
          $('#buddyboss_og_image').val(attachment.url);

          // Update/Show image preview in OG Image field
          if ($('#og-image-preview-img').length > 0) {
            $('#og-image-preview-img').attr('src', attachment.url);
          } else {
            // Create preview if doesn't exist
            $('.buddyboss-sharing-image-upload').prepend("<div class=\"buddyboss-sharing-image-preview\" style=\"margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; max-width: 500px;\">\n\t\t\t\t\t\t\t\t<img src=\"".concat(attachment.url, "\" id=\"og-image-preview-img\" style=\"width: 100%; height: auto; display: block;\" />\n\t\t\t\t\t\t\t</div>"));
          }

          // Update social preview image
          if ($('#social-preview-img').length > 0) {
            $('#social-preview-img').attr('src', attachment.url).closest('.social-preview-image').css('background', '#f0f2f5');
          } else {
            // Create image in social preview if placeholder was there
            $('.buddyboss-sharing-social-preview').find('.social-preview-image').html("<img src=\"".concat(attachment.url, "\" style=\"width: 100%; height: 100%; object-fit: cover;\" id=\"social-preview-img\" />"));
          }

          // Show remove button if not already there
          if ($('.buddyboss-sharing-remove-image').length === 0) {
            $('.buddyboss-sharing-upload-image').after(" <button type=\"button\" class=\"button buddyboss-sharing-remove-image\">".concat(buddybossSeoAdmin.i18n.remove || 'Remove', "</button>"));
          }
        });

        // Open media frame
        frame.open();
      });

      // Remove image
      $(document).on('click', '.buddyboss-sharing-remove-image', function (e) {
        e.preventDefault();

        // Clear hidden input
        $('#buddyboss_og_image').val('');

        // Remove image preview in OG Image field
        $('.buddyboss-sharing-image-preview').remove();

        // Reset social preview to placeholder
        $('.buddyboss-sharing-social-preview').find('.social-preview-image').html('<div style="width: 100%; height: 260px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;"><span style="color: white; font-size: 48px;">📱</span></div>');

        // Remove the remove button
        $(this).remove();
      });
    },
    /**
     * Sync checkboxes that copy SEO values to OG fields
     */
    initSyncCheckboxes: function initSyncCheckboxes() {
      // Use same as SEO title
      var $syncTitle = $('#buddyboss_og_use_same_title');
      var $ogTitle = $('#buddyboss_og_title');
      var $seoTitle = $('#buddyboss_seo_title');
      if ($syncTitle.length && $ogTitle.length && $seoTitle.length) {
        $syncTitle.on('change', function () {
          if ($(this).is(':checked')) {
            $ogTitle.prop('readonly', true).val($seoTitle.val());
            $('#social-preview-title').text($seoTitle.val());
          } else {
            $ogTitle.prop('readonly', false);
          }
        });

        // When SEO title changes, update OG if synced
        $seoTitle.on('input', function () {
          if ($syncTitle.is(':checked')) {
            $ogTitle.val($(this).val());
            $('#social-preview-title').text($(this).val());
          }
        });
      }

      // Use same as SEO description
      var $syncDesc = $('#buddyboss_og_use_same_desc');
      var $ogDesc = $('#buddyboss_og_description');
      var $seoDesc = $('#buddyboss_seo_description');
      if ($syncDesc.length && $ogDesc.length && $seoDesc.length) {
        $syncDesc.on('change', function () {
          if ($(this).is(':checked')) {
            $ogDesc.prop('readonly', true).val($seoDesc.val());
            $('#social-preview-desc').text($seoDesc.val());
          } else {
            $ogDesc.prop('readonly', false);
          }
        });

        // When SEO description changes, update OG if synced
        $seoDesc.on('input', function () {
          if ($syncDesc.is(':checked')) {
            $ogDesc.val($(this).val());
            $('#social-preview-desc').text($(this).val());
          }
        });
      }
    },
    /**
     * Update previews in real-time
     */
    initPreviewUpdates: function initPreviewUpdates() {
      // SEO title preview
      $('#buddyboss_seo_title').on('input', function () {
        var value = $(this).val();
        $('#seo-preview-title').text(value);

        // Update OG preview if synced
        if ($('#buddyboss_og_use_same_title').is(':checked')) {
          $('#social-preview-title').text(value);
        }
      });

      // SEO description preview
      $('#buddyboss_seo_description').on('input', function () {
        var value = $(this).val();
        $('#seo-preview-desc').text(value);

        // Update OG preview if synced
        if ($('#buddyboss_og_use_same_desc').is(':checked')) {
          $('#social-preview-desc').text(value);
        }
      });

      // OG title preview
      $('#buddyboss_og_title').on('input', function () {
        $('#social-preview-title').text($(this).val());
      });

      // OG description preview
      $('#buddyboss_og_description').on('input', function () {
        $('#social-preview-desc').text($(this).val());
      });
    }
  };

  // Initialize on document ready
  $(document).ready(function () {
    BuddyBossAdmin.init();
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=admin.js.map