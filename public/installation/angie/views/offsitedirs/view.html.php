<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

class AngieViewOffsitedirs extends AView
{
	public function onBeforeMain()
	{
        /** @var AngieModelSteps $stepsModel */
		$stepsModel   = AModel::getAnInstance('Steps', 'AngieModel', array(), $this->container);
        /** @var AngieModelOffsitedirs $offsiteModel */
        $offsiteModel = AModel::getAnInstance('Offsitedirs', 'AngieModel', array(), $this->container);

        $substeps   = $offsiteModel->getDirs(true, true);
		$cursubstep = $stepsModel->getActiveSubstep();

		$this->substep = $substeps[$cursubstep];
		$this->number_of_substeps = $stepsModel->getNumberOfSubsteps();

		return true;
	}
}
