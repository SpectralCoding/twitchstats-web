<?php namespace App\Http\Controllers;
use App\Libraries\TSFunctions;

class DataController extends Controller {

	public function __construct() {
	}

	public function channel($channel, $pastmin, $agressive) {
		$accuracy = TSFunctions::BestAccuracyForPastMin($pastmin, $agressive);
		$startID = TSFunctions::GetTimeIDMinAgo($pastmin, $accuracy);
		$redis = \Illuminate\Support\Facades\Redis::connection();
		$lineData = $redis->hgetall("Line:" . $channel . "|" . $accuracy);
		$returnData = array();
		foreach ($lineData as $lineKey => $lineCount) {
			$split = explode('|', $lineKey);
			$timeID = $split[0];
			if ($timeID >= $startID) {
				settype($lineCount, "int");
				$countType = $split[1];
				if ($countType == "Total") {
					$returnData[$timeID] = $lineCount;
				}
			}
		}
		ksort($returnData);
		return response()->json(array('accuracy' => $accuracy, 'data' => $returnData));
	}

	public function topchannelforemote($emote, $pastmin, $count) {
		$accuracy = TSFunctions::BestAccuracyForPastMin($pastmin, false);
		$startID = TSFunctions::GetTimeIDMinAgo($pastmin, $accuracy);
		$redis = \Illuminate\Support\Facades\Redis::connection();
		$channelList = $redis->smembers('Logs');
		$getList = array();
		foreach ($channelList as $curChannel) {
			$split = explode('|', $curChannel);
			$channelName = $split[0];
			$getList[] = $channelName;
		}
		$getList = array_unique($getList);
		$channelThisEmoteCount = array();
		$channelAllEmoteCount = array();
		foreach ($getList as $channelName) {
			$channelData = $redis->hgetall('EmoteTime:' . $channelName . '|' . $accuracy);
			foreach ($channelData as $typeKey => $typeCount) {
				$split = explode('|', $typeKey);
				$curEmote = $split[0];
				$timeID = $split[1];
				if ($timeID >= $startID) {
					if ($curEmote == $emote) {
						if (!array_key_exists($channelName, $channelThisEmoteCount)) {
							$channelThisEmoteCount[$channelName] = 0;
						}
						$channelThisEmoteCount[$channelName] += $typeCount;
					}
					if (!array_key_exists($channelName, $channelAllEmoteCount)) {
						$channelAllEmoteCount[$channelName] = 0;
					}
					$channelAllEmoteCount[$channelName] += $typeCount;
				}
			}
		}
		$emotePercent = array();
		foreach ($channelAllEmoteCount as $channelName => $thisEmoteCount) {
			$emotePercent[$channelName] = round($channelThisEmoteCount[$channelName] / $channelAllEmoteCount[$channelName] * 100, 2);
		}
		arsort($emotePercent);
		return response()->json(array_slice($emotePercent, 0, $count));
	}

	public function topchannels($pastmin, $count) {
		$accuracy = TSFunctions::BestAccuracyForPastMin($pastmin, false);
		$startID = TSFunctions::GetTimeIDMinAgo($pastmin, $accuracy);
		$redis = \Illuminate\Support\Facades\Redis::connection();
		$channelList = $redis->smembers('Lines');
		$getList = array();
		foreach ($channelList as $curChannel) {
			$split = explode('|', $curChannel);
			$channelName = str_replace('Line:', '', $split[0]);
			$curAcc = $split[1];
			if ($curAcc == $accuracy) {
				if ($channelName != '_global') {
					$getList[] = $channelName;
				}
			}
		}
		$allChannels = array();
		foreach ($getList as $channelName) {
			$channelData = $redis->hgetall('Line:' . $channelName . '|' . $accuracy);
			foreach ($channelData as $typeKey => $typeCount) {
				$split = explode('|', $typeKey);
				$timeID = $split[0];
				$type = $split[1];
				if ($timeID >= $startID) {
					if ($type == 'Total') {
						if (!array_key_exists($channelName, $allChannels)) {
							$allChannels[$channelName] = 0;
						}
						$allChannels[$channelName] += $typeCount;
					}
				}
			}
		}
		arsort($allChannels);
		$topChannels = array_slice($allChannels, 0, $count);
		$totalCount = array_sum($allChannels);
		$otherTotal = $totalCount - array_sum($topChannels);
		return response()->json(array_merge($topChannels, array('Other' => $otherTotal)));
	}

	public function topemotes($channel, $pastmin, $count) {
		$accuracy = TSFunctions::BestAccuracyForPastMin($pastmin, false);
		$startID = TSFunctions::GetTimeIDMinAgo($pastmin, $accuracy);
		$redis = \Illuminate\Support\Facades\Redis::connection();
		$emoteData = $redis->hgetall('EmoteTime:' . $channel . '|' . $accuracy);
		$allEmotes = array();
		foreach ($emoteData as $emoteKey => $emoteCount) {
			$split = explode('|', $emoteKey);
			$emoteName = $split[0];
			$timeID = $split[1];
			if ($timeID >= $startID) {
				if (!array_key_exists($emoteName, $allEmotes)) {
					$allEmotes[$emoteName] = 0;
				}
				$allEmotes[$emoteName] += $emoteCount;
			}
		}
		arsort($allEmotes);
		$topEmotes = array_slice($allEmotes, 0, $count);
		$totalCount = array_sum($allEmotes);
		$otherTotal = $totalCount - array_sum($topEmotes);
		return response()->json(array_merge($topEmotes, array('Other' => $otherTotal)));
	}

	public function emote($channel, $emote, $accuracy) {
		$redis = \Illuminate\Support\Facades\Redis::connection();
		$emoteData = $redis->hgetall('EmoteTime:' . $channel . '|' . $accuracy);
		$return = array();
		if ($emote == '*') {
			foreach ($emoteData as $emoteKey => $emoteCount) {
				$split = explode('|', $emoteKey);
				$emoteName = $split[0];
				$timeID = $split[1];
				$return[$emoteName][$timeID] = $emoteCount;
			}
		} else {
			foreach ($emoteData as $emoteKey => $emoteCount) {
				$split = explode('|', $emoteKey);
				$emoteName = $split[0];
				$timeID = $split[1];
				if ($emoteName == $emote) {
					$return[$timeID] = $emoteCount;
				}
			}
		}
		ksort($return);
		return response()->json($return);
	}


}
