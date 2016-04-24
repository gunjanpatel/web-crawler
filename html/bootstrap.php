<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Crawler\App;
use Crawler\Jobs\Jobs;

$url = 'https://www.spotify.com/int/jobs/opportunities/all/all/new-york-ny-united-states/';

$Crawler = new App($url, 2);

$anchors = $Crawler->Crawl()->xpathQuery("//h3[@class='job-title']//a");

$jobs = [];

if (!is_null($anchors))
{
	foreach ($anchors as $element)
	{
		$job = new stdClass;

		$job->url = $Crawler->absToReal($element->getAttribute('href')); //'Agile Coach - Spotify.html';

		$child = (new App($job->url))->Crawl();

		$jobElement = $child->xpathQuery("//div[@class='job-description']");
		$jobDescriptions = $jobElement[0]->childNodes;

		$job->headlines = '';

		if (!empty($jobDescriptions) && 'p' == $jobDescriptions[1]->nodeName)
		{
			$headlineNode   = $jobDescriptions[1];
			$job->headlines = $headlineNode->ownerDocument->saveHTML($headlineNode);
		}

		$index = 0;
		$job->description = '';
		$description = [];

		foreach ($jobDescriptions as $key => $node)
		{
			$index++;

			if ($index <= 2)
			{
				continue;
			}

			$description[] = $node->ownerDocument->saveHTML($node);
		}

		$job->description = (!empty($description)) ? implode('', $description) : '';

		// Insert job
		Jobs::add($job);

		array_push($jobs, $job);
	}
}

echo count($jobs);
