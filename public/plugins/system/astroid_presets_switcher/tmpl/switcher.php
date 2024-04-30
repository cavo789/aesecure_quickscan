<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
defined('_ASTROID') or die;

use Astroid\Framework;
use Joomla\CMS\Uri\Uri;

$uri = Uri::getInstance();

$presets = Framework::getTemplate()->getPresets();
if (empty($presets)) {
    return;
}
$params = Framework::getTemplate()->getParams();

$primary = $params->get('theme_primary', '');

?>
<div class="astroid-preset-switcher bg-white p-4">
    <div class="astroid-presets">
        <?php foreach ($presets as $presetname => $preset) {
            $uri->setVar('preset', $presetname);
            $active = (@$preset['preset']['theme_primary'] == $primary);
        ?>
            <a class="d-block border text-center bg-light mb-4<?php echo $active ? ' border-primary' : ''; ?>" title="<?php echo $preset['title']; ?>" href="<?php echo $uri->toString(); ?>">
                <div class="astroid-preset">
                    <?php if (!empty($preset['thumbnail'])) { ?>
                        <img src="<?php echo $preset['thumbnail']; ?>" alt="<?php echo $preset['title']; ?>"/>
                    <?php } ?>
                    <span class="small text-uppercase font-weight-bold"><?php echo $preset['title']; ?></span>
                </div>
            </a>
        <?php } ?>
    </div>
    <a href="javascript:void(0);" onclick="toggleAstroidPresets()" title="Template Presets" class="astroid-preset-switcher-toggle bg-dark text-light"><span class="fa fa-cog"></span></a>
</div>