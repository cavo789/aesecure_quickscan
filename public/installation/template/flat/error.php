<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

?>
<html>
<head>
    <title>ANGIE - Akeeba Next Generation Installation Engine v. <?php echo AKEEBA_VERSION ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="template/flat/css/fef.min.css"/>
    <link rel="stylesheet" type="text/css" href="template/flat/css/dark.min.css"/>
    <link rel="stylesheet" type="text/css" href="template/flat/css/theme.min.css"/>
    <script type="text/javascript" src="template/flat/js/menu.min.js"></script>
    <script type="text/javascript">window.addEventListener('DOMContentLoaded', function(event) { akeeba.fef.menuButton(); akeeba.fef.tabs(); });</script>
	<style>html{font-size: 11pt}</style>
</head>
<body class="akeeba-renderer-fef">
<!--[if IE]><div class="ie9"><![endif]-->

<header class="akeeba-navbar">
    <div class="akeeba-maxwidth akeeba-flex">
        <!-- Branding -->
        <div class="akeeba-nav-logo">
            <a href="#">
                <span class="aklogo-deluxe-j"></span>
                <span class="akeeba-product-name">
                        Akeeba Backup Site Restoration Script
                        <span class="akeeba-label--red--small">v.<?php echo AKEEBA_VERSION ?></span>
                    </span>
            </a>
            <a href="#" class="akeeba-menu-button akeeba-hidden-desktop akeeba-hidden-tablet"
               title="Toggle Navigation"></a>
        </div>

        <!-- Navigation -->
        <nav>
        </nav>
    </div>
</header>
<div class="akeeba-maxwidth">

    <?php echo $error_message ?>

</div>
<footer id="akeeba-footer">
    <div class="akeeba-maxwidth">
        <div class="akeeba-container--75-25">
            <div>
                <p class="credit">
                    Copyright &copy;2006 &ndash; <?php echo date('Y') ?> Akeeba Ltd. All rights reserved.<br/>
                    ANGIE is Free Software distributed under the
                    <a href="http://www.gnu.org/licenses/gpl.html">GNU GPL version 3</a> or any later version published by the FSF.
                </p>
            </div>
            <div style="text-align: right">
                <a href="https://www.akeeba.com" rel="nofollow" target="_blank" style="color: #cdcdcd">
                    <span class="aklogo-company-logo md"></span>
                </a>
            </div>
        </div>
    </div>
</footer>

<!--[if IE]></div><![endif]-->
</body>
</html>
