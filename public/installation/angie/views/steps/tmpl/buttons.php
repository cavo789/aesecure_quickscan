<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/** @var $this AView */

$document = $this->container->application->getDocument();

$data = $this->input->getData();

/** @var AngieModelSteps $stepsModel */
$stepsModel = AModel::getAnInstance('Steps', 'AngieModel', array(), $this->container);
$this->input->setData($data);

// Previous step
$prevStep = $stepsModel->getPreviousStep();

if(!empty($prevStep['step']))
{
	$url = 'index.php?view=' . $prevStep['step']
		. (!empty($prevStep['substep']) ? '&substep=' . $prevStep['substep'] : '');

	$document->appendButton(
		'GENERIC_BTN_PREV', $url, 'grey', 'arrow-left-c', 'btnPrev'
	);
}

// Skip (on database step)
if($stepsModel->getActiveStep() == 'database')
{
	// Next step
	$nextStep = $stepsModel->getNextStep();

	if(!empty($nextStep['step']))
	{
		$url = 'index.php?view=' . $nextStep['step']
			. (!empty($nextStep['substep']) ? '&substep=' . $nextStep['substep'] : '');
		$document->appendButton(
			'GENERIC_BTN_SKIP', $url, 'orange', 'arrow-right-c', 'btnSkip'
		);
	}

	$key = $stepsModel->getActiveSubstep();

	$document->appendButton(
		'GENERIC_BTN_NEXT', 'javascript:databaseRunRestoration(\''.$key.'\'); return false;', 'teal', 'arrow-right-c', 'btnNext'
	);
}
elseif($stepsModel->getActiveStep() == 'offsitedirs')
{
    // Next step
    $nextStep = $stepsModel->getNextStep();

    if(!empty($nextStep['step']))
    {
        $url = 'index.php?view=' . $nextStep['step']
            . (!empty($nextStep['substep']) ? '&substep=' . $nextStep['substep'] : '');
        $document->appendButton(
            'GENERIC_BTN_SKIP', $url, 'orange', 'arrow-right-c', 'btnSkip'
        );
    }

    $key = $stepsModel->getActiveSubstep();

    $document->appendButton(
        'GENERIC_BTN_NEXT', 'javascript:offsitedirsRunRestoration(\''.$key.'\'); return false;', 'teal', 'arrow-right-c', 'btnNext'
    );
}
elseif($stepsModel->getActiveStep() == 'setup')
{
    $nextStep = $stepsModel->getNextStep();
    $key      = $stepsModel->getActiveSubstep();

    // Sometimes I have to display the setup page multiple times (ie Drupal multisite)
    if($key)
    {
        if(!empty($nextStep['step']))
        {
            $url = 'index.php?view=' . $nextStep['step']
                . (!empty($nextStep['substep']) ? '&substep=' . $nextStep['substep'] : '');
            $document->appendButton(
                'GENERIC_BTN_SKIP', $url, 'orange', 'arrow-right-c', 'btnSkip'
            );
        }

        $document->appendButton(
            'GENERIC_BTN_NEXT', 'javascript:setupRunRestoration(\''.$key.'\'); return false;', 'teal multisite', 'arrow-right-c', 'btnNext'
        );
    }
    else
    {
        if(!empty($nextStep['step']))
        {
            $url = 'index.php?view=' . $nextStep['step']
                . (!empty($nextStep['substep']) ? '&substep=' . $nextStep['substep'] : '');
            $document->appendButton(
                'GENERIC_BTN_NEXT', $url, 'teal', 'arrow-right-c', 'btnNext'
            );
        }
    }
}
else
{
	// Next step
	$nextStep = $stepsModel->getNextStep();

	if(!empty($nextStep['step']))
	{
		$url = 'index.php?view=' . $nextStep['step']
			. (!empty($nextStep['substep']) ? '&substep=' . $nextStep['substep'] : '');

		$document->appendButton(
			'GENERIC_BTN_NEXT', $url, 'teal', 'arrow-right-c', 'btnNext'
		);
	}
}
