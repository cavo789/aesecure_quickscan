<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var  AngieViewMain  $this */

$version = $this->joomlaVersion ? $this->joomlaVersion : $this->version;
?>
<div class="akeeba-panel--info">
    <header class="akeeba-block-header">
        <h3><?php echo AText::_('MAIN_HEADER_SITEINFO') ?></h3>
    </header>
    <p><?php echo AText::_('MAIN_LBL_SITEINFO') ?></p>
    <table class="akeeba-table--striped" width="100%">
        <tbody>
        <tr>
            <td>
                <label><?php echo AText::_('MAIN_LBL_SITE_JOOMLA') ?></label>
            </td>
            <td class="angie-cms-version"><?php echo $version ?></td>
        </tr>
        <tr>
            <td>
                <label><?php echo AText::_('MAIN_LBL_SITE_PHP') ?></label>
            </td>
            <td><?php echo PHP_VERSION ?></td>
        </tr>
        </tbody>
    </table>
</div>

