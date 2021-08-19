<?php


namespace BloomLand\Core\scoreboard;


use BloomLand\Core\Core;

use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class ScoreboardFactory
{

    /**
     * @var array
     */
    private static array $scoreboards = [];

    public static function init()
    {
        Core::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () : void {

            if (empty(self::$scoreboards)) {
                return;
            }

            foreach (self::getScoreboards() as $scoreboard) {

                $data = Core::getInstance()->get('scoreboard');

                if (!$scoreboard->isSpawned()) {
                    $scoreboard->spawn($data['title'], Scoreboard::SORT_ASCENDING);
                }

                $scoreboard->removeLines();
                $scoreboard->setLines($data['lines']);
            }
        }), 20);
    }

    /**
     * @param Player $player
     */
    public static function createScoreboard(Player $player) : void
    {
        self::$scoreboards[$player->getName()] = new Scoreboard($player);
    }

    /**
     * @return array
     */
    public static function getScoreboards() : array
    {
        return self::$scoreboards;
    }

    /**
     * @param Player $player
     * @return Scoreboard|null
     */
    #[Pure]
    public static function getScoreboard(Player $player) : ?Scoreboard
    {
        return self::$scoreboards[$player->getName()] ?? null;
    }

    /**
     * @param Player $player
     */
    public static function removeScoreboard(Player $player)
    {
        unset (self::$scoreboards[$player->getName()]);
    }
}