<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2020 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

if($itemsServer = $this -> itemsServer){
    $loading    = '<span class="loading"><span class="tps tp-sync-alt text-update tp-spin"></span> '
        .JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLING').'</span>';
    $installed  = '<span class="installed"><span class="tps tp-check"></span> '
        .JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLED').'</span>';
    ?>
    <?php
    foreach ($itemsServer as $i => $item) {
        $detailUrl  = $item -> link;
        if(strpos($detailUrl,'?')){
            $detailUrl  .= '&tmpl=component';
        }else{
            $detailUrl  .= '?tmpl=component';
        }

        $modal_id   = 'tpp-addon__modal-detail-'.$i;
        $addon      = null;
        $version    = $item -> installedVersion;
        ?>
        <div class="tpp-extension__col">
            <div class="tpp-extension__item">
                <div class="top">
                    <h3 class="title">
                        <a data-toggle="modal" data-bs-toggle="modal" href="#<?php echo $modal_id; ?>">
                            <?php echo $item -> title; ?>
                            <?php if(isset($item -> imageUrl) && $item -> imageUrl){ ?>
                                <img src="<?php echo $item -> imageUrl;?>" alt="<?php echo $item -> title; ?>">
                            <?php } ?>
                        </a>
                    </h3>
                    <div class="action-links">
                        <ul class="pl-0 ps-0">
                            <?php
                        $addOnButton    = null;
                        if($item -> pProduce && $item -> pProduce -> pCommercial == true && !$item -> pProduce -> pHasPurchased) {
                            $addOnButton    = 'buynow';
                        }else{
                            $addOnButton = 'install';
                        }

                        if($version && $item -> pProduce){
                            if(!$item -> pProduce ->  pVersion || ($item -> pProduce -> pVersion
                                    && version_compare($version, $item -> pProduce -> pVersion, '>='))){
                                $addOnButton    = 'installed';
                            }elseif($item -> pProduce -> pVersion && version_compare($version, $item -> pProduce -> pVersion, '<')){
                                $addOnButton    = 'update';
                            }
                        }
                        ?>
                            <?php
                        switch ($addOnButton){
                            default:
                            case 'install':
                        ?>
                            <li>
                                <a href="<?php echo $item -> pProduce -> pProduceUrl;
                            ?>" class="install-now btn btn-outline-secondary"><span class="tps tp-download"></span> <?php
                                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_NOW'); ?></a>
                            </li>
                                <?php
                                break;
                            case 'update':
                            ?>
                            <li>
                                <a href="<?php echo $item -> pProduce ->  pProduceUrl;
                            ?>" class="install-now btn btn-outline-secondary"><span class="tps tp-sync-alt text-update"></span> <?php
                                echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPDATE_NOW'); ?></a>
                            </li>
                                <?php
                                break;
                            case 'buynow':
                            ?>
                            <li>
                                <a href="<?php echo $item -> pProduce ->  pProduceUrl?$item -> pProduce ->  pProduceUrl:$item -> link;
                            ?>" target="_blank" class="btn btn-outline-secondary"><span class="tps tp-shopping-cart"></span> <?php
                                echo JText::_('COM_TZ_PORTFOLIO_PLUS_BUY_NOW'); ?></a>
                            </li>
                                    <?php
                                break;
                            case 'installed':
                                ?>
                                    <li><button type="button" class="btn btn-outline-success disabled"><?php echo $installed; ?></li>
                            <?php
                                break;
                        }?>
                            <?php if(isset($item -> liveDemoUrl) && $item -> liveDemoUrl){ ?>
                                <li>
                                    <a target="_blank" class="btn btn-success js-tpp-live-demo" href="<?php
                                echo $item -> liveDemoUrl; ?>"><i class="tpr tp-eye"></i> <?php
                                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_LIVE_DEMO');?></a>
                                </li>
                            <?php } ?>
                            <li>
                                <a data-toggle="modal" data-bs-toggle="modal" href="#<?php echo $modal_id;
                                ?>" data-url="<?php echo $detailUrl; ?>"><?php
                                echo JText::_('COM_TZ_PORTFOLIO_PLUS_MORE_DETAIL');?></a>
                            </li>
                        </ul>
                    </div>
                    <div class="desc">
                        <?php echo $item -> introtext; ?>
                        <p class="author">
                            <?php
                        $author = '<strong>'.$item -> author.'</strong>';
                        echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_BY', $author);
                        ?>
                        </p>
                    </div>
                    <?php
                    $modalData  = array(
                        'title'      => $item -> title,
                        'url'        => $detailUrl,
                        'width'      => '400px',
                        'height'     => '800px',
                        'modalWidth' => '70',
                        'bodyHeight' => '70',
                        'closeButton' => true,
                        'modal-dialog-scrollable' => true,
                        'footer'      => '<a class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">' . JText::_('JCANCEL') . '</a>',
                    );
                    echo HTMLHelper::_(
                        'bootstrap.renderModal',
                        $modal_id,
                        $modalData
                    );
                ?>
                </div>
                <div class="bottom">
                    <ul class="unstyled list-unstyled pull-left float-left float-md-start float-none mb-1 mb-md-3">
                        <li><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_LATEST_VERSION', '') ?><span><?php
                            echo $item -> pProduce -> pVersion?$item -> pProduce ->  pVersion:JText::_('COM_TZ_PORTFOLIO_PLUS_NA');
                            ?></span>
                        </li>
                        <li><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALLED_VERSION', ''); ?><span><?php
                            echo $item -> installedVersion?$item -> installedVersion:JText::_('COM_TZ_PORTFOLIO_PLUS_NA');
                            ?></span>
                        </li>
                    </ul>
                    <ul class="unstyled list-unstyled pull-right float-right float-md-end float-none text-right">
                        <li><?php
                        $updated = '<span>'.JHtml::_('date', $item -> modified, JText::_('DATE_FORMAT_LC4')).'</span>';
                        echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_LAST_UPDATED', $updated);
                        ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <?php }
    ?>
<?php
}