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
<div class="akeeba-block--warning">
	<?php echo AText::_('SESSION_LBL_MAINMESSAGE'); ?>
</div>

<p>
	<?php echo AText::_('SESSION_LBL_INTRO'); ?>
	<?php echo ($this->hasFTP) ? AText::_('SESSION_LBL_INTROFTP') : '' ?>
</p>

<?php if ($this->hasFTP): ?>
<div class="akeeba-panel--info">
    <header class="akeeba-block-header">
        <h3>
		    <?php echo AText::_('SESSION_LBL_FTPINFORMATION'); ?>
        </h3>
    </header>

    <form class="akeeba-form--horizontal" name="sessionForm" action="index.php" method="POST">
        <input type="hidden" name="view" value="session" />
        <input type="hidden" name="task" value="fix" />

        <div class="akeeba-form-group">
            <label class="control-label" for="hostname">
				<?php echo AText::_('SESSION_FIELD_HOSTNAME_LABEL'); ?>
            </label>
            <input type="text" class="input-large" name="hostname" id="hostname" value="<?php echo $this->state->hostname ?>" />
            <span class="help-block">
				<?php echo AText::_('SESSION_FIELD_HOSTNAME_DESC'); ?>
            </span>
        </div>

        <div class="akeeba-form-group">
            <label class="control-label" for="port">
				<?php echo AText::_('SESSION_FIELD_PORT_LABEL'); ?>
            </label>
            <input type="text" class="input-large" name="port" id="port" value="<?php echo $this->state->port ?>" />
            <span class="help-block">
				<?php echo AText::_('SESSION_FIELD_PORT_DESC'); ?>
			</span>
        </div>

        <div class="akeeba-form-group">
            <label class="control-label" for="username">
				<?php echo AText::_('SESSION_FIELD_USERNAME_LABEL'); ?>
            </label>
            <input type="text" class="input-large" name="username" id="username" value="<?php echo $this->state->username ?>" />
        </div>

        <div class="akeeba-form-group">
            <label class="control-label" for="password">
				<?php echo AText::_('SESSION_FIELD_PASSWORD_LABEL'); ?>
            </label>
            <input type="password" class="input-large" name="password" id="password" value="<?php echo $this->state->password ?>" />
        </div>

        <div class="akeeba-form-group">
            <label class="control-label" for="directory">
				<?php echo AText::_('SESSION_FIELD_DIRECTORY_LABEL'); ?>
            </label>
            <div class="akeeba-input-group">
                <input type="text" class="input-large" name="directory" id="directory" value="<?php echo $this->state->directory ?>" />
                <span class="akeeba-input-group-btn">
                    <button type="button" class="akeeba-btn" id="ftpbrowser" onclick="openFTPBrowser();">
                        <span class="akion-folder"></span>
                        <?php echo AText::_('SESSION_BTN_BROWSE'); ?>
                    </button>
                </span>
            </div>
            <span class="akeeba-help-text">
				<?php echo AText::_('SESSION_FIELD_DIRECTORY_DESC'); ?>
			</span>
        </div>

        <div class="akeeba-form-group--pull-right">
            <div class="akeeba-form-group--actions">
                <button type="submit" class="akeeba-btn--teal--big">
                    <span class="akion-checkmark-round"></span>
		            <?php echo AText::_('SESSION_BTN_APPLY'); ?>
                </button>
            </div>
        </div>
    </form>
</div>

<div id="browseModal" class="modal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="browseModalLabel" style="display: none">
    <div class="akeeba-renderer-fef">
        <div class="akeeba-panel--teal">
            <header class="akeeba-block-header">
                <h3 id="browseModalLabel"><?php echo AText::_('GENERIC_FTP_BROWSER');?></h3>
            </header>
            <iframe id="browseFrame" src="about:blank" width="100%" height="300px"></iframe>
        </div>
    </div>
</div>

<script type="text/javascript">
    function openFTPBrowser()
    {
        var hostname  = document.getElementById('hostname').value;
        var port      = document.getElementById('port').value;
        var username  = document.getElementById('username').value;
        var password  = document.getElementById('password').value;
        var directory = document.getElementById('directory').value;

        if ((port <= 0) || (port >= 65536))
        {
            port = 21;
        }

        var url = 'index.php?view=ftpbrowser&tmpl=component'
            + '&hostname=' + encodeURIComponent(hostname)
            + '&port=' + encodeURIComponent(port)
            + '&username=' + encodeURIComponent(username)
            + '&password=' + encodeURIComponent(password)
            + '&directory=' + encodeURIComponent(directory);

        document.getElementById('browseFrame').src = url;

        akeeba.System.data.set(document.getElementById('browseModal'), 'modal', akeeba.Modal.open({
            inherit: '#browseModal',
            width:   '80%'
        }));

        return false;
    }

    function useFTPDirectory(path)
    {
		document.getElementById('directory').value = path;

        akeeba.System.data.get(document.getElementById('browseModal'), 'modal').close();
    }
</script>
<?php endif; ?>
