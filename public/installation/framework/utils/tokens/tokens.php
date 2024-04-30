<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die;

class AUtilsTokensTokens
{
	private $tokens;

	public function __construct($code)
	{
		// construct PHP code from string and tokenize it
		$tokens = token_get_all("<?php " . $code);
		// kick out whitespace tokens
		$this->tokens = array_filter($tokens, function ($token) {
			return (!is_array($token) || $token[0] !== T_WHITESPACE);
		});
		// remove start token (<?php)
		$this->pop();
	}

	public function doesMatch($what)
	{
		$token = $this->peek();

		if (is_string($what) && !is_array($token) && $token === $what)
		{
			return true;
		}
		if (is_int($what) && is_array($token) && $token[0] === $what)
		{
			return true;
		}

		return false;
	}

	public function done()
	{
		return count($this->tokens) === 0;
	}

	public function forceMatch($what)
	{
		if (!$this->doesMatch($what))
		{
			if (is_int($what))
			{
				throw new Exception("unexpected token - expecting " . token_name($what));
			}
			throw new Exception("unexpected token - expecting " . $what);
		}
		// consume the token
		$this->pop();
	}

	public function peek()
	{
		// return next token, don't consume it
		if ($this->done())
		{
			throw new Exception("already at end of tokens!");
		}

		return $this->tokens[0];
	}

	public function pop()
	{
		// consume the token and return it
		if ($this->done())
		{
			throw new Exception("already at end of tokens!");
		}

		return array_shift($this->tokens);
	}
}