<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;

jimport('joomla.filesystem.file');

class JFormFieldTZMedia extends JFormFieldMedia{
    protected $type = 'TZMedia';

    protected function getName($fieldName)
    {
        $name   = parent::getName($fieldName);
        $element    = $this -> element;

        if(isset($element['index']) && $element['index'] != null){
            $name   = preg_replace('/\[\]$/','['.$element['index'].']',$name);
        }
        return $name;
    }

    protected function getInput()
    {
        $attr       = '';
        $html       = array();
        $field_name = $this -> fieldname;

        if (!empty($this->class))
        {
            $this->class .= ' hasTipImgpath';
        }
        else
        {
            $this->class = 'hasTipImgpath';
        }

        // Initialize some field attributes.
        $class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $disabled = $this->disabled ? ' disabled' : '';

        // Initialize JavaScript field attributes.
        $onchange = $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

        $this -> __set('name',$field_name.'_client');

        $element    = $this -> element;

        $attr .= ' title="' . htmlspecialchars('<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8') . '"';

        // Initialize some field attributes.
        $attr .= !empty($this->class) ? ' class="input-small ' . $this->class . '"' : ' class="input-small"';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name . '_client'.$element['index']);
        }else {
            $this->__set('id', $field_name . '_client');
        }

        $html[] = '	<input type="file" name="' . $this -> name . '" id="' . $this->id . '"' . $attr . ' />';

        $this -> __set('name',$field_name.'_server');
        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name . '_server'.$element['index']);
        }else {
            $this -> __set('id',$field_name.'_server');
        }

        $value  = $this -> value;
        $this -> value  = '';

        $html[] = '<div style="padding-top: 5px;">'.parent::getInput();
        if($value && !empty($value) && is_string($value)){
            $html[] = '<a href="'.JUri::root().str_replace('.'
                    .File::getExt($value),($this -> element['img_prefix']?'_'.$this -> element['img_prefix']:'')
                    .'.'.File::getExt($value),$value).'?time='.time().'"'
                .' class="tp-image-preview tp-image-preview__modal" rel="{handler: \'image\'}" style="display: table; padding-top: 5px;">';

            $urlImg = JUri::root() . str_replace('.' . File::getExt($value),
                    ($this->element['img_prefix'] ? '_' . $this->element['img_prefix'] : '')
                    . '.' . File::getExt($value), $value);

            $urlImg .= '?time=' . time();
            $mUrlImg    = $urlImg;

            $img = '<img src="' . $urlImg. '" style="'
                . ($this->element['img_max-width'] ? 'max-width: 200px; ' : '') . 'cursor: pointer;" title="">';
            $html[] = $img;
            $html[] = '</a>';

            $image = new JImage();
            $image->loadFile(JPATH_SITE . '/' . str_replace('.' . File::getExt($value),
                    ($this->element['img_prefix'] ? '_' . $this->element['img_prefix'] : '')
                    . '.' . File::getExt($value), $value));

//            $imgHtml    = JHtml::_('image', $mUrlImg, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'));
            $imgHtml    = '<img src="' . $mUrlImg. '" alt="'.JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT').'" class="img-fluid">';
            $imgHtml    = '<div style="text-align:center; overflow-y: auto;">'.$imgHtml.'</div>';

            $unix   = null;
            if($this -> multiple){
                $unix   = uniqid();
            }

            $html[] = JHtml::_('bootstrap.renderModal',
                'tp-image-preview__modal-'.$field_name.$unix,
                array(
                    'title' => JText::_('JGLOBAL_PREVIEW'),
//                    'height' => '100%',
//                    'width' => '100%',
//                    'modalWidth' => '60',
//                    'bodyHeight' => '60',
                    'footer' => '<button type="button" class="btn" data-dismiss="modal" data-bs-dismiss="modal">'
                        . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
                ),
                $imgHtml);

            $doc = Factory::getApplication() -> getDocument();
            if(version_compare(JVERSION, '4.0', 'ge')) {
                $doc->addScriptDeclaration('
                    (function($, window){
                        $(document).ready(function(){
                            $("#tp-image-preview__modal-' . $field_name . $unix . '").parent()
                                .find(".tp-image-preview__modal").on("click", function(e){
                                e.preventDefault();
                                $("#tp-image-preview__modal-' . $field_name . $unix . '")
                                    .on("show.bs.modal", function(){
                                        $(this).find(".modal-dialog").width(' . ($image->getWidth() + 2) . ');
                                    })
                                    .modal("show");
                            });
                        });
                    })(jQuery, window);');
            }else{
                $doc->addScriptDeclaration('
                    (function($, window){
                        $(document).ready(function(){
                            $("#tp-image-preview__modal-' . $field_name . $unix . '").parent()
                                .find(".tp-image-preview__modal").on("click", function(e){
                                e.preventDefault();
                                $("#tp-image-preview__modal-' . $field_name . $unix . '")
                                    .on("show.bs.modal", function(){                                    
                                        var imgHeight   = $(this).find("img").prop("naturalHeight");                                        
                                        if($(this).height() < imgHeight){
                                            $(this).find(".modal-body").css("overflow-y", "auto");
                                        }
                                    })
                                    .modal("show");
                            });
                        });
                    })(jQuery, window);');
            }
        }
        $html[] = '</div>';

        $this -> __set('name',$field_name);
        $this -> __set('id',$field_name);

        $html[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="'
            . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"' . $class . $disabled . $onchange . ' />';

        return implode("\n", $html);
    }

    protected function getInput_Bak()
    {
        $attr       = '';
        $field_name = $this -> fieldname;

        // Tooltip for INPUT showing whole image path
        $options = array(
            'onShow' => 'jMediaRefreshImgpathTip',
        );
//        JHtmlBootstrap::tooltip();
//        JHtml::_('behavior.tooltip', '.hasTipImgpath', $options);
        JHtml::_('bootstrap.tooltip', '.hasTipImgpath', $options);

        if (!empty($this->class))
        {
            $this->class .= ' hasTipImgpath';
        }
        else
        {
            $this->class = 'hasTipImgpath';
        }

        $attr .= ' title="' . htmlspecialchars('<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8') . '"';

        // Initialize some field attributes.
        $attr .= !empty($this->class) ? ' class="input-small ' . $this->class . '"' : ' class="input-small"';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

        $html   = array();


        // Initialize some field attributes.
        $class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $disabled = $this->disabled ? ' disabled' : '';

        // Initialize JavaScript field attributes.
        $onchange = $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

        $this -> __set('name',$field_name.'_client');

        $element    = $this -> element;

        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name . '_client'.$element['index']);
        }else {
            $this->__set('id', $field_name . '_client');
        }

        $html[] = '	<input type="file" name="' . $this -> name . '" id="' . $this->id . '"' . $attr . ' />';

        $this -> __set('name',$field_name.'_server');
        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name . '_server'.$element['index']);
        }else {
            $this -> __set('id',$field_name.'_server');
        }

        $value  = $this -> value;
        $this -> value  = '';
        $html[] = '<div style="padding-top: 5px;">'.parent::getInput();
        if($value && !empty($value) && is_string($value)){
            $html[] = '<a href="'.JUri::root().str_replace('.'
                    .File::getExt($value),($this -> element['img_prefix']?'_'.$this -> element['img_prefix']:'')
                    .'.'.File::getExt($value),$value).'?time='.time().'"'
                .' class="tp-image-preview tp-image-preview__modal" rel="{handler: \'image\'}" style="display: table; padding-top: 5px;">';

            $urlImg = JUri::root() . str_replace('.' . File::getExt($value),
                    ($this->element['img_prefix'] ? '_' . $this->element['img_prefix'] : '')
                    . '.' . File::getExt($value), $value) . '?time=' . time();
            $img = '<img src="' . $urlImg. '" style="'
                . ($this->element['img_max-width'] ? 'max-width: 200px; ' : '') . 'cursor: pointer;" title="">';
            $html[] = $img;
            $html[] = '</a>';

//            if(version_compare(JVERSION, '4.0', 'ge')) {
                $image = new JImage();
                $image->loadFile(JPATH_SITE . '/' . str_replace('.' . File::getExt($value),
                        ($this->element['img_prefix'] ? '_' . $this->element['img_prefix'] : '')
                        . '.' . File::getExt($value), $value));

                $imgHtml    = JHtml::_('image', $urlImg, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'));

                $unix   = null;
                if($this -> multiple){
                    $unix   = uniqid();
                }

                $html[] = JHtml::_('bootstrap.renderModal',
                    'tp-image-preview__modal-'.$field_name.$unix,
                    array(
                        'title' => JText::_('JGLOBAL_PREVIEW'),
                        'height' => '100%',
                        'width' => '100%',
                        'modalWidth' => '60',
                        'bodyHeight' => '60',
                    ),
                    $imgHtml);

                $doc = Factory::getApplication() -> getDocument();
                if(version_compare(JVERSION, '4.0', 'ge')) {
                    $doc->addScriptDeclaration('     
                    (function($, window){
                        $(document).ready(function(){
                            $("#tp-image-preview__modal-' . $field_name . $unix . '").parent()
                                .find(".tp-image-preview__modal").on("click", function(e){
                                e.preventDefault();
                                $("#tp-image-preview__modal-' . $field_name . $unix . '")
                                    .on("show.bs.modal", function(){
                                        $(this).find(".modal-dialog").width(' . ($image->getWidth() + 2) . ');
                                    })
                                    .modal("show");
                            });
                        });
                    })(jQuery, window);');
                }else{
                    $doc->addScriptDeclaration('     
                    (function($, window){
                        $(document).ready(function(){
                            $("#tp-image-preview__modal-' . $field_name . $unix . '").parent()
                                .find(".tp-image-preview__modal").on("click", function(e){
                                e.preventDefault();
                                $("#tp-image-preview__modal-' . $field_name . $unix . '")
                                    .on("show.bs.modal", function(){
                                        $(this).width(' . ($image->getWidth() + 2) . ');
                                    })
                                    .modal("show");
                            });
                        });
                    })(jQuery, window);');
                }
//            }
//            else{
//                JHtmlBehavior::modal();
//                JHtml::_('behavior.modal','.tp-image-preview__modal');
//            }
        }
        $html[] = '</div>';

        $this -> __set('name',$field_name);
        $this -> __set('id',$field_name);

        $html[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="'
            . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"' . $class . $disabled . $onchange . ' />';


        return implode("\n", $html);

    }
}