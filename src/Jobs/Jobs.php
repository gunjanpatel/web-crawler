<?php

namespace Crawler\Jobs;

use Crawler\Database\Database;

/**
 * Class to crawl web pages.
 */
class Jobs
{
	public static function add($job)
	{
		$db = Database::prepare(
			"INSERT INTO `jobs`
			(`id`, `headline`, `url`, `description`, `experience_required`)
			VALUES (:id, :headline, :url, :description, :experience_required)"
		);

		try
		{
			$db->execute(
				array(
					'id' => 0,
					'headline' => htmlspecialchars($job->headlines),
					'url' => $job->url,
					'description' => htmlspecialchars($job->description),
					'experience_required' => self::detectExperience($job)
				)
			);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
			die;
		}
	}

	public static function detectExperience($job)
	{
		$regEx = "/(experience)+/";

		preg_match_all($regEx, $job->headlines, $matches);

		if (empty($matches))
		{
			preg_match_all($regEx, $job->description, $matches);

			return (empty($matches));
		}

		return true;
	}
}
