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
 * This file may contain code from the Joomla! Platform, Copyright (c) 2005 -
 * 2012 Open Source Matters, Inc. This file is NOT part of the Joomla! Platform.
 * It is derivative work and clearly marked as such as per the provisions of the
 * GNU General Public License.
 */

#[\AllowDynamicProperties]
abstract class ADocument
{
	protected $buffer = '';

	protected $scripts = array();

	protected $scriptDeclarations = array();

	protected $styles = array();

	protected $styleDeclarations = array();

    /** @var \AContainer Application container */
    protected $container;

	private static $instances = array();

	private $buttons = array();

	/**
	 * Return the static instance of the document
	 *
	 * @param   string  $type  The document type (html or json)
     * @param   AContainer $container Application container
	 *
	 * @return  ADocument
	 */
	public static function getInstance($type = 'html', AContainer $container = null)
	{
		if(!array_key_exists($type, self::$instances))
		{
			$className = 'ADocument' . ucfirst($type);

			if (!class_exists($className))
			{
				$className = 'ADocumentHtml';
			}

			self::$instances[$type] = new $className($container);
		}

		return self::$instances[$type];
	}

    public function __construct(AContainer $container = null)
    {
        if(is_null($container))
        {
            $container = AApplication::getInstance()->getContainer();
        }

        $this->container = $container;
    }

    /**
	 * Sets the buffer (contains the main content of the HTML page or the entire JSON response)
	 *
	 * @param   string  $buffer
	 *
	 * @return  ADocument
	 */
	public function setBuffer($buffer)
	{
		$this->buffer = $buffer;

		return $this;
	}

	/**
	 * Returns the contents of the buffer
	 *
	 * @return  string
	 */
	public function getBuffer()
	{
		return $this->buffer;
	}

	/**
	 * Adds an external script to the page
	 *
	 * @param   string   $url     The URL of the script file
	 * @param   boolean  $before  (optional) Should I add this before the template's scripts?
	 * @param   string   $type    (optional) The MIME type of the script file
	 *
	 * @return  ADocument
	 */
	public function addScript($url, $before = false, $type = "text/javascript")
	{
		$this->scripts[$url]['mime'] = $type;
		$this->scripts[$url]['before'] = $before;

		return $this;
	}

	/**
	 * Adds an inline script to the page's header
	 *
	 * @param   string  $content  The contents of the script (without the script tag)
	 * @param   string  $type    (optional) The MIME type of the script data
	 *
	 * @return  ADocument
	 */
	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		if (!isset($this->scriptDeclarations[strtolower($type)]))
		{
			$this->scriptDeclarations[strtolower($type)] = $content;
		}
		else
		{
			$this->scriptDeclarations[strtolower($type)] .= chr(13) . $content;
		}

		return $this;
	}

	/**
	 * Adds an external stylesheet to the page
	 *
	 * @param   string   $url     The URL of the stylesheet file
	 * @param   boolean  $before  (optional) Should I add this before the template's scripts?
	 * @param   string   $type    (optional) The MIME type of the stylesheet file
	 * @param   string   $media   (optional) The media target of the stylesheet file
	 *
	 * @return  ADocument
	 */
	public function addStyleSheet($url, $before = false, $type = 'text/css', $media = null)
	{
		$this->styles[$url]['mime'] = $type;
		$this->styles[$url]['media'] = $media;
		$this->styles[$url]['before'] = $before;

		return $this;
	}

	/**
	 * Adds an inline stylesheet to the page's header
	 *
	 * @param   string  $content  The contents of the stylesheet (without the style tag)
	 * @param   string  $type    (optional) The MIME type of the stylesheet data
	 *
	 * @return  ADocument
	 */
	public function addStyleDeclaration($content, $type = 'text/css')
	{
		if (!isset($this->styleDeclarations[strtolower($type)]))
		{
			$this->styleDeclarations[strtolower($type)] = $content;
		}
		else
		{
			$this->styleDeclarations[strtolower($type)] .= chr(13) . $content;
		}

		return $this;
	}

	/**
	 * Return the array with external scripts
	 *
	 * @return  array
	 */
	public function getScripts()
	{
		return $this->scripts;
	}

	/**
	 * Return the array with script declarations
	 *
	 * @return  array
	 */
	public function getScriptDeclarations()
	{
		return $this->scriptDeclarations;
	}

	/**
	 * Return the array with external stylesheets
	 *
	 * @return  array
	 */
	public function getStyles()
	{
		return $this->styles;
	}

	/**
	 * Return the array with style declarations
	 *
	 * @return  array
	 */
	public function getStyleDeclarations()
	{
		return $this->styleDeclarations;
	}

	/**
	 * Each document class implements its own renderer which outputs the buffer
	 * to the browser using the appropriate template.
	 */
	abstract public function render();

	/**
	 * Adds a button to the end of the buttons list
	 *
	 * @param   string  $message  The translation string of the button message
	 * @param   string  $url      The URL of the button's action. Prefix with "javascript:" for an onClick Javascript action
	 * @param   string  $type     The addon btn- Bootstrap classes, space separated, e.g. 'primary large'
	 * @param   string  $icon     The Bootstrap icon- classes, space separated, e.g. 'white arrow-left'
     * @param   string  $id
	 */
	public function appendButton($message, $url, $type = 'primary', $icon = '', $id = '')
	{
		if (substr($url, 0, 11) == 'javascript:')
		{
			$onclick = substr($url, 11);
			$url = '';
		}
		else
		{
			$onclick = '';
		}

		$types = explode(' ', $type);
		$icons = explode(' ', $icon);

		$this->buttons[] = array(
			'message'		=> AText::_($message),
			'url'			=> $url,
			'onclick'		=> $onclick,
			'types'			=> $types,
			'icons'			=> $icons,
			'id'			=> $id,
		);
	}

	/**
	 * Adds a button in the beginning of the buttons list. Yes, I know there is
	 * no such English word as "prepend".
	 *
	 * @param   string  $message  The translation string of the button message
	 * @param   string  $url      The URL of the button's action. Prefix with "javascript:" for an onClick Javascript action
	 * @param   string  $type     The addon btn- Bootstrap classes, space separated, e.g. 'primary large'
	 * @param   string  $icon     The Bootstrap icon- classes, space separated, e.g. 'white arrow-left'
	 */
	public function prependButton($message, $url, $type = 'primary', $icon = '')
	{
		if (substr($url, 0, 11) == 'javascript:')
		{
			$onclick = substr($url, 11);
			$url = '';
		}
		else
		{
			$onclick = '';
		}

		$types = explode(' ', $type);
		$icons = explode(' ', $icon);

		$button = array(
			'message'		=> AText::_($message),
			'url'			=> $url,
			'onclick'		=> $onclick,
			'types'			=> $types,
			'icons'			=> $icons,
		);

		array_unshift($this->buttons, $button);
	}

	/**
	 * Clear button definitions
	 */
	public function clearButtons()
	{
		$this->buttons = array();
	}

	/**
	 * Return all button definitions
	 *
	 * @return  array  array
	 */
	public function getButtons()
	{
		return $this->buttons;
	}
}
