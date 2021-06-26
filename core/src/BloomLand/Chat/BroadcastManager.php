<?php

declare(strict_types=1);


namespace BloomLand\Chat;


use pocketmine\Server;
use pocketmine\utils\TextFormat;

use BloomLand\Core\Core;
use BloomLand\Core\SGPlayer;

class BroadcastManager{
	/** @var int */
	private static $step = -1;
	/** @var array */
	private static $messages = [];

	public static function init() : void{
		self::addBroadcastMessage('instagramTwitter', [
			TextFormat::AQUA . 'Twitter' . TextFormat::GRAY . '/'. TextFormat::LIGHT_PURPLE . 'Instagram' . TextFormat::GRAY,
			TextFormat::AQUA . Core::INSTAGRAM_AND_TWITTER . TextFormat::GRAY
		]);
		self::addBroadcastMessage('discord', [
			TextFormat::BLUE . 'Discord' . TextFormat::GRAY,
			TextFormat::AQUA . Core::DISCORD . TextFormat::GRAY
		]);
		self::addBroadcastMessage('vote', [
			TextFormat::YELLOW . '/vote' . TextFormat::GRAY
		]);
		/*self::addBroadcastMessage('shop', [
			TextFormat::BLUE . 'shopier.com/StormGames' . TextFormat::GRAY,
			TextFormat::GOLD, TextFormat::GRAY
		]);*/

		Core::getAPI()->getScheduler()->scheduleRepeatingTask(new \pocketmine\scheduler\ClosureTask(function() : void{
			self::run();
		}), 20 * 150);
	}

	public static function addBroadcastMessage(string $translateKey, array $params = []) : void{
		self::$messages[] = ['broadcast.message.' . $translateKey, $params];
	}

	private static function run() : void{
		if(++self::$step >= count(self::$messages)){
			self::$step = 0;
		}
		$message = self::$messages[self::$step];

		/** @var SGPlayer $player */
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$player->sendMessage(Core::PREFIX . $player->translate($message[0], $message[1]));
		}
	}
}