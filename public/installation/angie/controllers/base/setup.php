<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieControllerBaseSetup extends AController
{
    public function apply()
    {
        /** @var AngieModelBaseSetup $model */
        $model = $this->getThisModel();
        $msg   = null;
        $type  = null;

        try
        {
            $writtenConfiguration = $model->applySettings();
            $url = 'index.php?view=finalise';

            if (!$writtenConfiguration)
            {
                $url .= '&showconfig=1';
            }
        }
        catch (Exception $exc)
        {
            $type = 'error';
            $msg  = $exc->getMessage();
            $url  = 'index.php?view=setup';
        }

        $this->setRedirect($url, $msg, $type);

        // Encode the result if we're in JSON format
        if($this->input->getCmd('format', '') == 'json')
        {
            $result['error'] = $msg;

	        @ob_clean();
            echo json_encode($result);
        }
    }
}
