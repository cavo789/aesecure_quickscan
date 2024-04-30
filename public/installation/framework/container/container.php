<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

/**
 * Dependency injection container. Based on AWF
 *
 * @property  string                         $application_name      The name of the application
 *
 * @property-read  AApplication              $application           The application instance
 * @property-read  ADispatcher               $dispatcher            The application dispatcher
 * @property-read  AInput                    $input                 The global application input object
 * @property-read  ASession                  $session               The session manager
 */
class AContainer extends APimple
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        // Application service
        if (!isset($this['application']))
        {
            $this['application'] = function (AContainer $c)
            {
                return AApplication::getTmpInstance($c->application_name, array(), 'Angie', $c);
            };
        }

        // Input Access service
        if (!isset($this['input']))
        {
            $this['input'] = function (AContainer $c)
            {
                return new AInput();
            };
        }

        // Application Dispatcher service
        if (!isset($this['dispatcher']))
        {
            $this['dispatcher'] = function (AContainer $c)
            {
                return ADispatcher::getTmpInstance(null, null, array(), $c);
            };
        }

        if(!isset($this['session']))
        {
            $this['session'] = function(AContainer $c){
                return ASession::getInstance();
            };
        }
    }
}
