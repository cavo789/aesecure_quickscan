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

if($itemsServer = $this -> itemsServer){
    $jTable = JTable::getInstance('Extension');
    foreach ($itemsServer as $i => $item) {
        $detailUrl  = $item -> link;
        if(strpos($detailUrl,'?')){
            $detailUrl  .= '&tmpl=component';
        }else{
            $detailUrl  .= '?tmpl=component';
        }

        $addon      = null;
        $version    = $item -> installedVersion;

        $exID       = null;
//        $enable     = true;
        if($jTable && $jTable -> load(array('type' => 'module', 'element' => $item -> pElement))){
            $exID   = $jTable -> get('extension_id');
//            if(!$jTable -> get('enabled')){
//                $enable = false;
//            }
        }
        ?>
    <div class="tpp-extension__col">
        <div class="tpp-extension__item">
            <div class="top">
                <h3 class="title">
                    <a data-toggle="modal" data-bs-toggle="modal" data-bs-toggle="modal" href="#tpp-addon__modal-detail-<?php echo $i; ?>">
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
                        <?php if(!$addOnButton || $addOnButton == 'install'){?>
                            <li>
                                <a href="<?php echo $item -> pProduce -> pProduceUrl;
                                ?>" class="install-now btn btn-outline-secondary"><span class="tps tp-download"></span> <?php
                                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_NOW'); ?></a>
                            </li>
                        <?php }elseif($addOnButton == 'buynow'){?>
                            <li>
                                <a href="<?php echo $item -> pProduce ->  pProduceUrl?$item -> pProduce ->  pProduceUrl:$item -> link;
                                ?>" target="_blank" class="btn btn-outline-secondary"><span class="tps tp-shopping-cart"></span> <?php
                                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_BUY_NOW'); ?></a>
                            </li>
                        <?php }else{?>
                        <li>
                            <div class="btn-group">
                                <?php if($addOnButton == 'installed'){?>
                                    <button type="button" class="btn btn-outline-success disabled"><span class="installed"><span class="tps tp-check"></span> <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLED'); ?></button>
                                <?php } ?>
                                <?php if($addOnButton == 'update'){?>
                                    <a href="<?php echo $item -> pProduce ->  pProduceUrl;
                                    ?>" class="install-now btn btn-secondary"><span class="tps tp-sync-alt text-update"></span> <?php
                                        echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPDATE_NOW'); ?></a>
                                <?php } ?>
                                <button type="button" class="btn btn-default btn-outline-secondary dropdown-toggle hasTooltip" title="<?php
                                echo JText::_('Actions');?>" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php if(version_compare(JVERSION, '4.0', '<')){ ?>
                                    <span class="tps tp-angle-down"></span>
                                    <?php } ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right pull-right text-left">
                                    <?php if($item -> installedVersion){?>
                                    <li><a href="<?php echo JRoute::_('index.php?option=com_modules&filter_module='
                                            .$item -> pElement); ?>" target="_blank" class="dropdown-item"><i class="tps tp-tools"></i> <?php
                                            echo JText::_('COM_TZ_PORTFOLIO_PLUS_CONFIGURE'); ?></a></li>
                                    <li role="separator" class="divider"></li>
                                    <?php }?>
                                    <?php if($item -> installedVersion){
                                        $exURL  = JRoute::_('index.php?option=com_installer&view=manage&filter_search=id:'
                                            .$exID);
                                        ?>
                                    <li><a href="<?php echo $exURL; ?>" target="_blank" class="dropdown-item"><i class="tpr tp-trash-alt"></i> <?php
                                            echo JText::_('JTOOLBAR_UNINSTALL');?></a></li>
                                    <?php }?>
                                </ul>
                            </div>
                        </li>
                        <?php } ?>
                        <?php if(isset($item -> liveDemoUrl) && $item -> liveDemoUrl){ ?>
                            <li>
                                <a target="_blank" class="btn btn-success js-tpp-live-demo" href="<?php
                                echo $item -> liveDemoUrl; ?>"><i class="tpr tp-eye"></i> <?php
                                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_LIVE_DEMO');?></a>
                            </li>
                        <?php } ?>
                        <li>
                            <a data-toggle="modal" data-bs-toggle="modal" href="#tpp-addon__modal-detail-<?php echo $i;
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
                echo JHtml::_(
                    'bootstrap.renderModal',
                    'tpp-addon__modal-detail-'.$i,
                    array(
                        'url'        => $detailUrl,
                        'title'      => $item -> title,
                        'width'      => '400px',
                        'height'     => '800px',
                        'modalWidth' => '70',
                        'bodyHeight' => '70',
                        'closeButton' => true,
                        'footer'      => '<a class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">' . JText::_('JCANCEL') . '</a>',
                    )
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
}