<?php
/**
 * @package   JD Simple Contact Form
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2021 Joomdev, Inc. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// no direct access
defined('_JEXEC') or die;
$title = $params->get('title', '');
$description = $params->get('description', '');
$session = JFactory::getSession();
$message = $session->get('jdscf-message-' . $module->id, '');
$captcha = $params->get('captcha', 0);
//checking if single cc is enabled
$single_cc_enable = ModJDSimpleContactFormHelper::isSingleCCMail($params);
?>
<?php
if (!empty($message)) {
   echo '<div class="jd-simple-contact-form jd-simple-contact-message-' . $module->id . '">' . $message . '</div>';
   $session->set('jdscf-message-' . $module->id, '');
} else {
   ?>
   <div class="jd-simple-contact-form jd-simple-contact-message-<?php echo $module->id; ?> <?php echo $moduleclass_sfx; ?>">
      <div class="cookie-notice alert alert-info" role="alert">
         <?php echo JText::_("MOD_JDSCF_NOTICE_ON_COOKIES_DISABLED"); ?>
      </div>
      <div id="jdscf-message-<?php echo $module->id; ?>"></div>
      <div class="simple-contact-form-loader module-<?php echo $module->id; ?> d-none">
         <div class="loading"></div>
      </div>
      <div class="jd-simple-contact-form-header">
         <?php if (!empty($title)) { ?>
            <h5 class="jd-simple-contact-description-title card-title"><?php echo JText::_($title); ?></h5>
         <?php } ?>
         <?php if (!empty($description)) { ?>
            <p class="jd-simple-contact-description card-subtitle mb-2 text-muted"><?php echo JText::_($description); ?></p>
         <?php } ?>
      </div>
      <form method="POST" action="<?php echo JURI::root(); ?>index.php?option=com_ajax&module=jdsimplecontactform&format=json&method=submit" data-parsley-validate data-parsley-errors-wrapper="<ul class='text-danger list-unstyled mt-2 small'></ul>" data-parsley-error-class="border-danger" data-parsley-success-class="border-success" id="simple-contact-form-<?php echo $module->id; ?>" enctype="multipart/form-data">
         <div class="jdscf-row">
            <?php
               ModJDSimpleContactFormHelper::renderForm($params, $module);
               if($single_cc_enable) {
                  $singleCC = new JLayoutFile('fields.singlecc', JPATH_SITE . '/modules/mod_jdsimplecontactform/layouts');
                  echo $singleCC->render(['params' => $params]);
               }
               if ($captcha) {
                  $captchaType = $params->get('captchaPlugins') == "" ? JFactory::getConfig()->get('captcha') : $params->get('captchaPlugins');
                  JPluginHelper::importPlugin('captcha', $captchaType);

                  if( ModJDSimpleContactFormHelper::getJoomlaVersion() < 4 ) {
                     $dispatcher = JEventDispatcher::getInstance();
                     $dispatcher->trigger('onInit', ['jdscf_recaptcha_' . $module->id]);
                  } else {
                     $dispatcher = \Joomla\CMS\Factory::getApplication();
                     $dispatcher->triggerEvent('onInit', ['jdscf_recaptcha_' . $module->id]);
                  }
                  
                  $plugin = JPluginHelper::getPlugin('captcha', $captchaType);
                  
                  if ( $captchaType == "recaptcha" ) {
                     // Recaptcha: I am not a robot
                     if (!empty($plugin)) {
                        $plugin_params = new JRegistry($plugin->params);
                        $attributes = [];
                        $attributes['data-theme'] = $plugin_params->get('theme2', '');
                        $attributes['data-size'] = $plugin_params->get('size', '');
                        $attributeArray = [];
                        foreach ($attributes as $attributeKey => $attributeValue) {
                           $attributeArray[] = $attributeKey . '="' . $attributeValue . '"';
                        }
                        ?>
                        <div class="jdscf-col-md-12">
                           <div class="form-group">
                              <div id="jdscf_recaptcha_<?php echo $module->id; ?>" class="g-recaptcha" data-sitekey="<?php echo $plugin_params->get('public_key', ''); ?>" <?php echo implode(' ', $attributeArray); ?>></div>
                           </div>
                        </div>
                        <?php
                     }
                  } elseif ( $captchaType == "recaptcha_invisible" ) {
                     // Invisible recaptcha
                     if (!empty($plugin)) {
                        $plugin_params = new JRegistry($plugin->params);
                     ?>
                        <div id='recaptcha' class="g-recaptcha" data-sitekey="<?php echo $plugin_params->get('public_key', ''); ?>"  data-size="invisible"></div>
                     <?php
                     }
                  } elseif ( !empty($captchaType) ) {
                     // Display captcha plugin fields
                     if (!empty($plugin)) {
                        $plugin_params = new JRegistry($plugin->params);
                        
                        if( ModJDSimpleContactFormHelper::getJoomlaVersion() < 4 ) {
                           $captchaHtml = $dispatcher->trigger('onDisplay', ['jdscf_recaptcha_' . $module->id, 'jdscf_recaptcha_' . $module->id]);
                        } else {
                           $captchaHtml = $dispatcher->triggerEvent('onDisplay', ['jdscf_recaptcha_' . $module->id, 'jdscf_recaptcha_' . $module->id]);
                        }
                        
                        if (!empty($captchaHtml)) {
                           ?>
                           <div class="jdscf-col-md-12">
                              <div class="form-group">
                                 <?php
                                 foreach ($captchaHtml as $cHtml) {
                                    // Add captcha generated html to page
                                    echo $cHtml;
                                 }
                                 ?>
                              </div>
                           </div>
                        <?php
                        }
                     }
                  }
               }
            ?>

            <?php
               $submit = new JLayoutFile('fields.submit', JPATH_SITE . '/modules/mod_jdsimplecontactform/layouts');
               echo $submit->render(['params' => $params]);
            ?>

         </div>
         
         <input type="hidden" name="returnurl" value="<?php echo urlencode(JUri::getInstance()); ?>"/>
         <input type="hidden" name="id" value="<?php echo $module->id; ?>" />
         <?php echo JHtml::_('form.token'); ?>
      </form>
   </div>
   <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
   <script src="//parsleyjs.org/dist/parsley.min.js"></script>
   <script src="<?php echo JURI::root(); ?>media/mod_jdsimplecontactform/assets/js/moment.min.js"></script>
   <script src="//cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
   <script>
      <?php
         foreach (ModJDSimpleContactFormHelper::getJS($module->id) as $js) {
            echo $js;
         }
      ?>
   </script>
   <script> var jQuery_3_3_1 = $.noConflict(true);</script>
   <?php if ($params->get('ajaxsubmit', 0)) { ?>
      <script>
         (function ($) {
            $(function () {
               var showMessage<?php echo $module->id; ?> = function (type, message) {
                  type = type == 'error' ? 'danger' : type;
                  var _alert = '<div class="alert alert-' + type + '"><div>' + message + '</div></div>';
                  $('#jdscf-message-<?php echo $module->id; ?>').html(_alert);
                  setTimeout(function () {
                     $('#jdscf-message-<?php echo $module->id; ?>').html('');
                  }, 3000);
                  
                  $('html, body').animate({
                     scrollTop: $('#simple-contact-form-<?php echo $module->id; ?>').offset().top - 150
                  }, 300);
               }

               // Smooth Scroll to parsley errors
               $('#simple-contact-form-<?php echo $module->id; ?>').parsley().on('field:validated', function() {
                  var errorNotice = $('ul.text-danger li');
                  if ( errorNotice.length ) {
                     $('html, body').animate({
                        scrollTop: errorNotice.offset().top - 100
                     }, 300);
                  }
               });

               $('#simple-contact-form-<?php echo $module->id; ?>').on('submit', function (e) {
                  e.preventDefault();
                  var formData = new FormData(this);
                  var _form = $(this);
                  var _id = 'simple-contact-form-<?php echo $module->id; ?>';
                  var _loading = $('.simple-contact-form-loader.module-<?php echo $module->id; ?>');
                  if (_form.parsley().isValid()) {
                     $.ajax({
                        url: '<?php echo JURI::root(); ?>index.php?option=com_ajax&module=jdsimplecontactform&format=json&method=submitForm',
                        data: formData,
                        type: 'POST',
                        beforeSend: function () {
                           _loading.removeClass('d-none');
                        },
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                           
                           if (response.status == 'success') {
                              $('.jd-simple-contact-message-<?php echo $module->id; ?>').html(response.data.message);
                              _loading.addClass('d-none');
                              if (response.data.redirect != '') {
                                 setTimeout(function () {
                                    window.location = response.data.redirect;
                                 }, 2000);
                              }
                           } else {
                              _loading.addClass('d-none');
                              
                              if ( response.message == "[]" ) {
                                 showMessage<?php echo $module->id; ?>("error", "<?php echo JText::_("MOD_JDSCF_UNSUPPORTED_MAIL_CLIENT_ERROR"); ?>");
                              }
                              else if(typeof response.message == "string") 
                              {
                                 showMessage<?php echo $module->id; ?>("error", response.message);
                              }
                              else 
                              {
                                 var errors = JSON.parse(response.message);

                                 for (index = 0; index < errors.length; ++index) {
                                    showMessage<?php echo $module->id; ?>("error", errors[index]);
                                 }
                              }
                           }
                        },
                        error: function (response) {
                           _loading.addClass('d-none');
                           showMessage<?php echo $module->id; ?>("error", "<?php echo JText::_("MOD_JDSCF_AJAX_ERROR_ON_SUBMIT"); ?>");
                        }
                     });
                  }
               });
            });

            // Checking for 🍪s
            function checkCookie() {
               var cookieEnabled = navigator.cookieEnabled;
               if ( !cookieEnabled ) {
                  document.cookie = "cookieforjdscf";
                  cookieEnabled = document.cookie.indexOf("cookieforjdscf") != -1;
               }
               if ( cookieEnabled == false ) {
                  $('.cookie-notice').show();
               }
            }
            checkCookie();

         })(jQuery_3_3_1);
      </script>
   <?php } ?>
<?php }
?>
