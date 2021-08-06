<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;
use pocketmine\math\AxisAlignedBB;

class NearCommand extends BaseCommand
{

    /**
     * NearCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('near', 'Игроки рядом.');
        $this->setPermission('core.command.near');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (count($near = $this->getNearPlayers($player->getId(), 80)) < 1) {
            $player->sendMessage('Игроков рядом нет.');
        } else {

            $near_players = [];

            foreach ($near as $players) {
                $near_players[] .= $players->getName();
            }

            $player->sendMessage('Ближайшие игроки в радиусе 80 блоков (' . count($near_players) . '): ' . implode(', ', $near_players));
        }
    }

    private function getNearPlayers(int $id, int $radius = 100) : array
    {
        $players = [];

        $player = $this->getPlugin()->getServer()->getWorldManager()->findEntity($id);

        if ($player instanceof Player) {

            $x = $player->getLocation()->getFloorX();
            $y = $player->getLocation()->getFloorY();
            $z = $player->getLocation()->getFloorZ();

            foreach ($player->getWorld()->getNearbyEntities(new AxisAlignedBB(
                $x - $radius, $y - $radius, $z - $radius,
                $x + $radius, $y + $radius, $z + $radius
            ), $player) as $player) {
                if ($player instanceof Player) {
                    $players[] = $player;
                }
            }
        }
        return $players;
    }
}