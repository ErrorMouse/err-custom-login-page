jQuery(document).ready(function($) {
   var mediaUploader;
   var $currentButtonClicked; 

   var errclp_params = window.errclp_ajax_params || {};
   
   $('body').on('click', '.errclp_upload_image_button', function(e) {
      e.preventDefault();
      $currentButtonClicked = $(this); 
      var uploaderTitle = $currentButtonClicked.data('uploader_title') || errclp_params.uploader_title;
      var uploaderButtonText = $currentButtonClicked.data('uploader_button_text') || errclp_params.uploader_button_text;

      if (mediaUploader) {
         mediaUploader.options.title = uploaderTitle;
         mediaUploader.options.button.text = uploaderButtonText;
         mediaUploader.open();
         return;
      }
      
      mediaUploader = wp.media.frames.file_frame = wp.media({
         title: uploaderTitle,
         button: { text: uploaderButtonText },
         multiple: false 
      });

      mediaUploader.on('select', function() {
         var attachment = mediaUploader.state().get('selection').first().toJSON();
         var $uploaderContainer = $currentButtonClicked.closest('.errclp-image-uploader');
         var $inputField = $uploaderContainer.find('input[type="text"].regular-text');
         var $previewDiv = $uploaderContainer.find('.errclp_image_preview');
         
         $inputField.val(attachment.url);
         
         if ($inputField.attr('id') === 'errclp_logo_image_url') {
            var $idField = $uploaderContainer.find('#errclp_logo_image_id');
            if ($idField.length) {
               $idField.val(attachment.id);
            }
         }
         
         $previewDiv.html(
            '<img src="' + attachment.url + '" style="max-width:100%; height:auto; border:1px solid #ddd;" /><br>' +
            '<button type="button" class="button button-small errclp_remove_image_button" style="margin-top:5px;">' +
            errclp_params.remove_image_text +
            '</button>'
         );
      });
      mediaUploader.open();
   });

   $('.wrap').on('click', '.errclp_remove_image_button', function(e){
      e.preventDefault();
      var $uploaderContainer = $(this).closest('.errclp-image-uploader');
      var $inputField = $uploaderContainer.find('input[type="text"].regular-text');
      var $previewDiv = $uploaderContainer.find('.errclp_image_preview');
      
      $inputField.val('');
      $previewDiv.html('');

      if ($inputField.attr('id') === 'errclp_logo_image_url') {
         var $idField = $uploaderContainer.find('#errclp_logo_image_id');
         if ($idField.length) {
            $idField.val('');
         }
      }
   });

   if (typeof $.fn.wpColorPicker === 'function') {
      $('.errclp-color-picker').wpColorPicker();
   }

   // --- Handling the opacity slider 
   var $formBgOpacitySlider = $('#errclp_form_bg_opacity_slider');
   var $formBgOpacityValue = $('#errclp_form_bg_opacity_value');
   if ($formBgOpacitySlider.length && $formBgOpacityValue.length) {
      $formBgOpacitySlider.on('input change', function() {
            $formBgOpacityValue.text($(this).val());
      });
   }
   var $formBorderOpacitySlider = $('#errclp_form_border_color_opacity_slider');
   var $formBorderOpacityValue = $('#errclp_form_border_color_opacity_value');
   if ($formBorderOpacitySlider.length && $formBorderOpacityValue.length) {
      $formBorderOpacitySlider.on('input change', function() {
         $formBorderOpacityValue.text($(this).val());
      });
   }

   // --- AJAX handling for saving settings ---
   var $settingsForm = $('#errclp-settings-form');
   var popupTimeout;

   if ($settingsForm.length) {
      $settingsForm.on('submit', function(e) {
            e.preventDefault(); 

            var $submitButton = $(this).find('input[type="submit"], button[type="submit"]');
            var originalButtonText = $submitButton.val();

            $submitButton.val(errclp_params.saving_text).prop('disabled', true);
            var formData = $(this).serialize() + '&action=errclp_save_settings' + '&security=' + errclp_params.nonce;

            $.ajax({
               url: errclp_params.ajax_url,
               type: 'POST',
               data: formData,
               dataType: 'json',
               success: function(response) {
                  if (response.success) {
                     var message = response.data.message || errclp_params.success_text;
                     if (response.data.status === 'no_change') {
                        message = response.data.message || errclp_params.no_change_text;
                        showAjaxPopup(message, 'info');
                     } else {
                        showAjaxPopup(message, 'success');
                     }
                  } else {
                     var errorMessage = (response.data && response.data.message) || errclp_params.error_text;
                     showAjaxPopup(errorMessage, 'error');
                  }
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  var detailedError = errclp_params.error_text;
                  if (jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message) {
                        detailedError = jqXHR.responseJSON.data.message;
                  } else if (errorThrown) {
                        detailedError += ' (' + textStatus + ': ' + errorThrown + ')';
                  }
                  showAjaxPopup(detailedError, 'error');
                  console.error("AJAX Error: ", textStatus, errorThrown, jqXHR.responseText);
               },
               complete: function() {
                  $submitButton.val(originalButtonText).prop('disabled', false);
               }
            });
      });
   }

   function showAjaxPopup(message, type) {
      var $popup = $('#errclp-ajax-popup');
      if (!$popup.length) {
         $('body').append('<div id="errclp-ajax-popup" style="display:none; position:fixed; top:50px; left:50%; transform:translateX(-50%); background-color:#4CAF50; color:white; padding:15px 25px; border-radius:5px; z-index:10000; box-shadow: 0 0 10px rgba(0,0,0,0.2); text-align:center;"><p style="margin:0; font-size:16px;"></p></div>');
         $popup = $('#errclp-ajax-popup');
      }

      $popup.find('p').text(message);

      if (type === 'error') {
         $popup.css('background-color', '#f44336');
      } else if (type === 'info') {
         $popup.css('background-color', '#2196F3');
      } else { // success
         $popup.css('background-color', '#4CAF50');
      }

      $popup.fadeIn();
      clearTimeout(popupTimeout);
      popupTimeout = setTimeout(function() {
         $popup.fadeOut();
      }, 5000);
   }

   // Reset Settings AJAX
   $('#errclp-reset-settings-button').on('click', function(e) {
      e.preventDefault();

      if (confirm(errclp_params.reset_confirm_text)) {
            var $resetButton = $(this);
            var originalButtonText = $resetButton.text(); 
            
            if ($resetButton.is('input')) {
               originalButtonText = $resetButton.val();
               $resetButton.val(errclp_params.resetting_text);
            } else {
               $resetButton.text(errclp_params.resetting_text);
            }
            $resetButton.prop('disabled', true);
            
            $('#errclp-ajax-popup').removeClass('success error info').hide().find('p').empty();


            $.ajax({
               url: errclp_params.ajax_url,
               type: 'POST',
               data: {
                  action: 'errclp_reset_settings',
                  security: errclp_params.reset_nonce
               },
               dataType: 'json',
               success: function(response) {
                  var message = '';
                  var messageType = 'info';

                  if (response.success) {
                     message = response.data.message;
                     messageType = 'success';
                     setTimeout(function() {
                        location.reload();
                     }, 2500);
                  } else {
                     message = (response.data && response.data.message) ? response.data.message : errclp_params.error_text;
                     messageType = 'error';
                  }
                  showAjaxPopup(message, messageType);
               },
               error: function(xhr, status, error) {
                  var errorMsg = errclp_params.error_text;
                  if(xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message){
                     errorMsg = xhr.responseJSON.data.message;
                  } else {
                     errorMsg += ' (AJAX Error: ' + status + ' - ' + error + ')';
                  }
                  showAjaxPopup(errorMsg, 'error');
               },
               complete: function(xhr) {
                  if (!(xhr.responseJSON && xhr.responseJSON.success)) {
                     if ($resetButton.is('input')) {
                        $resetButton.val(originalButtonText);
                     } else {
                        $resetButton.text(originalButtonText);
                     }
                     $resetButton.prop('disabled', false);
                  }
               }
            });
      }
   });
});