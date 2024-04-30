<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die;

class AUtilsTokensParser
{
	private static $CONSTANTS = [
		"null"  => null,
		"true"  => true,
		"false" => false,
	];

	private $tokens;

	public function __construct(AUtilsTokensTokens $tokens)
	{
		$this->tokens = $tokens;
	}

	public function parseArray()
	{
		$found  = 0;
		$result = [];

		$this->tokens->forceMatch(T_ARRAY);
		$this->tokens->forceMatch("(");

		while (true)
		{
			if ($this->tokens->doesMatch(")"))
			{
				// reached the end of the array
				$this->tokens->forceMatch(")");
				break;
			}

			if ($found > 0)
			{
				// we must see a comma following the first element
				$this->tokens->forceMatch(",");
			}

			if ($this->tokens->doesMatch(T_ARRAY))
			{
				// nested array
				$result[] = $this->parseArray();
			}
			else if ($this->tokens->doesMatch(T_CONSTANT_ENCAPSED_STRING))
			{
				// string
				$string = $this->parseValue();
				if ($this->tokens->doesMatch(T_DOUBLE_ARROW))
				{
					// array key (key => value)
					$this->tokens->pop();
					$result[$string] = $this->parseValue();
				}
				else
				{
					// simple string
					$result[] = $string;
				}
			}
			else
			{
				$result[] = $this->parseValue();
			}

			++$found;
		}

		return $result;
	}

	public function parseValue()
	{
		if ($this->tokens->doesMatch(T_CONSTANT_ENCAPSED_STRING))
		{
			// strings
			$token = $this->tokens->pop();

			return stripslashes(substr($token[1], 1, -1));
		}

		if ($this->tokens->doesMatch(T_STRING))
		{
			// built-in string literals: null, false, true
			$token = $this->tokens->pop();
			$value = strtolower($token[1]);
			if (array_key_exists($value, self::$CONSTANTS))
			{
				return self::$CONSTANTS[$value];
			}
			throw new Exception("unexpected string literal " . $token[1]);
		}

		// the rest...
		// we expect a number here
		$uminus = 1;

		if ($this->tokens->doesMatch("-"))
		{
			// unary minus
			$this->tokens->forceMatch("-");
			$uminus = -1;
		}

		if ($this->tokens->doesMatch(T_LNUMBER))
		{
			// long number
			$value = $this->tokens->pop();

			return $uminus * (int) $value[1];
		}
		if ($this->tokens->doesMatch(T_DNUMBER))
		{
			// double number
			$value = $this->tokens->pop();

			return $uminus * (double) $value[1];
		}

		throw new Exception("unexpected value token");
	}
}