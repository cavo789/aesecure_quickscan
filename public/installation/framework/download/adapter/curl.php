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
 * A download adapter using the cURL PHP integration
 */
class ADownloadAdapterCurl extends ADownloadAdapterAbstract implements ADownloadInterface
{
	protected $headers = array();

	public function __construct()
	{
		$this->priority = 110;
		$this->supportsFileSize = true;
		$this->supportsChunkDownload = true;
		$this->name = 'curl';
		$this->isSupported = function_exists('curl_init') && function_exists('curl_exec') && function_exists('curl_close');
	}

	/**
	 * Download a part (or the whole) of a remote URL and return the downloaded
	 * data. You are supposed to check the size of the returned data. If it's
	 * smaller than what you expected you've reached end of file. If it's empty
	 * you have tried reading past EOF. If it's larger than what you expected
	 * the server doesn't support chunk downloads.
	 *
	 * If this class' supportsChunkDownload returns false you should assume
	 * that the $from and $to parameters will be ignored.
	 *
	 * @param   string   $url     The remote file's URL
	 * @param   integer  $from    Byte range to start downloading from. Use null for start of file.
	 * @param   integer  $to      Byte range to stop downloading. Use null to download the entire file ($from is ignored)
	 * @param   array    $params  Additional params that will be added before performing the download
	 *
	 * @return  string  The raw file data retrieved from the remote URL.
	 *
	 * @throws  AExceptionDownload  A generic exception is thrown on error
	 */
	public function downloadAndReturn($url, $from = null, $to = null, array $params = array())
	{
		$ch = curl_init();

		if (empty($from))
		{
			$from = 0;
		}

		if (empty($to))
		{
			$to = 0;
		}

		if ($to < $from)
		{
			$temp = $to;
			$to = $from;
			$from = $temp;
			unset($temp);
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSLVERSION, 0);
		curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'reponseHeaderCallback'));

		if (!(empty($from) && empty($to)))
		{
			curl_setopt($ch, CURLOPT_RANGE, "$from-$to");
		}

		if (!empty($params))
		{
			foreach ($params as $k => $v)
			{
				@curl_setopt($ch, $k, $v);
			}
		}

		$result = curl_exec($ch);

		$errno = curl_errno($ch);
		$errmsg = curl_error($ch);
		$error = '';
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($result === false)
		{
			$error = AText::sprintf('DOWNLOAD_ERR_CURL_ERROR', $errno, $errmsg);
		}
		elseif (($http_status >= 300) && ($http_status <= 399) && isset($this->headers['location']) && !empty($this->headers['location']))
		{
			return $this->downloadAndReturn($this->headers['location'], $from, $to, $params);
		}
		elseif ($http_status > 399)
		{
			$result = false;
			$errno = $http_status;
			$error = AText::sprintf('DOWNLOAD_ERR_HTTPERROR', $http_status);
		}

		curl_close($ch);

		if ($result === false)
		{
			throw new AExceptionDownload($error, $errno);
		}
		else
		{
			return $result;
		}
	}

	/**
	 * Get the size of a remote file in bytes
	 *
	 * @param   string  $url  The remote file's URL
	 *
	 * @return  integer  The file size, or -1 if the remote server doesn't support this feature
	 */
	public function getFileSize($url)
	{
		$result = -1;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSLVERSION, 0);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_NOBODY, true );
		curl_setopt($ch, CURLOPT_HEADER, true );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');

		$data = curl_exec($ch);
		curl_close($ch);

		if ($data)
		{
			$content_length = "unknown";
			$status = "unknown";
			$redirection = null;

			if (preg_match( "/^HTTP\/1\.[01] (\d\d\d)/i", $data, $matches))
			{
				$status = (int)$matches[1];
			}

			if (preg_match( "/Content-Length: (\d+)/i", $data, $matches))
			{
				$content_length = (int)$matches[1];
			}

			if (preg_match( "/Location: (.*)/i", $data, $matches))
			{
				$redirection = (int)$matches[1];
			}

			if( $status == 200 || ($status > 300 && $status <= 308) )
			{
				$result = $content_length;
			}

			if (($status > 300) && ($status <= 308))
			{
				if (!empty($redirection))
				{
					return $this->getFileSize($redirection);
				}

				return -1;
			}
		}

		return $result;
	}

	/**
	 * Handles the HTTP headers returned by cURL
	 *
	 * @param   resource  $ch    cURL resource handle (unused)
	 * @param   string    $data  Each header line, as returned by the server
	 *
	 * @return  int  The length of the $data string
	 */
	protected function reponseHeaderCallback(&$ch, &$data)
	{
		$strlen = strlen($data);

		if (($strlen) <= 2)
		{
			return $strlen;
		}

		if (substr($data, 0, 4) == 'HTTP')
		{
			return $strlen;
		}

		if (strpos($data, ':') === false)
		{
			return $strlen;
		}

		list($header, $value) = explode(': ', trim($data), 2);

		$this->headers[strtolower($header)] = $value;

		return $strlen;
	}
}
