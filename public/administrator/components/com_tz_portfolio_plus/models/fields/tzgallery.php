<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    Sonny

# copyright Copyright (C) 2020 tzportfolio.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum.html

-------------------------------------------------------------------------*/

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;

/**
 * Form Field class for the Joomla Platform.
 *
 * Provides a pop up date picker linked to a button.
 * Optionally may be filtered to use user's or server's time zone.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldTZGallery extends JFormField
{

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'TZGallery';

    /**
     * Method to get the field input markup.
     *
     * @return  string   The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        $this -> value  = (object) $this -> value;
        $class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $app            =   Factory::getApplication();
        $input          =   $app -> input;
        $gallery_tmp       =   uniqid('gallery_');
        $gallery_file_type =   'bmp,gif,jpg,jpeg,png';
        $gallery_file_type =   explode(',', $gallery_file_type);
        for ($i = 0 ; $i< count($gallery_file_type); $i++) {
            $gallery_file_type[$i]  =   '"'.trim($gallery_file_type[$i]).'"';
        }
        $gallery_file_type=   is_array($gallery_file_type) ? implode(',', $gallery_file_type) : '';
        $doc            = Factory::getApplication() -> getDocument();
        $doc->addStyleSheet(JUri::root().'media/tz_portfolio_plus/dm_uploader/css/jquery.dm-uploader.min.css');
        $doc->addStyleSheet(JUri::root().'media/tz_portfolio_plus/gallery_upload/css/style.css');
        $doc->addScript(JUri::root().'media/tz_portfolio_plus/dm_uploader/js/jquery.dm-uploader.min.js');
        $ajaxUrl    =   'index.php?option=com_tz_portfolio_plus&task=ajax.gallery_upload&folder='.$gallery_tmp;
        $doc->addScriptDeclaration('
    var GalleryContent = window.GalleryContent || {};
    jQuery.extend(GalleryContent, {
    ajaxUrl                : "'.$ajaxUrl.'",
    maxFileSize            : 20,
    extFilter              : ['.$gallery_file_type.']
    });
    ');
        $doc->addScript(JUri::root().'media/tz_portfolio_plus/gallery_upload/js/style-ui.js');
        $doc->addScript(JUri::root().'media/tz_portfolio_plus/gallery_upload/js/gallery_uploader.js');
        ob_start();
        ?>


    <div class="container-addon">
        <div class="row-addon">
            <div class="col-addon">

                <!-- Our markup, the important part here! -->
                <div id="gallery_uploader" class="dm-uploader p-5">
                    <h3 class="mb-5 mt-5 text-muted"><?php echo JText::_('PLG_CONTENT_GALLERY_DROP_DRAG'); ?></h3>

                    <div class="btn btn-primary btn-block mb-5">
                        <span><?php echo JText::_('PLG_CONTENT_GALLERY_OPEN_FILE'); ?></span>
                        <input type="file" title='Click to add Files' />
                    </div>
                </div><!-- /uploader -->

            </div>
            <div class="col-addon">
                <div class="card h-100">
                    <div class="card-header">
                        <?php echo JText::_('PLG_CONTENT_GALLERY_FILE_LIST'); ?>
                    </div>

                    <ul class="list-unstyled p-2 d-flex flex-column col" id="gallery_files">
                        <?php if (isset($this->value->gallery_image) && count($this->value->gallery_image)) : ?>
                            <?php for ($i=0; $i<count($this->value->gallery_image); $i++) :
                                $image     =   $this->value->gallery_image[$i];
                                $title     =   isset($this->value->gallery_image_title) ? $this->value->gallery_image_title[$i] : '';
                                ?>
                                <li class="media" data-name="<?php echo $image; ?>" data-source="server">
                                    <img class="mr-3 mb-2 preview-img" src="<?php echo JUri::root().'/images/tz_portfolio_plus/gallery/'.$input->getInt('id').'/'.$image; ?>" alt="Generic placeholder image">
                                    <div class="media-body mb-1">
                                        <p class="mb-2">
                                            <strong class="filename"><?php echo $image; ?></strong> - Status: <span class="status text-success">Available</span> - <em class="grid_featured"><input type="radio" name="<?php echo $this->name; ?>[image_featured]" class="grid_image_featured" value="<?php echo $image; ?>"<?php if (isset($this->value->image_featured) && $this->value->image_featured == $image) echo ' checked="checked"'; ?> /> <?php echo JText::_('JFEATURED'); ?></em> - <a href="#" class="delete_grid_image"><?php echo JText::_('JACTION_DELETE'); ?></a>
                                        </p>
                                        <p class="mb-2">
                                            <input type="text" class="inputbox" name="<?php echo $this->name; ?>[gallery_image_title][]" placeholder="Title..." value="<?php echo $title; ?>" />
                                        </p>
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-primary bg-success"
                                                 role="progressbar"
                                                 style="width: 100%"
                                                 aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <hr class="mt-1 mb-1" />
                                    </div>
                                    <input type="hidden" name="<?php echo $this->name; ?>[gallery_image][]" class="gallery_url" value="<?php echo $image; ?>" />
                                    <input type="hidden" name="<?php echo $this->name; ?>[gallery_source][]" class="gallery_source" value="server" />
                                </li>
                            <?php endfor; ?>
                        <?php endif; ?>
                        <li class="text-muted text-center empty"<?php if (isset($this->value->gallery_image) && is_array($this->value->gallery_image) && count($this->value->gallery_image)) echo ' style="display: none;"'; ?>><?php echo JText::_('PLG_CONTENT_GALLERY_NO_FILE_UPLOADED'); ?></li>
                    </ul>
                </div>
            </div>
        </div><!-- /file list -->

        <div class="row-addon">
            <div class="col-addon">
                <div class="card h-100">
                    <div class="card-header">
                        <?php echo JText::_('PLG_CONTENT_GALLERY_DEBUG_MESSAGES'); ?>
                    </div>

                    <ul class="list-group list-group-flush" id="gallery_debug">
                        <li class="list-group-item text-muted empty"><?php echo JText::_('PLG_CONTENT_GALLERY_LOADING_PLUGIN'); ?></li>
                    </ul>
                </div>
            </div>
        </div> <!-- /debug -->

    </div> <!-- /container -->
    <input type="hidden" name="<?php echo $this->name; ?>[gallery_folder]" value="<?php echo $gallery_tmp; ?>" />
    <!-- File item template -->
    <script type="text/html" id="gallery_files_template">
        <li class="media">
            <img class="mr-3 mb-2 preview-img" src="https://via.placeholder.com/150" alt="Generic placeholder image">
            <div class="media-body mb-1">
                <p class="mb-2">
                    <strong class="filename">%%filename%%</strong> - Status: <span class="text-muted">Waiting</span> - <em class="grid_featured"><input type="radio" name="<?php echo $this->name; ?>[image_featured]" class="grid_image_featured" value="" /> <?php echo JText::_('JFEATURED'); ?></em> - <a href="#" class="delete_grid_image"><?php echo JText::_('JACTION_DELETE'); ?></a>
                </p>
                <p class="mb-2">
                    <input type="text" class="inputbox" name="<?php echo $this->name; ?>[gallery_image_title][]" placeholder="Title..." />
                </p>
                <div class="progress mb-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                         role="progressbar"
                         style="width: 0%"
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <hr class="mt-1 mb-1" />
            </div>
            <input type="hidden" name="<?php echo $this->name; ?>[gallery_image][]" class="gallery_url" value="" />
            <input type="hidden" name="<?php echo $this->name; ?>[gallery_source][]" class="gallery_source" value="client" />
        </li>
    </script>

    <!-- Debug item template -->
    <script type="text/html" id="gallery_debug_template">
        <li class="list-group-item text-%%color%%"><strong>%%date%%</strong>: %%message%%</li>
    </script>

<?php
        $html   =   '</div><div ' . $class . ' style="margin-top: 30px;">' .ob_get_clean();
//        $html   .= '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" class="tzgallery" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" />';
        return $html;
    }
}
