<?php
/*------------------------------------------------------------------------
# plg_extravote - ExtraVote Plugin
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomla.vargas.co.cr
# Technical Support:  Forum - http://joomla.vargas.co.cr/forum
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;

jimport('joomla.filesystem.file');

if(isset($this -> item) && $this -> item):

    $params = $this -> params;

    $doc = JFactory::getDocument();
    $doc->addStyleSheet(TZ_Portfolio_PlusUri::root(true).'/addons/content/charity/css/charity.css');

    if($params -> get('load_style', 0)){
        $doc -> addStyleSheet(TZ_Portfolio_PlusUri::root(true).'/addons/content/charity/css/style.css');
    }

    $modelsP    = $this -> getModel();
    $modelAddon = $modelsP->get('addon');
    $idAddon    = $modelAddon -> id;

    $itemID = $this -> item -> id;

    // check currency
    if(isset($this -> currency) && $this -> currency != '') {
        $signCurrency = $this -> currency -> sign;
    }else {
        $signCurrency = '';
    }

    // check show events global
    if($params->get('show_cat_events',0)):
        $crt_evt_start  = $params->get('crt_evt_start','');
        $crt_evt_end    = $params->get('crt_evt_end','');
        if($crt_evt_start != '' && $crt_evt_end != ''):
            $dateStart  = JHtml::_('date', $crt_evt_start, 'd F Y');
            $dateEnd    = JHtml::_('date', $crt_evt_end, 'd F Y');
            $doc->addScript(TZ_Portfolio_PlusUri::root(true).'/addons/content/charity/js/jquery.lwtCountdown-1.0.js');
    ?>
        <div class="evens">
            <?php
            if (($timestamp = strtotime($crt_evt_end)) !== false) {
                $php_date = getdate($timestamp);
                // or if you want to output a date in year/month/day format:
                $date = date("d/m/Y", $timestamp); // see the date manual page for format options
            } else {
                echo 'invalid timestamp!';
            }

            $tzdate		= JFactory::getDate();
            $unix       = $tzdate -> toUnix();

            $second     = 0;
            if($timestamp >= $unix) {
                $second = $timestamp - $unix;
            }

            $day        = (int)($second / (24*60*60));
            $second     = $second - $day * 24 * 60 * 60;

            $hour       = (int)($second/(60*60));
            $second     = $second - $hour * 60 * 60;

            $minute     = (int)($second / 60);
            $second     = $second - $minute * 60;
            ?>
            <div id="countdown_dashboard<?php echo $itemID;?>">

                <div class="dash days_dash">
                    <div class="time_number">
                        <?php if($day && $day > 0 && strlen($day) > 2){
                            for($i = 1; $i <= (strlen($day) - 2); $i++){
                                ?>
                                <div class="digit">0</div>
                                <?php
                            }
                        }?>
                        <div class="digit">0</div>
                        <div class="digit">0</div>
                    </div>
                    <span class="dash_title"><?php echo JText::_('ADDON_DAYS');?></span>
                </div>

                <div class="dash hours_dash">
                    <div class="time_number">
                        <div class="digit">0</div>
                        <div class="digit">0</div>
                    </div>
                    <span class="dash_title"><?php echo JText::_('ADDON_HOURS');?></span>
                </div>

                <div class="dash minutes_dash">
                    <div class="time_number">
                        <div class="digit">0</div>
                        <div class="digit">0</div>
                    </div>
                    <span class="dash_title"><?php echo JText::_('ADDON_MINUTES');?></span>
                </div>

                <div class="dash seconds_dash">
                    <div class="time_number">
                        <div class="digit">0</div>
                        <div class="digit">0</div>
                    </div>
                    <span class="dash_title"><?php echo JText::_('ADDON_SECONDS');?></span>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery('#countdown_dashboard<?php echo $itemID;?>').countDown({
                    targetOffset: {
                        'day': <?php echo $day; ?>,
                        'month': 0,
                        'year': 0,
                        'hour': <?php echo $hour; ?>,
                        'min': <?php echo $minute; ?>,
                        'sec': <?php echo $second; ?>
                    },
                    omitWeeks: true
                });
            });
        </script>

    <?php endif; endif; // check show events global ?>

<?php
endif;

// check show donate global
if($params->get('show_cat_donate',0)):
    $doc->addStyleSheet(TZ_Portfolio_PlusUri::root(true).'/addons/content/charity/css/animate.css');
    $doc->addScript(TZ_Portfolio_PlusUri::root(true).'/addons/content/charity/js/wow.min.js');
    ?>

    <div class="charity">
        <div class="donate-goal">

            <div class="donate-progress">

                <?php
                // Get donated
                if(isset($this->donated) && !empty($this->donated)): $donated    = $this->donated; ?>

                    <?php
                    $donateSum  = (int)$donated["sumDonate"];
                    $goalDonate = (int)$params->get('tz_crt_goal_money',0);
                    if($donateSum != 0 && $goalDonate != 0) {
                        $tlDonate   = ($donateSum*100)/$goalDonate;
                        if($tlDonate > 100) {
                            $tlDonate = 100;
                        }
                    }else {
                        $tlDonate   = 0;
                    }

                    ?>
                    <div class="item-progress">
                        <div class="child-prgb" style="width:<?php echo $tlDonate;?>%;">
                            <div id="prgb_child<?php echo $itemID;?>" class="wow slideInLeft animated">
                            </div>
                        </div>
                    </div>

                    <div class="progress-label">
                        <div class="progress-ed">
                            <?php echo JText::_('ADDON_COLLECTED');?>
                            <span><?php echo $donateSum;?></span>
                        </div>
                        <div class="total">
                            <?php echo JText::_('ADDON_DONATOR');?>
                            <span><?php echo $donated["countDonate"];?></span>
                        </div>
                        <div class="progress-final"><?php echo JText::_('ADDON_DONATE_GOAL');?><span>
                            <?php echo $signCurrency.$goalDonate;?>
                            </span>
                        </div>
                    </div>

                    <?php
                endif;
                ?>
            </div>

            <?php
            // Check button donate
            $donated_status = $params->get('tz_crt_donated_status',0);
            if($donated_status == 1) {
                echo JText::_('SITE_NPF_FINISHED');
            }elseif($donated_status == 2) {
                echo JText::_('SITE_NPF_PAUSE');
            }else {
                ?>
                <button id="tz-charity-donate" class="btn btn-donate btn-sm" type="button" data-toggle="modal" data-target="#form-charity-donate<?php echo $itemID;?>"><?php echo JText::_('SITE_BUTTON_DONATE');?></button>
                <?php
            }
            ?>
        </div>
        <?php if($donated_status != 1 && $donated_status != 2):?>
            <div id="tz-form-donate<?php echo $itemID;?>" class="tz-form-donate">
                <div class="modal fade donate-modal" id="form-charity-donate<?php echo $itemID;?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <div class="content-head">
                                    <?php
                                    $media  = $this -> item -> media;
                                    $imgUrl = $media -> image -> url;
                                    if(isset($imgUrl) && $imgUrl != '') {
                                        if ($size = $params->get('mt_image_size', 'o')) {
                                            $image_url_ext = File::getExt($imgUrl);
                                            $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                                . $image_url_ext, $imgUrl);
                                            $imgUrl = JURI::root() . $image_url;
                                            echo '<div class="bg-header" style="background-image: url('.$imgUrl.')"></div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="modal-body">
                                <?php
                                if($form    = $this -> formDonate):
                                    ?>
                                    <form action="<?php $this -> item -> link;?>" method="post" class="form-horizontal"
                                          enctype="multipart/form-data" id="donateForm" name="donateForm">
                                        <div class="donate-tab">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active">
                                                    <a href="#donateTab<?php echo $itemID;?>" aria-controls="home" role="tab" data-toggle="tab">
                                                        <?php echo JText::_('SITE_DESC_STEP1_DONATE');?></a>
                                                </li>
                                                <li role="presentation">
                                                    <a href="#donateTab<?php echo $itemID;?>2" class="donateTab2" aria-controls="home" role="tab" data-toggle="tab">
                                                        <?php echo JText::_('SITE_DESC_STEP2_DONATE');?></a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="error-select-amount"><?php echo JText::_('SITE_DESC_PLEASE_SELECT_AMOUNT_DONATE');?></div>
                                                <div role="tabpanel" class="tab-pane active" id="donateTab<?php echo $itemID;?>">
                                                    <p class="desc-specify"><?php echo JText::_('SITE_DESC_PLEASE_SPECIFY_DONATE');?></p>
                                                    <?php
                                                    $amounts    = $params -> get('tz_crt_amounts','');
                                                    ?>
                                                    <div class="choose-item">
                                                        <?php
                                                        if($amounts != '') {
                                                            $arrAmount  = explode(',', $amounts);
                                                            foreach($arrAmount as $i => $amV) {
                                                                echo '<div class="item-input"><label>'.$signCurrency.(int)$amV.'</label>' .
                                                                    '<input name="amount'.$itemID.'" id="input_amount_'.$i.'" class="input_donate" type="radio" value="'.(int)$amV.'" />' .
                                                                    '</div>';
                                                            }

                                                        }
                                                        $ct_amounts = $params -> get('tz_crt_ct_amounts','');
                                                        if($ct_amounts != 0) {
                                                            echo '<div class="item-input amount-custom">'
                                                                . JText::_('SITE_DESC_PLEASE_AMOUNT_DONATE_CUSTOM')
                                                                .'<input name="amount-custom" type="text" class="form-control donate-form-text-input" placeholder="$0" value="" />' .
                                                                '</div>';
                                                        }
                                                        ?>
                                                        <div class="donate-number-error">
                                                            <?php echo JText::_('SITE_DONATE_ONLY_NUMBER'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="donateTab<?php echo $itemID;?>2">
                                                    <div class="about-donate">
                                                        <div class="item">
                                                            <?php echo $form -> getInput('firstname','value');?>
                                                        </div>
                                                        <div class="item"><?php echo $form -> getInput('email','value');?></div>
                                                        <div class="item"><?php echo $form -> getInput('address','value');?></div>
                                                        <div class="item"><?php echo $form -> getInput('comment','value');?></div>
                                                        <?php echo $form -> getInput('money_donate','value');?>
                                                    </div>

                                                    <div id="donate-form-submit" class="center-btn">
                                                        <button class="btn btn-primary radius-small" name="ok" type="submit"><?php echo JText::_('SITE_BUTTON_DONATE');?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="option" value="com_tz_portfolio_plus"/>
                                        <input type="hidden" name="view" value="article"/>
                                        <input type="hidden" name="id" value="<?php echo $itemID;?>"/>
                                        <input type="hidden" name="return" value="<?php echo base64_encode($this -> item -> fullLink);?>" />
                                        <input type="hidden" name="addon_view" value="donate"/>
                                        <input type="hidden" name="addon_task" value="charity.donate.process_donation"/>
                                        <input type="hidden" name="addon_id" value="<?php echo $idAddon;?>"/>
                                        <?php echo JHtml::_( 'form.token' ); ?>
                                    </form>
                                    <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif?>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            var $ctDonate = '';
            $('#tz-form-donate<?php echo $itemID;?>.tz-form-donate .choose-item .item-input').on("click", function(){

                $('#tz-form-donate<?php echo $itemID;?> .item-input').removeClass('selected');

                $('#tz-form-donate<?php echo $itemID;?> .input_donate').prop('checked', false);

                $(this).addClass('selected');
                $(this).find('.input_donate').prop('checked', true);
                $('#tz-form-donate<?php echo $itemID;?>.tz-form-donate .donate-form-text-input').val('');
            });

            $("#tz-form-donate<?php echo $itemID;?> .donate-form-text-input").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    //display error message
                    $("#tz-form-donate<?php echo $itemID;?> .donate-number-error").show().fadeOut(1600);
                    return false;
                }else {
                    $("#tz-form-donate<?php echo $itemID;?> .donate-number-error").hide().fadeOut(1600);
                }
            });

            $('#tz-form-donate<?php echo $itemID;?> .donateTab2').on("click", function(){
                if($('#tz-form-donate<?php echo $itemID;?>.tz-form-donate .donate-form-text-input').length > 0) {
                    $ctDonate   = $('#tz-form-donate<?php echo $itemID;?>.tz-form-donate .donate-form-text-input').val();
                }else {
                    $ctDonate   = '';
                }
                if($("#form-charity-donate<?php echo $itemID;?> input[name='amount<?php echo $itemID;?>']").is(':checked') == true || $ctDonate != '') {
                    if($ctDonate == '') {
                        $ctDonate   = $("#form-charity-donate<?php echo $itemID;?> input[name='amount<?php echo $itemID;?>']:checked").val();
                    }
                    return true;
                }else {
                    $('.error-select-amount').show().fadeOut(3000);
                    return false;
                }
            });
            // console.log($ctDonate);
            $('#form-charity-donate<?php echo $itemID;?> #donate-form-submit').on("click", function() {
                $('#form-charity-donate<?php echo $itemID;?> #jform_value_money_donate').val($ctDonate);
            });

            $('#tz-form-donate<?php echo $itemID;?>').on('shown.bs.modal', function () {
                $(this).appendTo('body');
            });
        });
    </script>

<?php endif; // End check show donate global
