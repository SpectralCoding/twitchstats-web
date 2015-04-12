<?php
namespace App\Libraries;

class TSFunctions {

	public static function GetTimeIDMinAgo($minago, $accuracy) {
		$now = new \DateTime();
		$start = new \DateTime('2015-01-01', new \DateTimeZone('America/Phoenix'));
		$now->sub(new \DateInterval('PT' . $minago . 'M'));
		$diff = abs($start->getTimestamp() - $now->getTimestamp());
		$diff /= 60;
		$diff /= $accuracy;
		$diff = floor($diff);
		return $diff;
	}

	public static function BestAccuracyForPastMin($pastmin, $aggressive) {
		if ($pastmin <= 15) {
			return 5;					// This should be 1 if we are computing minutely stats
		} elseif ($pastmin <= 30) {
			if ($aggressive) { return 5; }
			return 5;
		} elseif ($pastmin <= 60) {
			if ($aggressive) { return 5; }
			return 15;
		} elseif ($pastmin <= 360) {	// 6 hours
			if ($aggressive) { return 5; }
			return 30;
		} elseif ($pastmin <= 720) {	// 12 hours
			if ($aggressive) { return 15; }
			return 60;
		} elseif ($pastmin <= 1440) {	// 1 day
			if ($aggressive) { return 15; }
			return 60;				//		1 hour
		} elseif ($pastmin <= 10080) {	// 7 week
			if ($aggressive) { return 30; }
			return 360;			//		6 hours
		} elseif ($pastmin <= 43200) {	// 30 days
			if ($aggressive) { return 60; }
			return 720;			//		12 hours
		} else {
			if ($aggressive) { return 360; }
			return 1440;			//		24 hours
		}
	}

}
