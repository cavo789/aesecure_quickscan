<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/** @var $this AngieViewDatabase */

$document = $this->container->application->getDocument();

$document->addScript('angie/js/json.min.js');
$document->addScript('angie/js/ajax.min.js');
$document->addScript('angie/js/database.min.js');

$url             = 'index.php';
$dbPassMessage   = AText::_('DATABASE_ERR_COMPLEXPASSWORD');
$dbPassMessage   = str_replace(array("\n", "'"), array('\\n', '\\\''), $dbPassMessage);
$dbPrefixMessage = AText::_('DATABASE_ERR_UPPERCASEPREFIX');
$dbPrefixMessage = str_replace(array("\n", "'"), array('\\n', '\\\''), $dbPrefixMessage);
$dbEmptyMessage = AText::_('DATABASE_ERR_EMPTY_INFO');
$dbEmptyMessage = str_replace(array("\n", "'"), array('\\n', '\\\''), $dbEmptyMessage);
$dbuserEscaped   = addcslashes($this->db->dbuser, '\'\\');
$dbpassEscaped   = addcslashes($this->db->dbpass, '\'\\');

$header = '';

if ($this->number_of_substeps)
{
    $header = AText::_('DATABASE_HEADER_MASTER_MAINDB');

    if ($this->substep != 'site.sql')
    {
        $header = AText::sprintf('DATABASE_HEADER_MASTER', $this->substep);
    }
}

$document->addScriptDeclaration(<<<JS
var akeebaAjax = null;

function angieRestoreDefaultDatabaseOptions()
{
	var elDBUser = document.getElementById('dbuser');
	var elDBPass = document.getElementById('dbpass');
	
	// Chrome auto-fills fields it THINKS are a login form. We need to restore these values. However, we can't just do
	// that, because if the real value is empty Chrome will simply ignore us. So we have to set them to a dummy value
	// and then to the real value. Writing web software is easy. Working around all the ways the web is broken is not.
	elDBUser.value = 'IGNORE ME';
	elDBPass.value = 'IGNORE ME';
	// And now the real value, at last
	elDBUser.value = '$dbuserEscaped';
	elDBPass.value = '$dbpassEscaped';
}

akeeba.System.documentReady(function ()
{
	akeebaAjax = new akeebaAjaxConnector('$url');

	databasePasswordMessage = '$dbPassMessage';
	databasePrefixMessage = '$dbPrefixMessage';
	databaseEmptyMessage = '$dbEmptyMessage';
	
	setTimeout(angieRestoreDefaultDatabaseOptions, 500);
});

JS
);

echo $this->loadAnyTemplate('steps/buttons');
echo $this->loadAnyTemplate('steps/steps', array('helpurl' => 'https://www.akeeba.com/documentation/solo/angie-installers.html#angie-common-database'));
?>

<div id="restoration-dialog" style="display: none">
    <div class="akeeba-renderer-fef">
        <h3><?php echo AText::_('DATABASE_HEADER_DBRESTORE') ?></h3>

        <div id="restoration-progress">
            <div class="akeeba-progress">
                <div class="akeeba-progress-fill" id="restoration-progress-bar" style="width:20%;"></div>
                <div class="akeeba-progress-status" id="restoration-progress-bar-text">
                    20%
                </div>
            </div>
            <table width="100%" class="akeeba-table--leftbold--striped">
                <tbody>
                <tr>
                    <td width="50%"><?php echo AText::_('DATABASE_LBL_RESTORED') ?></td>
                    <td>
                        <span id="restoration-lbl-restored"></span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo AText::_('DATABASE_LBL_TOTAL') ?></td>
                    <td>
                        <span id="restoration-lbl-total"></span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo AText::_('DATABASE_LBL_ETA') ?></td>
                    <td>
                        <span id="restoration-lbl-eta"></span>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="akeeba-block--warning" id="restoration-warnings">
                <h4><?php echo AText::_('DATABASE_HEADER_INPROGRESS_WARNINGS'); ?></h4>
                <p>
				    <?php echo AText::_('DATABASE_MSG_INPROGRESS_WARNINGS'); ?>
                    <br />
                    <code id="restoration-inprogress-log"></code>
                </p>
            </div>
        </div>
        <div id="restoration-success">
            <div class="akeeba-block--success" id="restoration-success-nowarnings">
                <h3><?php echo AText::_('DATABASE_HEADER_SUCCESS'); ?></h3>
            </div>
            <div class="akeeba-block--warning" id="restoration-success-warnings">
                <h4><?php echo AText::_('DATABASE_HEADER_WARNINGS'); ?></h4>
                <p>
				    <?php echo AText::_('DATABASE_MSG_WARNINGS'); ?>
                    <br />
                    <code id="restoration-sql-log"></code>
                </p>
            </div>
            <p>
			    <?php echo AText::_('DATABASE_MSG_SUCCESS'); ?>
            </p>
            <button type="button" onclick="databaseBtnSuccessClick();" class="akeeba-btn--green">
                <span class="akion-arrow-right-c"></span>
			    <?php echo AText::_('DATABASE_BTN_SUCCESS'); ?>
            </button>
        </div>
        <div id="restoration-error">
            <div class="akeeba-block--failure">
			    <?php echo AText::_('DATABASE_HEADER_ERROR'); ?>
            </div>
            <div class="well well-small" id="restoration-lbl-error">

            </div>
        </div>
        <div id="restoration-retry" style="display: none">
            <div class="akeeba-panel--warning">
                <header class="akeeba-block-header">
                    <h3>
                        <?php echo AText::_('DATABASE_HEADER_RESTORERETRY')?>
                    </h3>
                </header>
                <div id="retryframe">
                    <p><?php echo AText::_('DATABASE_LBL_RESTOREFAILEDRETRY') ?></p>
                    <p>
                        <strong>
                            <?php echo AText::_('DATABASE_LBL_WILLRETRY')?>
                            <span id="akeeba-retry-timeout">0</span>
                            <?php echo AText::_('DATABASE_LBL_WILLRETRYSECONDS')?>
                        </strong>
                        <br/>
                        <button class="akeeba-btn--red--small" id="restoration-cancel-resume">
                            <span class="akion-android-cancel"></span>
                            <?php echo AText::_('DATABASE_LBL_CANCEL')?>
                        </button>
                        <button class="akeeba-btn--green--small" id="restoration-resume">
                            <span class="akion-ios-redo"></span>
                            <?php echo AText::_('DATABASE_LBL_BTNRESUME')?>
                        </button>
                    </p>

                    <p><?php echo AText::_('DATABASE_LBL_LASTERRORMESSAGEWAS')?></p>
                    <p id="restoration-error-message-retry"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <button class="akeeba-btn--dark" style="float: right;" onclick="toggleHelp(); return false;">
        <span class="akion-help"></span>
        Show / hide help
    </button>
    <h1><?php echo $header ?></h1>
</div>

<div class="akeeba-container--50-50">
    <div>
        <div class="akeeba-panel--teal" style="margin-top: 0">
            <header class="akeeba-block-header">
                <h3><?php echo AText::_('DATABASE_HEADER_CONNECTION');?></h3>
            </header>

            <?php if ($this->large_tables):?>
                <p class="akeeba-block--warning">
                    <?php echo AText::sprintf('DATABASE_WARN_LARGE_COLUMNS', $this->large_tables, $this->recommendedPacketSize, $this->maxPacketSize)?>
                </p>
            <?php endif;?>

            <div class="AKEEBA_MASTER_FORM_STYLING akeeba-form--horizontal">
                <div class="akeeba-form-group">
                    <label for="dbtype">
                        <?php echo AText::_('DATABASE_LBL_TYPE') ?>
                    </label>
                    <?php echo AngieHelperSelect::dbtype($this->db->dbtype, $this->db->dbtech) ?>
                    <span class="akeeba-help-text" style="display: none">
                        <?php echo AText::_('DATABASE_LBL_TYPE_HELP') ?>
                    </span>
                </div>
                <div class="akeeba-form-group">
                    <label for="dbhost">
                        <?php echo AText::_('DATABASE_LBL_HOSTNAME') ?>
                    </label>
                    <input type="text" id="dbhost" placeholder="localhost" value="<?php echo $this->db->dbhost ?>" />
                    <span class="akeeba-help-text" style="display: none">
                        <?php echo AText::_('DATABASE_LBL_HOSTNAME_HELP') ?>
                    </span>
                </div>
                <div class="akeeba-form-group">
                    <label for="dbuser">
                        <?php echo AText::_('DATABASE_LBL_USERNAME') ?>
                    </label>
                    <input type="text" id="dbuser" placeholder="<?php echo AText::_('DATABASE_LBL_USERNAME') ?>" value="<?php echo $this->db->dbuser ?>" />
                    <span class="akeeba-help-text" style="display: none">
                        <?php echo AText::_('DATABASE_LBL_USERNAME_HELP') ?>
                    </span>
                </div>
                <div class="akeeba-form-group">
                    <label class="control-label" for="dbpass">
                        <?php echo AText::_('DATABASE_LBL_PASSWORD') ?>
                    </label>
                    <input type="password" id="dbpass" placeholder="<?php echo AText::_('DATABASE_LBL_PASSWORD') ?>" value="<?php echo $this->db->dbpass ?>" />
                    <span class="akeeba-help-text" style="display: none">
                        <?php echo AText::_('DATABASE_LBL_PASSWORD_HELP') ?>
                    </span>
                </div>
                <div class="akeeba-form-group">
                    <label class="control-label" for="dbname">
                        <?php echo AText::_('DATABASE_LBL_DBNAME') ?>
                    </label>
                    <input type="text" id="dbname" placeholder="<?php echo AText::_('DATABASE_LBL_DBNAME') ?>" value="<?php echo $this->db->dbname ?>" />
                    <span class="akeeba-help-text" style="display: none">
                        <?php echo AText::_('DATABASE_LBL_DBNAME_HELP') ?>
                    </span>
                </div>
				<div class="akeeba-form-group">
					<label for="prefix">
			            <?php echo AText::_('DATABASE_LBL_PREFIX') ?>
					</label>
					<input type="text" id="prefix" placeholder="<?php echo AText::_('DATABASE_LBL_PREFIX') ?>" value="<?php echo $this->db->prefix ?>" />
					<span class="akeeba-help-text" style="display: none">
                        <?php echo AText::_('DATABASE_LBL_PREFIX_HELP') ?>
                    </span>
				</div>

				<?php if (defined('ANGIE_DB_ALLOW_PORT_SOCKET') || defined('ANGIE_DB_ALLOW_SSL')): ?>

				<h3><?= AText::_('DATABASE_LBL_UNCOMMON_OPTIONS_HEADER') ?></h3>
				<div class="akeeba-block--info">
					<?= AText::_('DATABASE_LBL_UNCOMMON_OPTIONS_INFO') ?>
				</div>

	            <?php if (defined('ANGIE_DB_ALLOW_PORT_SOCKET')): ?>
					<div class="akeeba-form-group">
						<label for="dbport">
							<?php echo AText::_('DATABASE_LBL_PORT') ?>
						</label>
						<input type="text" id="dbport" placeholder="3306" value="<?php echo $this->db->dbport ?>" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('DATABASE_LBL_PORT_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group">
						<label for="dbsocket">
							<?php echo AText::_('DATABASE_LBL_SOCKET') ?>
						</label>
						<input type="text" id="dbsocket" placeholder="unix:/tmp/mysql.sock or \\.\MySQL" value="<?php echo $this->db->dbsocket ?>" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('DATABASE_LBL_SOCKET_HELP') ?>
						</span>
					</div>
				<?php endif ?>

				<?php if (defined('ANGIE_DB_ALLOW_SSL')): ?>
					<div class="akeeba-form-group--checkbox--pull-right">
						<label for="dbencryption">
							<input type="checkbox" id="dbencryption" <?php echo $this->db->dbencryption ? 'checked="checked"' : '' ?> />
							<?php echo AText::_('DATABASE_LBL_DBENCRYPTION') ?>
						</label>
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('DATABASE_LBL_DBENCRYPTION_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group">
						<label for="dbsslcipher">
							<?php echo AText::_('DATABASE_LBL_DBSSLCIPHER') ?>
						</label>
						<input type="text" id="dbsslcipher" placeholder="AES128-GCM-SHA256:AES256-GCM-SHA384:AES128-CBC-SHA256:AES256-CBC-SHA384:DES-CBC3-SHA"
							   value="<?php echo $this->db->dbsslcipher ?>" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('DATABASE_LBL_DBSSLCIPHER_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group">
						<label for="dbsslcert">
							<?php echo AText::_('DATABASE_LBL_DBSSLCERT') ?>
						</label>
						<input type="text" id="dbsslcert" placeholder="<?= APATH_SITE ?>/client-cert.pem"
							   value="<?php echo $this->db->dbsslcert ?>" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('DATABASE_LBL_DBSSLCERT_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group">
						<label for="dbsslkey">
							<?php echo AText::_('DATABASE_LBL_DBSSLKEY') ?>
						</label>
						<input type="text" id="dbsslkey" placeholder="<?= APATH_SITE ?>/client-key.pem"
							   value="<?php echo $this->db->dbsslkey ?>" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('DATABASE_LBL_DBSSLKEY_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group">
						<label for="dbsslca">
							<?php echo AText::_('DATABASE_LBL_DBSSLCA') ?>
						</label>
						<input type="text" id="dbsslca" placeholder="<?= APATH_SITE ?>/ca.pem"
							   value="<?php echo $this->db->dbsslca ?>" />
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('DATABASE_LBL_DBSSLCA_HELP') ?>
						</span>
					</div>
					<div class="akeeba-form-group--checkbox--pull-right">
						<label for="dbsslverifyservercert">
							<input type="checkbox" id="dbsslverifyservercert" <?php echo $this->db->dbsslverifyservercert ? 'checked="checked"' : '' ?> />
							<?php echo AText::_('DATABASE_LBL_DBSSLVERIFYSERVERCERT') ?>
						</label>
						<span class="akeeba-help-text" style="display: none">
							<?php echo AText::_('DATABASE_LBL_DBSSLVERIFYSERVERCERT_HELP') ?>
						</span>
					</div>
					<?php endif ?>



				<?php endif; ?>
            </div>
        </div>

    </div>

	<div id="advancedWrapper" class="akeeba-panel--info" style="margin-top: 0">
        <header class="akeeba-block-header">
            <h3><?php echo AText::_('DATABASE_HEADER_ADVANCED'); ?></h3>
        </header>

		<div class="AKEEBA_MASTER_FORM_STYLING akeeba-form--horizontal">
			<div class="akeeba-form-group">
				<label for="existing">
					<?php echo AText::_('DATABASE_LBL_EXISTING') ?>
				</label>

                <div class="akeeba-toggle">
					<select name="existing" id="existing">
						<option id="existing-drop" value="drop" <?php echo ($this->db->existing == 'drop') ? 'selected="selected"' : '' ?>>
							<?php echo AText::_('DATABASE_LBL_EXISTING_DROP') ?>
						</option>
						<option id="existing-backup" value="backup" <?php echo ($this->db->existing == 'backup') ? 'selected="selected"' : '' ?>>
							<?php echo AText::_('DATABASE_LBL_EXISTING_BACKUP') ?>
						</option>

						<option id="existing-dropprefix" value="dropprefix" <?php echo ($this->db->existing == 'dropprefix') ? 'selected="selected"' : '' ?>>
							<?php echo AText::_('DATABASE_LBL_EXISTING_DROPPREFIX') ?>
						</option>

						<option id="existing-dropall" value="dropall" <?php echo ($this->db->existing == 'dropall') ? 'selected="selected"' : '' ?>>
							<?php echo AText::_('DATABASE_LBL_EXISTING_DROPALL') ?>
						</option>
					</select>
                </div>
				<span class="akeeba-help-text" style="display: none">
					<?php echo AText::_('DATABASE_LBL_EXISTING_HELP') ?>
				</span>
			</div>

			<?php if ($this->table_list): ?>
            <div class="akeeba-form-group--checkbox--pull-right">
                <label for="specific_tables_cbx">
                    <input type="checkbox" id="specific_tables_cbx" />
					<?php echo AText::_('DATABASE_LBL_SPECIFICTABLES_LBL') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_SPECIFICTABLES_HELP') ?>
                </span>

                <div id="specific_tables_holder" style="display: none">
                    <p>
                        <span id="specific_tables_addall" class="akeeba-btn--mini"><?php echo AText::_('DATABASE_LBL_SPECIFICTABLES_ADD_ALL')?></span>
                        <span id="specific_tables_clearall" class="akeeba-btn--mini-grey"><?php echo AText::_('DATABASE_LBL_SPECIFICTABLES_CLEAR_ALL')?></span>
                    </p>
	            <?php echo $this->table_list ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label for="foreignkey">
                    <input type="checkbox" id="foreignkey" <?php echo $this->db->foreignkey ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_FOREIGNKEY') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_FOREIGNKEY_HELP') ?>
                </span>
			</div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="noautovalue">
                    <input type="checkbox" id="noautovalue" <?php echo $this->db->noautovalue ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_NOAUTOVALUE') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
	                <?php echo AText::_('DATABASE_LBL_NOAUTOVALUE_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="replace">
                    <input type="checkbox" id="replace" <?php echo $this->db->replace ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_REPLACE') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
	                <?php echo AText::_('DATABASE_LBL_REPLACE_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="utf8db">
                    <input type="checkbox" id="utf8db" <?php echo $this->db->utf8db ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_FORCEUTF8DB') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
    	            <?php echo AText::_('DATABASE_LBL_FORCEUTF8DB_HELP') ?>
                </span>
			</div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="utf8tables">
                    <input type="checkbox" id="utf8tables" <?php echo $this->db->utf8db ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_FORCEUTF8TABLES') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
	                <?php echo AText::_('DATABASE_LBL_FORCEUTF8TABLES_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="utf8mb4">
                    <input type="checkbox" id="utf8mb4" <?php echo $this->db->utf8mb4 ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_UTF8MB4DETECT') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_UTF8MB4DETECT_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="break_on_failed_create">
                    <input type="checkbox" id="break_on_failed_create"
			            <?php echo $this->db->break_on_failed_create ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_ON_CREATE_ERROR') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
                    <?php echo AText::_('DATABASE_LBL_ON_CREATE_ERROR_HELP') ?>
                </span>
            </div>

            <div class="akeeba-form-group--checkbox--pull-right">
                <label class="checkbox help-tooltip" for="break_on_failed_insert">
                    <input type="checkbox" id="break_on_failed_insert"
			            <?php echo $this->db->break_on_failed_insert ? 'checked="checked"' : '' ?> />
		            <?php echo AText::_('DATABASE_LBL_ON_OTHER_ERROR') ?>
                </label>
                <span class="akeeba-help-text" style="display: none">
	                <?php echo AText::_('DATABASE_LBL_ON_OTHER_ERROR_HELP') ?>
                </span>
            </div>

            <h4><?php echo AText::_('DATABASE_HEADER_FINETUNING') ?></h4>

            <div class="akeeba-block--info">
                <?php echo AText::_('DATABASE_MSG_FINETUNING'); ?>
            </div>

            <div class="akeeba-form-group">
                <label for="maxexectime">
                    <?php echo AText::_('DATABASE_LBL_MAXEXECTIME') ?>
                </label>
                <input class="input-mini" type="text" id="maxexectime" placeholder="<?php echo AText::_('DATABASE_LBL_MAXEXECTIME') ?>" value="<?php echo $this->db->maxexectime ?>" />
	            <span class="akeeba-help-text" style="display: none;">
                    <?php echo AText::_('DATABASE_LBL_MAXEXECTIME_HELP') ?>
                </span>
            </div>
            <div class="akeeba-form-group">
                <label for="throttle">
                    <?php echo AText::_('DATABASE_LBL_THROTTLEMSEC') ?>
                </label>
                <input class="input-mini" type="text" id="throttle" placeholder="<?php echo AText::_('DATABASE_LBL_THROTTLEMSEC') ?>" value="<?php echo $this->db->throttle ?>" />
                <span class="akeeba-help-text" style="display: none;">
                    <?php echo AText::_('DATABASE_LBL_THROTTLEMSEC_HELP') ?>
                </span>
            </div>
		</div>
	</div>
</div>
