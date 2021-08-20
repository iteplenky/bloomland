<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class HackCommand extends BaseCommand
{

    /**
     * @var array
     */
    private array $hackers = [];

    /**
     * HackCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('hack', 'Взломать сервер.', ['hack-extra++']);
        $this->setPermission('core.command.hack');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $player->sendMessage('Начат процесс взлома сервера..');
    }

    /**
     * @return array
     */
    public function getHackers() : array
    {
        return $this->hackers;
    }
}