<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use BloomLand\Core\utils\Utils;
use pocketmine\player\Player;

class ClearInventoryCommand extends BaseCommand
{

    /**
     * ClearInventoryCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('ci', 'Очистить игровой инвентарь.', ['clearinventory']);
        $this->setPermission('core.command.ci');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $cleared = 0;

        $contents = array_merge($player->getInventory()->getContents(), $player->getArmorInventory()->getContents());

        foreach ($contents as $content) {
            $cleared += $content->getCount();
        }

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getCursorInventory()->clearAll();

        if ($cleared > 0) {
            $player->sendMessage('Инвентарь очищен от §b' . Utils::convertCase($cleared, ['предмета', 'предметов', 'предметов']) . ' §rв инвентаре.');
        } else {
            $player->sendMessage('Вы очистили §bпустой §rинвентарь.');
        }
    }
}