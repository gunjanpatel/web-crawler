<?php

namespace Crawler;

use Crawler\DOM\Document;
use Crawler\DOM\Xpath;

/**
 * Class to crawl web pages.
 */
class App
{
	protected $url = null;

	protected $depth = 1;

	protected $seen = array();

	protected $dom = null;

	protected $xpath = null;

	public function __construct($url, $depth = 1)
	{
		$this->url   = $url;
		$this->depth = $depth;
	}

	public function Crawl()
	{
		if (!$this->setSeen())
		{
			throw new \Exception("Limit exceed: Only Allow " . $this->depth);
		}

		// Load DOM using set url
		$this->load();

		return $this;
	}

	public function absToReal($href)
	{
		if (0 !== strpos($href, 'http'))
		{
			$path = '/' . ltrim($href, '/');

			if (extension_loaded('http'))
			{
				$href = http_build_url($url, array('path' => $path));
			}
			else
			{
				$parts = parse_url($url);
				$href = $parts['scheme'] . '://';

				if (isset($parts['user']) && isset($parts['pass']))
				{
					$href .= $parts['user'] . ':' . $parts['pass'] . '@';
				}

				$href .= $parts['host'];

				if (isset($parts['port']))
				{
					$href .= ':' . $parts['port'];
				}

				$href .= $path;
			}
		}

		return $href;
	}

	protected function load()
	{
		$this->dom = new Document('1.0');
		$this->dom->loadHTMLFile($this->url);

		return $this->dom;
	}

	protected function xpath()
	{
		$this->xpath = new Xpath($this->dom);
	}

	public function xpathQuery($query)
	{
		if (!$this->xpath)
		{
			$this->xpath();
		}

		return $this->xpath->query($query);
	}

	protected function setSeen()
	{
		if (!$this->overLimit())
		{
			$this->seen[$this->url] = true;

			return true;
		}

		return false;
	}

	protected function overLimit()
	{
		if (isset($this->seen[$this->url]) || $this->depth === 0)
		{
			return true;
		}

		return false;
	}
}
