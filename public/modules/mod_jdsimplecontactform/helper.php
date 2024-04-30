<?php

/**
 * @package   JD Simple Contact Form
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2021 Joomdev, Inc. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// no direct access
defined('_JEXEC') or die;

class ModJDSimpleContactFormHelper {

   const JOOMLA_VERSION = \Joomla\CMS\Version::MAJOR_VERSION;

   public static function renderForm($params, $module) {
      $fields = $params->get('fields', []);
      foreach ($fields as $field) {
         $field->id = \JFilterOutput::stringURLSafe('jdscf-' . $module->id . '-' . $field->name);
         self::renderField($field, $module, $params);
      }
   }

   public static function renderField($field, $module, $params) {
      $label = new JLayoutFile('label', JPATH_SITE . '/modules/mod_jdsimplecontactform/layouts');
      $field_layout = self::getFieldLayout($field->type);
      $input = new JLayoutFile('fields.' . $field_layout, JPATH_SITE . '/modules/mod_jdsimplecontactform/layouts');
      $layout = new JLayoutFile('field', JPATH_SITE . '/modules/mod_jdsimplecontactform/layouts');
      if ($field->type == 'checkbox' || $field->type == 'hidden') {
         $field->show_label = 0;
      }
      echo $layout->render(['field' => $field, 'label' => $label->render(['field' => $field]), 'input' => $input->render(['field' => $field, 'label' => self::getLabelText($field), 'module' => $module, 'params' => $params]), 'module' => $module]);
   }

   public static function getOptions($options) {
      $options = explode("\n", $options);
      $array = [];
      foreach ($options as $option) {
         if (!empty($option)) {
            $array[] = ['text' => $option, 'value' => trim( $option )];
         }
      }
      return $array;
   }

   public static function getLabelText($field) {
      $label = $field->label;
      if (empty($label)) {
         $label = ucfirst($field->name);
      } else {
         $label = JText::_($label);
      }
      return $label;
   }

   public static function getFieldLayout($type) {
      $return = '';
      if (file_exists(JPATH_SITE . '/modules/mod_jdsimplecontactform/layouts/fields/' . $type . '-custom.php')) {
         // For adding custom files
         $return = $type . '-custom';
      } else if (file_exists(JPATH_SITE . '/modules/mod_jdsimplecontactform/layouts/fields/' . $type . '.php')) {
         $return = $type;
      } else {
         $return = 'text';
      }
      return $return;
   }

   public static function submitForm($ajax = false) {
      if (!JSession::checkToken()) {
         throw new \Exception(JText::_("JINVALID_TOKEN"));
      }
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         throw new \Exception(JText::_('MOD_JDSCF_BAD_REQUEST'), 400);
      }
      $app = JFactory::getApplication();
      $jinput = $app->input->post;

      $jdscf = $jinput->get('jdscf', [], 'ARRAY');
      $id = $jinput->get('id', [], 'INT');
      $params = self::getModuleParams();

      if ($params->get('captcha', 0)) {

         $captchaType = $params->get('captchaPlugins') == "" ? JFactory::getConfig()->get('captcha') : $params->get('captchaPlugins');
         JPluginHelper::importPlugin('captcha', $captchaType);
         if( ModJDSimpleContactFormHelper::getJoomlaVersion() < 4 ) {
            $dispatcher = JEventDispatcher::getInstance();
         } else {
            $dispatcher = \Joomla\CMS\Factory::getApplication();
         }

         if ( $captchaType == "recaptcha" ) {
            if( ModJDSimpleContactFormHelper::getJoomlaVersion() < 4 ) {
               $check_captcha = $dispatcher->trigger('onCheckAnswer', $jinput->get('recaptcha_response_field'));
            } else {
               $check_captcha = $dispatcher->triggerEvent('onCheckAnswer', [ $jinput->get('recaptcha_response_field') ] );
            }
            
            if (!$check_captcha[0]) {
               throw new \Exception(JText::_('Invalid Captcha'), 0);
            }
         } elseif ( $captchaType == "recaptcha_invisible" ) {
            if( ModJDSimpleContactFormHelper::getJoomlaVersion() < 4 ) {
               $check_captcha = $dispatcher->trigger('onCheckAnswer', $jinput->get('g-recaptcha-response'));
            } else {
               $check_captcha = $dispatcher->triggerEvent('onCheckAnswer', [ $jinput->get('g-recaptcha-response') ] );
            }
            
         } elseif (!empty($captchaType)) {
            if( ModJDSimpleContactFormHelper::getJoomlaVersion() < 4 ) {
               $check_captcha = $dispatcher->trigger('onCheckAnswer');
            } else {
               $check_captcha = $dispatcher->triggerEvent('onCheckAnswer', [] );
            }  
         }
      }

      $labels = [];
      foreach ($params->get('fields', []) as $field) {
         $labels[$field->name] = ['label' => self::getLabelText($field), 'type' => $field->type];
      }

      $cc_emails = [];
      $values = [];
      foreach ($jdscf as $name => $value) {
         if(is_array($value)) {

            // Type email values
            if(isset($value['email'])) {
               $values[$name] = $value['email'];
               
               //single cc
               if(isset($value['single_cc']) && $value['single_cc'] == 1) {
                  $cc_emails[] = $value['email'];
               }
            }
			
            // Type text values
            ( isset($value['text'] ) ? $values[$name] = $value['text'] : '');
            
            // Type number values
            ( isset($value['number'] ) ? $values[$name] = $value['number'] : '');

            // Type url values
            ( isset($value['url'] ) ? $values[$name] = $value['url'] : '');

            // Type Hidden Value
            ( isset($value['hidden'] ) ? $values[$name] = $value['hidden'] : '');

         } else {
            $values[$name] = $value;
         }
      }

      $contents = [];
      $attachments = [];
      $errors = [];
      // Get all error messages and add them to $errors variable
      $messages = $app->getMessageQueue();
      if (!empty($messages)) {
         for ($i=0; $i < count($messages); $i++) { 
            $errors[] = $messages[$i]["message"];
         }
      }
      foreach ($labels as $name => $fld) {
         $value = isset($values[$name]) ? $values[$name] : '';

         if ($fld['type'] == 'checkboxes') {
            if ( isset ($_POST['jdscf'][$name]['cbs'] ) ) {
               $value = $_POST['jdscf'][$name]['cbs'];
            }
            
            if (is_array($value)) {
               $value = implode(', ', $value);
            } else {
               $value = $value;
            }
         }        
         if ($fld['type'] == 'checkbox') {
            if (isset($_POST['jdscf'][$name]['cb'])){
               $value = $_POST['jdscf'][$name]['cb'];
            }            
            if (is_array($value)) {
               $value = implode(',', $value);
            } else {
               $value = $value;
            }
            $value = empty($value) ? 'unchecked' : 'checked';
         }

         if ($fld['type'] == 'file') {
            if(isset($_FILES['jdscf']['name'][$name])) {
               $value = $_FILES['jdscf']['name'][$name];
               $uploaded = self::uploadFile($_FILES['jdscf']['name'][$name], $_FILES['jdscf']['tmp_name'][$name]);
               //filetype error
               if(!empty($value)) {
                  if(!$uploaded) {
                     $errors[] = JText::_('MOD_JDSCF_UNSUPPORTED_FILE_ERROR');
                  }
               }               
               if(!empty($uploaded)) {
                  $attachments[] = $uploaded;
               }
            }
         }
         if ($fld['type'] == 'textarea') {
            if ($value) {
               $value = nl2br($value);
            }
         }

         $contents[] = [
             "value" => $value,
             "label" => $fld['label'],
             "name" => $name,
         ];
      }

      // Fetches IP Address of Client
      if ( $params->get('ip_info' ) ) {
         if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
         }
         elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
         }
         else {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
         }

         $contents[] = array( 
            "value" => "<a href='http://whois.domaintools.com/$ipAddress'>$ipAddress</a>",  
            "label" => "IP Address", 
            "name" => "ip"
         );
      }

      if ($params->get('email_template', '') == 'custom') {
         $html = $params->get('email_custom', '');
         if ( empty( $html ) ) {
            $layout = new JLayoutFile('emails.default', JPATH_SITE . '/modules/mod_jdsimplecontactform/layouts');
            $html = $layout->render(['contents' => $contents]);
         } else {
            $html = self::renderVariables($contents, $html);  
         }
      } else {
         $layout = new JLayoutFile('emails.default', JPATH_SITE . '/modules/mod_jdsimplecontactform/layouts');
         $html = $layout->render(['contents' => $contents]);
      }

      // sending mail
      $mailer = JFactory::getMailer();
      $config = JFactory::getConfig();
      $title = $params->get('title', '');
      if (!empty($title)) {
         $title = ' : ' . $title;
      }
      // Sender
      if (!empty($params->get('email_from', ''))) {
         $email_from = $params->get('email_from', '');
         $email_from = self::renderVariables($contents, $email_from);
         if (!filter_var($email_from, FILTER_VALIDATE_EMAIL)) {
            $email_from = $config->get('mailfrom');
         }
      } else {
         $email_from = $config->get('mailfrom');
      }

      if (!empty($params->get('email_name', ''))) {
         $email_name = $params->get('email_name', '');
         $email_name = self::renderVariables($contents, $email_name);
         if (empty($email_name)) {
            $email_name = $config->get('fromname');
         }
      } else {
         $email_name = $config->get('fromname');
      }

      $sender = array($email_from, $email_name);
      $mailer->setSender($sender);

      // Subject
      $email_subject = !empty($params->get('email_subject', '')) ? $params->get('email_subject') : JText::_('MOD_JDSCF_DEFAULT_SUBJECT', $title);
      $email_subject = self::renderVariables($contents, $email_subject);
      $mailer->setSubject($email_subject);

      // Recipient
      $recipients = !empty($params->get('email_to', '')) ? $params->get('email_to') : $config->get('mailfrom');
      $recipients = explode(',', $recipients);
      if (!empty($recipients)) {
         $mailer->addRecipient($recipients);
      }

      // Reply-To
      if (!empty($params->get('reply_to', ''))) {
         $reply_to = $params->get('reply_to', '');
         $reply_to = self::renderVariables($contents, $reply_to);
         if (!filter_var($reply_to, FILTER_VALIDATE_EMAIL)) {
            $reply_to = '';
         }
         $mailer->addReplyTo($reply_to);
      } else {
         $reply_to = '';
      }

      // CC
      $cc = !empty($params->get('email_cc', '')) ? $params->get('email_cc') : '';
      $cc = empty($cc) ? [] : explode(",", $cc);
      if(!empty($cc_emails)){
         $cc = array_merge($cc, $cc_emails);
         $cc = array_unique($cc);
      }

      if (!empty($cc)) {
         $mailer->addCc($cc);
      }
      // BCC
      $bcc = !empty($params->get('email_bcc', '')) ? $params->get('email_bcc') : '';
      $bcc = empty($bcc) ? [] : explode(',', $bcc);
      if (!empty($bcc)) {
         $mailer->addBcc($bcc);
      }
      $mailer->isHtml(true);
      $mailer->Encoding = 'base64';
      $mailer->setBody($html);
      foreach($attachments as $attachment){
         $mailer->addAttachment($attachment);
      }
      if(!empty($errors)) {
         $app = JFactory::getApplication();
         $send = false;
         // showing all the validation errors
         foreach ($errors as $error) {
            $app->enqueueMessage(\JText::_($error), 'error');
         }
      }
      else {
         $send = $mailer->Send();
      }

      if ($send !== true) {
         switch($params->get('ajaxsubmit'))
         {
            case 0: throw new \Exception(JText::_('MOD_JDSCFEMAIL_SEND_ERROR'));
            break;
            case 1: throw new \Exception(json_encode($errors));
            break;
         }         
      }
      $message = $params->get('thankyou_message', '');
      if (empty($message)) {
         $message = JText::_('MOD_JDSCF_THANKYOU_DEFAULT');
      } else {
         $template = $params->get('email_custom', '');
         $message = self::renderVariables($contents, $message);
      }
      $redirect_url = $params->get('redirect_url', '');
      $redirect_url = self::renderVariables($contents, $redirect_url);
      if (!$ajax) {
         $return = !empty($redirect_url) ? $redirect_url : urldecode($jinput->get('returnurl', '', 'RAW'));
         $session = JFactory::getSession();
         if (empty($redirect_url)) {
            $session->set('jdscf-message-' . $id, $message);
         } else {
            $session->set('jdscf-message-' . $id, '');
         }
         $app->redirect($return);
      }
      return ['message' => $message, 'redirect' => $redirect_url, 'errors' => json_encode($errors)];
   }

   public static function renderVariables($variables, $source) {
      foreach ($variables as $content) {
         $value = is_array($content['value']) ? implode(', ', $content['value']) : $content['value'];
         $value = empty($value) ? '' : $value;
         $label = empty($content['label']) ? '' : $content['label'];
         $source = str_replace('{' . $content['name'] . ':label}', $label, $source);
         $source = str_replace('{' . $content['name'] . ':value}', $value, $source);
      }
      return $source;
   }

   public static function getModuleParams() {
      $app = JFactory::getApplication();
      $jinput = $app->input->post;
      $id = $jinput->get('id', 0);
      $params = new JRegistry();

      $db = JFactory::getDbo();
      $query = "SELECT * FROM `#__modules` WHERE `id`='$id'";
      $db->setQuery($query);
      $result = $db->loadObject();
      if (!empty($result)) {
         $params->loadString($result->params, 'JSON');
      } else {
         throw new \Exception(JText::_('MOD_JDSCF_MODULE_NOT_FOUND'), 404);
      }
      return $params;
   }

   public static function submitAjax() {
      try {
         self::submitForm();
      } catch (\Exception $e) {
         $app = JFactory::getApplication();
         $params = self::getModuleParams();
         $jinput = $app->input->post;
         $app->enqueueMessage($e->getMessage(), 'error');
         $redirect_url = $params->get('redirect_url', '');
         $return = !empty($redirect_url) ? $redirect_url : urldecode($jinput->get('returnurl', '', 'RAW'));
         $app->redirect($return);
      }
   }

   public static function submitFormAjax() {
      header('Content-Type: application/json');
      header('Access-Control-Allow-Origin: *');
      $return = array();
      try {
         $data = self::submitForm(true);
         $return['status'] = "success";
         $return['code'] = 200;
         $return['data'] = $data;
      } catch (\Exception $e) {
         $return['status'] = "error";
         $return['code'] = $e->getCode();
         $return['message'] = $e->getMessage();
         $return['line'] = $e->getLine();
         $return['file'] = $e->getFile();
      }
      echo \json_encode($return);
      exit;
   }

   public static function addJS($js, $moduleid) {
      if (!isset($GLOBALS['mod_jdscf_js_' . $moduleid])) {
         $GLOBALS['mod_jdscf_js_' . $moduleid] = [];
      }
      $GLOBALS['mod_jdscf_js_' . $moduleid][] = $js;
   }

   public static function getJS($moduleid) {
      if (!isset($GLOBALS['mod_jdscf_js_' . $moduleid])) {
         return [];
      }
      return $GLOBALS['mod_jdscf_js_' . $moduleid];
   }

   //for single email field (at bottom)
   public static function isSingleCCMail($params) {      
      $singlesendcopy_email = $params->get('single_sendcopy_email', 0);
      $singlesendcopyemail_field = $params->get('singleSendCopyEmail_field', '');      
      if($singlesendcopy_email && !empty($singlesendcopyemail_field)){
         return true;
      } else {
         return false;
      }
   }

   public static function uploadFile($name, $src) {
      jimport('joomla.filesystem.file');
      jimport('joomla.application.component.helper');

      $fullFileName = JFile::stripExt($name);
      $filetype = JFile::getExt($name);
      $filename = JFile::makeSafe($fullFileName."_".mt_rand(10000000,99999999).".".$filetype);

      $params = JComponentHelper::getParams('com_media');
      
      if( ModJDSimpleContactFormHelper::getJoomlaVersion() < 4 ) {
         $allowable = array_map('trim', explode(',', $params->get('upload_extensions')));
      } else {
         $allowable = array_map('trim', explode(',', $params->get('restrict_uploads_extensions')));
      }

      if ($filetype == '' || $filetype == false || (!in_array($filetype, $allowable) ))
      {
         return false;
      }
      else
      {
         $tmppath = JPATH_SITE . '/tmp';
         if (!file_exists($tmppath.'/jdscf')) {
            mkdir($tmppath.'/jdscf',0777);
         }
         $folder = md5(time().'-'.$filename.rand(0,99999));
         if (!file_exists($tmppath.'/jdscf/'.$folder)) {
            mkdir($tmppath.'/jdscf/'.$folder,0777);
         }
         $dest = $tmppath.'/jdscf/'.$folder.'/'.$filename;

         $return = null;
         if (JFile::upload($src, $dest)) {
            $return = $dest;
         }
         return $return;
      }
   }

   public static function getJoomlaVersion() {
      $jversion = new JVersion();
      return $jversion::MAJOR_VERSION;
   }
}
