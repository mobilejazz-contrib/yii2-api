<?php
/**
 * Created by PhpStorm.
 * User: aleix
 * Date: 25/4/15
 * Time: 17:33
 */

namespace common\components;


use Parse\ParseClient;
use Parse\ParsePush;
use Parse\ParseQuery;
use yii\base\Component;

class PushNotification extends Component
{
	public $appid;
	public $masterkey;
	public $apikey;

	private $_parse;

	public function init ()
	{
		$this->_parse = ParseClient::initialize ($this->appid, $this->apikey, $this->masterkey);
	}

	public function send2c ($channel, $message, $data = null, $badge = true, $date = null)
	{
		//Send Push notification
		$pdata = array(
			"channels" => [$channel],
			"data"     => [
				"alert"     => $message,
				"sound"		=> "default"
			]
		);

		if ($data)
			$pdata["data"] = $data;
		if ($badge)
			$pdata["data"]["badge"] = "Increment";
		if ($date)
			$pdata["push_time"] = $date;

		$st = ParsePush::send ($pdata);

		return $st;
	}

	public function send2d ($email, $message, $data = null, $badge = true, $date = null)
	{
		$query = new ParseQuery("ParseInstallation");
		$query->equalTo("alias", $email);

		//Send Push notification
		$pdata = array(
			"where" => $query,
			"data"     => [
				"alert"     => $message,
				"sound"		=> "default"
			]
		);

		if ($data)
			$pdata["data"] = $data;
		if ($badge)
			$pdata["data"]["badge"] = "Increment";
		if ($date)
			$pdata["push_time"] = $date;

		$st = ParsePush::send ($pdata);

		return $st;
	}
}