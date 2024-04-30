<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2015 templaza.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

if($item = $this -> item):
    $params = $this -> params;
        $lang           = JFactory::getLanguage();

    $addonParams    = $this -> addon -> params;

?>
    
<div class="tpComment">
    <?php if($params->get('comment_type', 'facebook') == 'disqus'){?>
    <div id="disqus_thread"></div>
    <script type="text/javascript">
        var disqus_shortname = "<?php echo $params -> get('disqus_subdomain','templazatoturials'); ?>";
        var disqus_url      = "<?php echo $item -> fullLink;?>";
        var disqus_developer = 1;
        (function() {
            var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
            dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
            (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
        })();
    </script>
    <?php }elseif($params->get('comment_type', 'facebook') == 'facebook'){?>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/<?php echo str_replace('-','_',$lang -> getTag());?>/all.js#xfbml=1&xfbml=1";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, "script", "facebook-jssdk"));</script>
    <div class="fb-comments" data-width="100%" data-href="<?php echo $item -> fullLink;?>" data-num-posts="2"></div>
    <?php }else{
        if(isset($item -> comment) && $item -> comment){
            echo $item -> comment;
        }else{
    ?>
    <div class="tz_comment_notice"><?php echo JText::sprintf('PLG_CONTENT_COMMENT_NOTICE', ucfirst($addonParams -> get('comment_type', 'facebook')));?></div>
    <?php
        }
    }?>
</div>
<?php
endif;