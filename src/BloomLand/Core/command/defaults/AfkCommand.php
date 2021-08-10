<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class AfkCommand extends BaseCommand
{

    /**
     * @var array
     */
    private array $playersPos;

    /**
     * AfkCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('afk', 'Войти в режим ожидания.');
        $this->setPermission('core.command.afk');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if ($player->isAfk()) {
            $player->setAfk(false);
            $player->sendMessage($this->getPrefix() . 'Вы вышли из режима ожидания.');
        } else {
            $player->setAfk();

            $this->playersPos[$player->getLowerCaseName()] = [$player->getEyePos()];

            $handler = $this->getPlugin()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() use($player, &$handler) : void
            {
                if ($player->isOnline() && $player->isAfk() && $this->isMoved($player)) {
                    $player->setScoreTag('§bAFK');
                } else {
                    if ($player->isOnline()) {
                        $player->setScoreTag('');
                        $player->sendMessage($this->getPrefix() . 'Вы вышли из режима ожидания.');
                    }
                    $player->setAfk(false);
                    $handler->cancel();
                    $handler = null;
                }
            }), 10);

            $player->sendMessage($this->getPrefix() . 'Вы вошли в режим ожидания.');
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    private function isMoved(Player $player) : bool
    {
        return $this->playersPos[$player->getLowerCaseName()] == [$player->getEyePos()];
    }
}