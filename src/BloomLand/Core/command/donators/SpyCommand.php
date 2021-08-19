<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use JsonException;
use pocketmine\player\Player;

class SpyCommand extends BaseCommand
{

    /**
     * SpyCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('spy', 'Слежка за командами.', ['console']);
        $this->setPermission('core.command.spy');
    }

    /**
     * @param Player $player
     * @param array $args
     * @throws JsonException
     */
    public function onExecute(Player $player, array $args) : void
    {
        $player->setSpy(!$player->isSpy());
        $player->sendMessage('Вы §b' . ($player->isSpy() ? 'следите' : 'больше не следите') . ' §rза командами.');
    }
}