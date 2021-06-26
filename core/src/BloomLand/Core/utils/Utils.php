<?php
/**
 *  Copyright (C) BloomLand, Inc - All Rights Reserved
 *  Unauthorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 *  Written by Eren Ahmet Akyol <ahmederen123@gmail.com>, July 2019
 */
declare(strict_types=1);

namespace BloomLand\Core\utils;

use DateTime;
use pocketmine\entity\EntityFactory;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Internet;
use BloomLand\Core\entity\FloatingText;
use BloomLand\Core\Core;
use BloomLand\Core\BLPlayer;
use pocketmine\entity\{EntityDataHelper};

use function is_array;
use function urlencode;

class Utils{
	/** @var Server */
	private static $server;
	/** @var string[] */
	private static $usernames = [];

	private const ROMAN_NUMBERS = [
		1000 => 'M',
		900 => 'CM',
		500 => 'D',
		400 => 'CD',
		100 => 'C',
		90 => 'XC',
		50 => 'L',
		40 => 'XL',
		10 => 'X',
		9 => 'IX',
		5 => 'V',
		4 => 'IV',
		1 => 'I'
	];

	public static function init(Server $server) : void{
		self::$server = $server;
	}

	/**
	 * @param BLPlayer|BLPlayer[] $players
	 * @param DataPacket[] $packets
	 *
	 * @return BLPlayer[]
	 */
	public static function broadcastPacket($players, array $packets) : array{
		if(is_array($players)){
			self::$server->broadcastPackets($players, $packets);

			return $players;
		}else{
			foreach($packets as $p){
				$players->getNetworkSession()->sendDataPackets([$p]);
			}

			return [$players];
		}
	}

	public static function getWorldByName(string $name) : ?World{
		$manager = self::$server->getWorldManager();
		if(!$manager->isWorldGenerated($name)){
			$manager->generateWorld($name);
		}
		if(($level = $manager->getWorldByName($name)) !== null){
			return $level;
		}

		$manager->loadWorld($name, true);
		return $manager->getWorldByName($name);
	}

	/**
	 * @param string $function
	 * @return BLPlayer[]
	 */
	public static function getPlayers(string $function = 'getLowerCaseName') : array{
		$players = [];
		foreach(self::$server->getOnlinePlayers() as $player){
			if($player->isOnline()){
				$players[$player->{$function}()] = $player;
			}
		}

		return $players;
	}

	public static function isDefaultLevel(World $level) : bool{
		return $level->getId() === self::$server->getWorldManager()->getDefaultWorld()->getId();
	}

	public static function addBlankEnchantment(Item $item) : Item{
		$item->getNamedTag()->setTag('ench', new ListTag());
		return $item;
	}

	public static function addFloatingText(Position $pos, string $text) : int{
		$nbt = EntityDataHelper::createBaseNBT($pos->floor()->add(0.5, 0, 0.5), null, 0, 0);
		$nbt->setString("CustomName", $text);
		$entity = new FloatingText(FloatingText::class, $pos->getWorld(), $nbt);
		// $entity = EntityFactory::create(FloatingText::class, $pos->getWorld(), $nbt);
		$entity->spawnToAll();

		return $entity->getId();
	}

	public static function translateText(string $sourceLang, string $targetLang, string $text) : string{
		return json_decode(Internet::getURL("https://translate.googleapis.com/translate_a/single?client=gtx&sl=$sourceLang&tl=$targetLang&dt=t&ie=UTF-8&oe=UTF-8&q=" . urlencode($text)))[0][0][0] ?? "ERROR";
	}

	public static function secondsToDateInterval(int $seconds) : \DateInterval{
		return date_create('@' . (($now = time()) + $seconds))->diff(date_create('@' . $now));
	}


	public static function convertRoman(int $number) : string{
		if(isset($roman[$number])){
			return self::ROMAN_NUMBERS[$number];
		}else{
			$res = '';
			foreach(self::ROMAN_NUMBERS as $n => $symbol){
				$matches = intval($number / $n);
				if($matches === 0) continue;

				$res .= str_repeat($symbol, $matches);
				$number %= $n;
			}

			return $res;
		}
	}
	public static function getUsername(string $username) : string{
		if(!isset(self::$usernames[$username])){
			if(($player = Server::getInstance()->getPlayerExact($username)) !== null){
				self::$usernames[$username] = $player->getName();
			}else{
				$result = Core::getDatabase()->select(Core::TABLE_PLAYERS, "name", "WHERE username='$username'");
				self::$usernames[$username] = (($result->num_rows ?? 0) > 0) ? $result->fetch_assoc()["name"] : $username;
			}
		}

		return self::$usernames[$username];
	}

	/**
	 * Roman converter
	 *
	 * @param int $number
	 * @return string
	 */
	public static function convertRoman(int $number) : string{
		if(isset($roman[$number])){
			return self::ROMAN_NUMBERS[$number];
		}else{
			$res = '';
			foreach(self::ROMAN_NUMBERS as $n => $symbol){
				$matches = intval($number / $n);
				if($matches === 0) continue;

				$res .= str_repeat($symbol, $matches);
				$number %= $n;
			}

			return $res;
		}
	}

	public static function minMax(Vector3 $pos1, Vector3 $pos2) : AxisAlignedBB{
		return new AxisAlignedBB(
			min($pos1->x, $pos2->x), min($pos1->y, $pos2->y), min($pos1->z, $pos2->z),
			max($pos1->x, $pos2->x), max($pos1->y, $pos2->y), max($pos1->z, $pos2->z)
		);
	}

	/**
	 * @param string $message
	 * @param array $args
	 * @param array $recipients
	 */
	public static function broadcastMessage(string $message, array $args = [], array $recipients = null) : void{
		$separator = '#';
		$recipients = $recipients ?? Server::getInstance()->getOnlinePlayers();
		/** @var BLPlayer $r */
		foreach($recipients as $r){
			$r->sendMessage($r->translateExtended($message, $args, $separator));
		}
	}

	/**
	 * @param $player
	 * @return string
	 */
	public static function getSkinHeadImageURL($player) : string{
		return 'http://cdn.BloomLand.net/heads/' . urlencode($player instanceof BLPlayer ? $player->getLowerCaseName() : strtolower($player)) . '.png';
	}

	public static function createProgress(int $percent, int $count = 5, int $max = 100, string $accentColor = TextFormat::GREEN, string $backgroundColor = TextFormat::RED) : string{
		for($i = 0, $startIndex = (int) ($percent / ($max / $count)), $text = $accentColor; $i < $count; ++$i){
			if($i === $startIndex){
				$text .= $backgroundColor;
			}
			$text .= '■';
		}

		return $text;
	}
	public static function getItemName(Item $item) : string{
		return $item->hasCustomName() ? explode("\n", $item->getCustomName())[0] : $item->getVanillaName();
	}

	public static function addMonetaryUnit(int $money, bool $applyF = false, bool $format = true) : string{
		if($format){
			$money = substr(number_format($money, 2, ',', '.'), 0, -3);
		}
		return $applyF ? '%s' . Core::MONETARY_UNIT . '%s' . $money : Core::MONETARY_UNIT . $money;
	}

	public static function removeMonetaryUnit(string $money) : int{
		return (int) str_replace('.', '', substr($money, 1));
	}

	/**
	 * @param int    $finish
	 *
	 * @param string $format
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function diffTime(int $finish, string $format = null): string{
		$now = new DateTime();
		$finishDateTime = new DateTime();
		$finishDateTime->setTimestamp($finish);
		$finishDate = new DateTime((string) $finishDateTime->format('Y-m-d H:i:s'));
		$remainingTime = $now->diff($finishDate);
		return $remainingTime->format($format ?? '%m Ay, %d Gün, %H Saat, %i Dakika, %s Saniye');
	}

	public static function getItemCount(int $itemId, Inventory $inventory) : int{
		$count = 0;
		foreach($inventory->getContents(false) as $index => $i){
			if($itemId === $i->getId()){
				$count += $i->getCount();
			}
		}

		return $count;
	}

	public static function getDimension(World $world): int{
		return ["netherisland" => DimensionIds::NETHER, "nether" => DimensionIds::NETHER,
			"end" => DimensionIds::THE_END, "endisland" => DimensionIds::THE_END][$world->getProvider()->getWorldData()->getGenerator()] ?? DimensionIds::OVERWORLD;
	}
}