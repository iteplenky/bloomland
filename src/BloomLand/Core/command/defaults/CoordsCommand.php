<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\player\Player;

class CoordsCommand extends BaseCommand
{

    /**
     * @var array
     */
    private array $enabled = [];

    /**
     * CoordsCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('xyz', 'Управление координатами.', ['coords']);
        $this->setPermission('core.command.xyz');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $name = $player->getLowerCaseName();
        $pk = new GameRulesChangedPacket();
        $this->enabled[$name] = !($this->enabled[$name] ?? false);
        $pk->gameRules = [
            'showcoordinates' => new BoolGameRule($this->enabled[$name], $this->enabled[$name])
        ];
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}