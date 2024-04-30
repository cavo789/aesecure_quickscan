<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Parses an PHP string and extract the requests tokens from it
 * Based on Awf\Utils\PhpTokenizer
 */
class AUtilsPhptokenizer
{
    /** @var  string    PHP code that will be analyzed */
    private $code;

    /**
     * Class constructor
     *
     * @param   string  $code   PHP code that will be analyzed
     */
    public function __construct($code = null)
    {
        $this->code = $code;
    }

    /**
     * Sets the code that will be analyzed
     *
     * @param   string  $code   PHP code that will be analyzed
     *
     * @return  $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Searches for a specific PHP token and extracts its code
     *
     * @param   string  $type   PHP Token constant or a custom one (@see tokenChar())
     * @param   string  $name   Name of the token (ie variable name)
     * @param   int     $skip   How many lines should I skip from the beginning?
     *
     * @return array    Indexed array containing the starting /ending line and the data contained inside
     */
    public function searchToken($type, $name, $skip = 0)
    {
        if(!$this->code)
        {
            throw new RuntimeException('Please set some code before trying to analyze it');
        }

        // Let's find the starting line (where the token first appears)
        $startLine  = $this->findToken($type, $name, $skip);

        // Token not found? Let's raise an exception
        if(!$startLine)
        {
            throw new RuntimeException('Token '.$type.' with value '.$name.' not found');
        }

        // Ok, got it, now let's search for the semicolon
        $endLine = $this->findToken('AWF_SEMICOLON', ';', $startLine);
        $data    = $this->extractData($startLine, $endLine);

        return array(
            'startLine' => $startLine,
            'endLine'   => $endLine,
            'data'      => $data
        );
    }

    /**
     * Given a PHP token, this function replaces it with a new text
     *
     * @param   string  $type       PHP Token constant or a custom one (@see tokenChar())
     * @param   string  $name       Name of the token (ie variable name)
     * @param   int     $skip       How many lines should I skip from the beginning?
     * @param   string  $replace    Replacing text
     *
     * @return  string  The new text
     */
    public function replaceToken($type, $name, $skip = 0, $replace = '')
    {
        if(!$this->code)
        {
            throw new RuntimeException('Please set some code before trying to analyze it');
        }

        // Let's find the starting line (where the token first appears)
        $startLine  = $this->findToken($type, $name, $skip);

        // Token not found? Let's raise an exception
        if(!$startLine)
        {
            throw new RuntimeException('Token '.$type.' with value '.$name.' not found');
        }

        // Ok, got it, now let's search for the semicolon
        $endLine = $this->findToken('AWF_SEMICOLON', ';', $startLine);

        $data = $this->replaceData($startLine, $endLine, $replace);

        return $data;
    }

    /**
     * Finds the line where the requested PHP Token appears
     *
     * @param   string  $type   PHP Token constant or a custom one (@see tokenChar())
     * @param   string  $name   Name of the token (ie variable name)
     * @param   int     $skip   How many line should I skip from the beginning?
     *
     * @return  int|null    Returns the corrispongin line or null if not found
     */
    protected function findToken($type, $name, $skip = 0)
    {
		$code   = $this->processCode($skip);

		// Simply sanity check. If the "string" is not present (even in commented code), there's
		// no need to loop on every token: we can simply assume that the variable is not there
		if(strpos($code, $name) === false)
		{
			return null;
		}

        $tokens = token_get_all($this->code);

        $iterator   = new ArrayIterator($tokens);
        $collection = new CachingIterator($iterator, CachingIterator::TOSTRING_USE_CURRENT);
		$offset     = $skip ? $skip - 1 : 0;

        // Ok let's start looking for the requested token
        foreach($collection as $token)
        {
            if(is_string($token))
            {
                $info['token'] = $this->tokenChar($token);
                $info['value'] = $token;
            }
            else
            {
                $info['token'] = token_name($token[0]);
                $info['value'] = $token[1];
            }

            // Ok token found, let's get the line (we have to add the skip count since we processed the whole code string)
            if($info['token'] == $type && $info['value'] == $name)
            {
                // If it's an array, that's easy
                if(is_array($token))
                {
                    return $token[2] + $offset - 1;
                }
                else
                {
                    // It's a string, I have to fetch the next token so I'll have the proper line number
                    // To be sure, I have to iterate until I finally get an array for the token
                    $next = null;

                    while(!is_array($next))
                    {
                        $next = $collection->getInnerIterator()->current();

                        // The next token is not an array (ie it's a char like ;.=?)? Move the iterator forward and fetch
                        // the next token
                        if(!is_array($next))
                        {
                            $collection->getInnerIterator()->next();
                            continue;
                        }

                        return $next[2] + $offset;
                    }
                }
            }
        }

        return null;
    }

	/**
	 * Processes the current code snippet, removing the lines we have to skip
	 *
	 * @param   int     $skip
	 *
	 * @return  string  The part of the code we're interested in
	 */
	protected function processCode($skip)
	{
		if(!$skip)
		{
			return $this->code;
		}

		$lines  = explode("\n", $this->code);

		// If the line is not defined, let's return the whole code
		if(!isset($lines[$skip]))
		{
			return $this->code;
		}

		// I have to add the opening tag, otherwise token_get_all() won't find anything
		$result = '<?php'."\n";

		for($i = ($skip - 1); $i < count($lines); $i++)
		{
			if(!isset($lines[$i]))
			{
				break;
			}

			$result .= $lines[$i]."\n";
		}

		return $result;
	}

    /**
     * Given a starting and an ending line, extract the text within them
     *
     * @param   int  $start
     * @param   int  $end
     *
     * @return  string
     */
    protected function extractData($start, $end)
    {
        $result = '';
        $lines  = explode("\n", $this->code);

        if(!isset($lines[$start]))
        {
            return $result;
        }

        for($i = ($start - 1); $i < $end; $i++)
        {
            if(!isset($lines[$i]))
            {
                break;
            }

            $result .= $lines[$i]."\n";
        }

        return $result;
    }

    /**
     * Replaces the text between the starting and the ending line
     *
     * @param   int     $start      Starting line
     * @param   int     $end        Ending line
     * @param   string  $replace    Text that will be replaced
     *
     * @return  string  The new text
     */
    protected function replaceData($start, $end, $replace = '')
    {
        $result = array();
        $lines  = explode("\n", $this->code);

        $i = 0;

        foreach($lines as $line)
        {
            $i += 1;

            // Line is before or after the section we are interested into, let's add to the final result
            if($i < $start || $i > $end)
            {
                $result[] = $line;
            }

            // We hit the starting zone, let's inject our replace data
            if($i == $start)
            {
                $result[] = $replace;
            }
        }

        $result = implode("\n", $result);

        return $result;
    }

    /**
     * PHP doesn't have a token for single chars as (),;= and so on.
     * This function sets some custom tokens for consistency
     *
     * @param   string  $char
     *
     * @return  string  Our custom token
     */
    private function tokenChar($char)
    {
        switch($char)
        {
            case ';':
                return 'AWF_SEMICOLON';
        }

        return 'AWF_UNKNOWN';
    }
}
