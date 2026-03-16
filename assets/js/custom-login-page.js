jQuery(document).ready(function($) {
   // Use the localized parameters object, with a fallback to prevent errors.
   var errculop_params = window.errculop_ajax_params || {};

   // --- Media Uploader for Image Selection ---
   var mediaUploader;
   $('body').on('click', '.errculop_upload_image_button', function(e) {
      e.preventDefault();
      var $currentButtonClicked = $(this);

      if (mediaUploader) {
         mediaUploader.open();
         return;
      }

      mediaUploader = wp.media({
         title: 'Choose an Image',
         button: { text: 'Use this image' },
         multiple: false
      });

      mediaUploader.on('select', function() {
         var attachment = mediaUploader.state().get('selection').first().toJSON();
         var $uploaderContainer = $currentButtonClicked.closest('.errculop-image-uploader');
         var $inputField = $uploaderContainer.find('input[type="text"]');
         var $idField = $uploaderContainer.find('input[type="hidden"]');
         var $previewDiv = $uploaderContainer.find('.errculop_image_preview');

         $inputField.val(attachment.url);
         $idField.val(attachment.id);

         // IMPROVED: Safer way to create HTML elements.
         var $img = $('<img>', {
            src: attachment.url,
            style: 'max-width:100%; height:auto; border:1px solid #ddd;'
         });
         var $button = $('<button></button>', {
            type: 'button',
            class: 'button button-small errculop_remove_image_button',
            style: 'margin-top:5px;'
         }).text(errculop_params.remove_image_text);

         $previewDiv.empty().append($img, '<br>', $button);
      });
      mediaUploader.open();
   });

   // --- Remove Image Button ---
   $('#errculop-settings-page').on('click', '.errculop_remove_image_button', function(e) {
      e.preventDefault();
      var $uploaderContainer = $(this).closest('.errculop-image-uploader');
      $uploaderContainer.find('input[type="text"]').val('');
      $uploaderContainer.find('input[type="hidden"]').val('');
      $uploaderContainer.find('.errculop_image_preview').empty();
   });

   // --- Initialize WordPress Color Picker ---
   if (typeof $.fn.wpColorPicker === 'function') {
      $('.errculop-color-picker').wpColorPicker();
   }
   
   // --- Handling the opacity sliders ---
   $('.errculop-slider-control').on('input change', function() {
      // Use data-target to find the corresponding value display
      var targetSelector = $(this).data('target');
      $(targetSelector).text($(this).val());
   });

   // --- AJAX Form Submission for Saving Settings ---
   $('#errculop-settings-form').on('submit', function(e) {
      e.preventDefault();

      var $form = $(this);
      var $submitButton = $form.find('input[type="submit"]');
      var originalButtonText = $submitButton.val();
      var formData = $form.serialize() + '&action=errculop_save_settings' + '&security=' + errculop_params.nonce;

      $submitButton.val(errculop_params.saving_text).prop('disabled', true);

      $.ajax({
         url: errculop_params.ajax_url,
         type: 'POST',
         data: formData,
         dataType: 'json',
         success: function(response) {
            // IMPROVED: Simplified logic to match the new PHP response.
            if (response.success) {
               showAjaxPopup(response.data.message, 'success');
            } else {
               var errorMessage = (response.data && response.data.message) ? response.data.message : errculop_params.error_text;
               showAjaxPopup(errorMessage, 'error');
            }
         },
         error: function() {
            showAjaxPopup(errculop_params.error_text, 'error');
         },
         complete: function() {
            $submitButton.val(originalButtonText).prop('disabled', false);
         }
      });
   });

   // --- AJAX for Resetting Settings ---
   $('#errculop-reset-settings-button').on('click', function(e) {
      e.preventDefault();

      if (confirm(errculop_params.reset_confirm_text)) {
            var $resetButton = $(this);
            var originalButtonText = $resetButton.text();
            $resetButton.text(errculop_params.resetting_text).prop('disabled', true);

            $.ajax({
               url: errculop_params.ajax_url,
               type: 'POST',
               data: {
                  action: 'errculop_reset_settings',
                  security: errculop_params.reset_nonce
               },
               dataType: 'json',
               success: function(response) {
                  if (response.success) {
                     showAjaxPopup(response.data.message, 'success');
                     setTimeout(function() {
                        location.reload();
                     }, 2000);
                  } else {
                     showAjaxPopup(response.data.message || errculop_params.error_text, 'error');
                     $resetButton.text(originalButtonText).prop('disabled', false);
                  }
               },
               error: function() {
                  showAjaxPopup(errculop_params.error_text, 'error');
                  $resetButton.text(originalButtonText).prop('disabled', false);
               }
            });
      }
   });

   // --- Reusable AJAX Notification Popup ---
   var popupTimeout;
   function showAjaxPopup(message, type) {
      var $popup = $('#errculop-ajax-popup');
      var backgroundColor = '#4CAF50'; // Success

      if (type === 'error') {
         backgroundColor = '#f44336'; // Error
      } else if (type === 'info') {
         backgroundColor = '#2196F3'; // Info
      }
      
      $popup.find('p').text(message);
      $popup.css('background-color', backgroundColor).fadeIn();

      clearTimeout(popupTimeout);
      popupTimeout = setTimeout(function() {
         $popup.fadeOut();
      }, 5000);
   }
});