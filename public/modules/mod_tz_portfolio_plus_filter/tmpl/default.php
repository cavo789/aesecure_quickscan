<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2016 tzportfolio.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Family Website: http://www.templaza.com

# Technical Support:  Forum - http://tzportfolio.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$bootstrap4 = ($params -> get('enable_bootstrap',0) && $params -> get('bootstrapversion', 3) == 4);

$doc    = JFactory::getDocument();
if($params -> get('enable_bootstrap',0)){
    $doc -> addScriptDeclaration('
        (function($){
            $(document).off(\'click.modal.data-api\')
            .on(\'click.modal.data-api\', \'[data-toggle="modal"]\', function (e) {
                var $this = $(this)
                  , href = $this.attr(\'href\')
                  , $target = $($this.attr(\'data-target\') || (href && href.replace(/.*(?=#[^\s]+$)/, \'\'))) //strip for ie7
                  , option = $target.data(\'modal\') ? \'toggle\' : $.extend({ remote:!/#/.test(href) && href }, $target.data(), $this.data())
            
                e.preventDefault();
            
                $target
                  .modal(option)
                  .one(\'hide\', function () {
                    $this.focus()
                  });
              });
        })(jQuery);
    ');
}
$doc -> addStyleSheet(JUri::base(true).'/modules/mod_tz_portfolio_plus_filter/css/style.css');

if ($width)
{
    $moduleclass_sfx .= ' ' . 'mod_search' . $module->id;
    $css = 'div.mod_search' . $module->id . ' input[type="search"]{ width:auto; }';
    JFactory::getDocument()->addStyleDeclaration($css);
    $width = ' size="' . $width . '"';
}
else
{
    $width = '';
}

$input  = JFactory::getApplication() -> input;
$column     =   [];
$column[]   =   ($params -> get('column_lg', 1) && intval($params -> get('column_lg', 1))) ? 'col-lg-' . 12/$params -> get('column_lg', 1) : 'col-lg';
$column[]   =   ($params -> get('column_md', 1) && intval($params -> get('column_md', 1))) ? 'col-md-' . 12/$params -> get('column_md', 1) : 'col-md';
$column[]   =   ($params -> get('column_sm', 1) && intval($params -> get('column_sm', 1))) ? 'col-sm-' . 12/$params -> get('column_sm', 1) : 'col-sm';
$column[]   =   ($params -> get('column', 1) && intval($params -> get('column', 1))) ? 'col-' . 12/$params -> get('column', 1) : 'col';
$gutter     =   $params -> get('gutter', '') ? ' '. $params -> get('gutter', '') : '';
$button_width   =   $params -> get('button_width', '') ? ' style="width:'. $params -> get('button_width', '').'px;"' : '';
?>
<div class="tz-filter<?php echo $moduleclass_sfx ?> tpp-bootstrap">
    <form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=search&Itemid='.$mitemid);?>"
          method="post">
        <div class="row<?php echo $gutter; ?>">
        <?php if($params -> get('show_search_word', 1)) { ?>
            <div class="<?php echo implode(' ', $column); ?>">
                <div class="form-group">
                    <?php if($params -> get('show_box_label', 1)){?>
                        <label for="mod_tz_portfolio_plus_filter-searchword" class="form-label"><?php echo $label ?></label>
                    <?php }?>
                    <input type="search" name="searchword" id="mod_tz_portfolio_plus_filter-searchword"
                           maxlength="<?php echo $maxlength; ?>"
                           class="form-control" placeholder="<?php echo $text;?>"
                           value="<?php echo ($input->get('option') == 'com_tz_portfolio_plus') ? $input->getString('searchword') : ''; ?>"/>
                </div>
            </div>
            <?php
        }
        ?>
        <?php if($params -> get('show_category',0)) { ?>
            <div class="<?php echo implode(' ', $column); ?>">
                <div class="form-group">
                    <?php if($params -> get('show_category_text', 1)){ ?>
                        <label class="form-label" for="catid"><?php echo ($text = $params -> get('category_text'))?$text:JText::_('MOD_TZ_PORTFOLIO_PLUS_FILTER_CATEGORY');?></label>
                    <?php } ?>
                    <select name="id" class="form-select" id="catid">
                        <?php echo JHtml::_('select.options', $categoryOptions, 'value', 'text',
                            (($input -> get('option') == 'com_tz_portfolio_plus')?$input -> get('id'):'')); ?>
                    </select>
                </div>
            </div>
            <?php
            }
        if($advfilter && $params -> get('show_fields', 1)){
            if ($params -> get('show_group_title', 0)) echo '<div class="col-12">';
            require JModuleHelper::getLayoutPath('mod_tz_portfolio_plus_filter', 'default_filter');
            if ($params -> get('show_group_title', 0)) echo '</div>';
        }
        ?>
            <div class="align-self-end <?php echo $params -> get('search_inline', 0) ? 'col-auto' : 'col-12'; ?>">
                <?php if($params -> get('button', 1)){?>
                <div class="form-group d-grid"<?php echo $button_width; ?>>
                    <button class="button btn <?php echo $params -> get('button_style', 'btn-primary'); ?>">
                        <?php
                        $btn_output = null;
                        if($imagebutton){
                            if($icon = $params -> get('icon')) {
                                $btn_output = '<img src="'.$icon.'" alt="'.$button_text.'"/>';
                            }elseif($iconClass = $params -> get('icon_class')){
                                $btn_output = '<i class="' .$iconClass.'"></i>';
                            }
                        }
                        echo $btn_output.$button_text;
                        ?>
                    </button>
                </div>
                <?php } ?>
            </div>
        </div>
        <input type="hidden" name="option" value="com_tz_portfolio_plus"/>
        <input type="hidden" name="task" value="search.search"/>
        <input type="hidden" name="Itemid" value="<?php echo $mitemid;?>"/>
    </form>
</div>
