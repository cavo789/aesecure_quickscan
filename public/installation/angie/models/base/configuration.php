<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die();

abstract class AngieModelBaseConfiguration extends AModel
{
    /**
     * The CMS configuration variables
     *
     * @var array
     */
    protected $configvars = array();

    /**
     * Destructor. Automatically saves the configuration variables to the session
     */
    public function __destruct()
    {
        if (!empty($this->configvars))
        {
            $this->container->session->set('configuration.variables', $this->configvars);
        }
    }

    /**
     * Public getter for the configvars variable
     * 
     * @return array
     */
    public function getConfigvars()
    {
        return $this->configvars;
    }

    /**
     * Saves the modified configuration variables to the session
     */
    public function saveToSession()
    {
        $this->container->session->set('configuration.variables', $this->configvars);
    }

    /**
     * Resets the configuration variables
     */
    public function reset()
    {
        $this->configvars = array();
        $this->container->session->remove('configuration.variables');
    }

    /**
     * Gets a configuration value
     *
     * @param   string $key     The key (variable name)
     * @param   mixed  $default The default value to return if the key doesn't exist
     *
     * @return  mixed  The variable's value
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->configvars))
        {
            return $this->configvars[$key];
        }
        else
        {
            // The key was not found. Set it with the default value, store and
            // return the default value
            $this->configvars[$key] = $default;
            $this->saveToSession();

            return $default;
        }
    }

    /**
     * Sets a variable's value and stores the configuration array in the global
     * Storage.
     *
     * @param   string $key   The variable name
     * @param   mixed  $value The value to set it to
     */
    public function set($key, $value)
    {
        $this->configvars[$key] = $value;
        $this->saveToSession();
    }

	public function remove($key)
	{
		if (array_key_exists($key, $this->configvars))
		{
			unset($this->configvars[$key]);
		}
    }

    /**
     * Makes a Windows path more UNIX-like, by turning backslashes to forward slashes.
     * Since JP 2.0.b1 it takes into account UNC paths, e.g.
     * \\myserver\some\folder becomes \\myserver/some/folder
     *
     * @param string $p_path The path to transform
     *
     * @return string
     */
    protected function TranslateWinPath($p_path)
    {
        static $is_windows;

        if (empty($is_windows))
        {
            $is_windows = (DIRECTORY_SEPARATOR == '\\');
        }

        $is_unc = false;

        if ($is_windows)
        {
            // Is this a UNC path?
            $is_unc = (substr($p_path, 0, 2) == '//');
            // Change potential windows directory separator
            if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\'))
            {
                $p_path = strtr($p_path, '\\', '/');
            }
        }

        // FIX 2.1.b2: Remove multiple slashes
        $p_path = str_replace('///', '/', $p_path);
        $p_path = str_replace('//', '/', $p_path);

        // Fix UNC paths
        if ($is_unc)
        {
            $p_path = '/' . $p_path;
        }

        return $p_path;
    }
}
