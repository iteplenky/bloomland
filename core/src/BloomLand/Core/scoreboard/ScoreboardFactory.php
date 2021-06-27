<?php
declare(strict_types=1);
namespace BloomLand\Core\scoreboard;

use BloomLand\Core\Core;
// use JetBrains\PhpStorm\Pure;
use BloomLand\Core\BLPlayer;
use pocketmine\scheduler\ClosureTask;
// use rxffa\spicex\rxffa\RXFFA;

/**
 * Class ScoreboardFactory
 * @package rxffa\spicex\rxffa\scoreboard
 */
class ScoreboardFactory
{
	/** @var array Scoreboard[] */
	private static array $scoreboards = [];

	public static function init(){
		Core::getAPI()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
			if (empty(self::$scoreboards)) return;
			/** @var Scoreboard $scoreboard */
			foreach (self::$scoreboards as $scoreboard) {
				if (!$scoreboard->isSpawned()){
					$scoreboard->spawn("cubecraft.sb.logo", Scoreboard::SORT_ASCENDING);
				}
				$scoreboard->removeLines();
				$scoreboard->setLines([
					// 1 => ' §f-------------- ',
					2 => '  §f01:37',
					3 => ' § ',
					4 => '  §f3.570',
					5 => '  §f{MONEY}',
					6 => ' §f ',
					7 => '  Сайт: §bbl-pе.ru ',
					// 5 => ' §fВК: §bvk.com §f',
					// 3 => ' §fDeaths: §e{DEATHS} ',
					// 4 => ' §fKDR: §e{KDR} ',
				]);
			}
		}), 20);
	}

	/**
	 * @param BLPlayer $player
	 */
	public static function createScoreboard(BLPlayer $player): void {
		ScoreboardFactory::$scoreboards[$player->getName()] = new Scoreboard($player);
	}

	/**
	 * @return Scoreboard[]
	 */
	public static function getScoreboards(): array
	{
		return self::$scoreboards;
	}

	/**
	 * @param BLPlayer $player
	 * @return Scoreboard|null
	 */
	// #[Pure]
	public static function getScoreboard(BLPlayer $player): Scoreboard{
		return self::$scoreboards[$player->getName()] ?? null;
	}

	/**
	 * @param BLPlayer $player
	 */
	public static function removeSecoreboard(BLPlayer $player){
		unset(self::$scoreboards[$player->getName()]);
	}

}