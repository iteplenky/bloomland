<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BlockTopCommand extends BaseCommand
{

    /**
     * BlockTopCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('btop', 'Переместиться на самый верхний блок.', ['blocktop']);
        $this->setPermission('core.command.btop');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $x = $player->getLocation()->getFloorX();
        $z = $player->getLocation()->getFloorZ();

        if (is_null(($highestBlock = $player->getWorld()->getHighestBlockAt($x, $z)))) {
            $player->sendMessage('§cВы и так находитесь на самом высоком блоке.');
            return;
        }

        $player->teleport(new Vector3($x, $highestBlock + 1.0, $z));
        $player->sendMessage('Вы §bпереместились §rна самый высокий блок. §8(Y: §7' . $highestBlock . '§8)');
    }
}