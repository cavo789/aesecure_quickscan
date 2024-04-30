<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\FileLayout;
use Astroid\Framework;

/**
 * Astroid Astroid Presets Switcher system plugin
 *
 * @since  1.0
 */
class plgSystemAstroid_presets_switcher extends JPlugin
{
    protected $app;

    public function onBeforeRender()
    {
        $params = $this->params;

        $position = $params->get('position', 'left');
        $alignment = $params->get('alignment', 'top');

        $style = '.astroid-preset-switcher{
            position: fixed;
            top: 0;
            ' . $position . ': -200px;
            height: 100vh;
            width: 200px;
            box-shadow: none;
            z-index: 99999;
            transition: ' . $position . ' 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
        }
        .astroid-preset-switcher .astroid-presets{
            overflow-y: auto;
            height: 100%;
            width: 100%;
        }
        .astroid-preset-switcher.open{
            ' . $position . ': 0px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.35);
            transition: ' . $position . ' 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .astroid-preset-switcher-toggle{
            position: absolute;
            z-index: 1;
            width: 40px;
            height: 40px;
            display: block;
            ' . ($alignment == 'top' ? 'top:0' : ($alignment == 'bottom' ? 'bottom:0' : 'top: calc(50% - 20px)')) . ';
            ' . ($position == 'left' ? 'right' : 'left') . ': 0;
            margin-' . ($position == 'left' ? 'right' : 'left') . ': -40px;
            text-align: center;
            line-height: 40px;
            cursor: pointer;
        }
        ';

        $script = 'function toggleAstroidPresets(){
            document.querySelector(".astroid-preset-switcher").classList.toggle("open");
        }';

        $document = Framework::getDocument();
        $document->addStyleDeclaration($style);
        $document->addScriptDeclaration($script);
    }

    public function onAfterRender()
    {
        if (defined('_ASTROID') && $this->app->isClient('site')) {
            $body = $this->app->getBody();

            $layout = new FileLayout('switcher');
            $layout->addIncludePath(JPATH_SITE . '/plugins/system/astroid_presets_switcher/tmpl');
            $switcher = $layout->render();

            $pos = strrpos($body, '</body>');
            $body = substr_replace($body, $switcher . '</body>', $pos, strlen('</body>'));
            $this->app->setBody($body);
        }
    }
}
