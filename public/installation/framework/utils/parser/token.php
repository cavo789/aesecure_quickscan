<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_AKEEBA') or die;

/**
 * PHP Tokenizer–based file parser.
 *
 * This is the safest option.
 *
 * @since 9.1.0
 */
class AUtilsParserToken extends AUtilsParserAbstract
{
	/**
	 * The priority for this parser. Lower number runs first.
	 *
	 * @var   int
	 * @since 9.1.0
	 */
	protected $priority = 500;

	public function isSupported()
	{
		return true;
	}

	/** @inheritDoc */
	public function parseFile($file, $className)
	{
		$ret          = [];
		$fileContents = $this->cleanComments(file_get_contents($file));
		$fileContents = str_replace("\r\n", "\n", $fileContents);
		$fileContents = str_replace("\r", "\n", $fileContents);
		$fileLines    = explode("\n", $fileContents);

		$inLine = false;

		foreach ($fileLines as $line)
		{
			$line = trim($line);

			if (!$inLine)
			{
				if ((strpos($line, 'public') !== 0) && (strpos($line, 'var') !== 0))
				{
					continue;
				}

				$inLine = true;

				if (strpos($line, 'public') === 0)
				{
					$code = substr($line, 6);
				}
				else
				{
					$code = substr($line, 3);
				}
			}

			if (substr($line, -1) != ';')
			{
				$code .= substr($line, 0, -1);

				continue;
			}

			$code = trim($code);

			if (version_compare(PHP_VERSION, '7.1.0', 'lt'))
			{
				list($key, $value) = explode('=', $code, 2);
			}
			else
			{
				/** @noinspection PhpLanguageLevelInspection */
				[$key, $value] = explode('=', $code, 2);
			}

			$key    = ltrim(trim($key), '$');
			$value  = trim(ltrim($value, ';'));
			$parser = new AUtilsTokensParser(new AUtilsTokensTokens($value));

			try
			{
				if (strpos($value, 'array') === 0 || strpos($value, '[') === 0)
				{
					$value = $parser->parseArray();
				}
				else
				{
					$value = $parser->parseValue();
				}
			}
			catch (Exception $e)
			{
				$inLine = false;

				continue;
			}

			$ret[$key] = $value;

			$inLine = false;
		}

		return $ret;
	}

	/**
	 * Remove all comments from the PHP code
	 *
	 * @param   string  $phpCode
	 *
	 * @return  string
	 * @since   9.1.0
	 */
	private function cleanComments($phpCode)
	{
		$tokens        = token_get_all($phpCode);
		$commentTokens = [T_COMMENT];

		if (defined('T_DOC_COMMENT'))
		{
			$commentTokens[] = T_DOC_COMMENT;
		}

		if (defined('T_ML_COMMENT'))
		{
			$commentTokens[] = T_ML_COMMENT;
		}

		$newStr = '';

		foreach ($tokens as $token)
		{
			if (is_array($token))
			{
				if (in_array($token[0], $commentTokens))
				{
					/**
					 * If the comment ended in a newline we need to output the newline. Otherwise we will have
					 * run-together lines which won't be parsed correctly by parseWithoutTokenizer.
					 */
					if (substr($token[1], -1) == "\n")
					{
						$newStr .= "\n";
					}

					continue;
				}

				$token = $token[1];
			}

			$newStr .= $token;
		}

		return $newStr;
	}
}