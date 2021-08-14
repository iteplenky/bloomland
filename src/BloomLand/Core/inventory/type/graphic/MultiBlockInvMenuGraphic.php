<?php


namespace BloomLand\Core\inventory\type\graphic;


use InvalidStateException;

use BloomLand\Core\inventory\type\graphic\network\InvMenuGraphicNetworkTranslator;

use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class MultiBlockInvMenuGraphic implements PositionedInvMenuGraphic
{

    /**
     * @var PositionedInvMenuGraphic[]
     */
    private array $graphics;

    /**
     * MultiBlockInvMenuGraphic constructor.
     * @param array $graphics
     */
    public function __construct(array $graphics)
    {
        $this->graphics = $graphics;
    }

    /**
     * @return PositionedInvMenuGraphic
     */
    private function first() : PositionedInvMenuGraphic
    {
        $first = current($this->graphics);
        if ($first === false) {
            throw new InvalidStateException("Tried sending inventory from a multi graphic consisting of zero entries");
        }

        return $first;
    }

    /**
     * @param Player $player
     * @param string|null $name
     */
    public function send(Player $player, ?string $name) : void
    {
        foreach ($this->graphics as $graphic) {
            $graphic->send($player, $name);
        }
    }

    /**
     * @param Player $player
     * @param Inventory $inventory
     * @return bool
     */
    public function sendInventory(Player $player, Inventory $inventory) : bool
    {
        return $this->first()->sendInventory($player, $inventory);
    }

    /**
     * @param Player $player
     */
    public function remove(Player $player) : void
    {
        foreach ($this->graphics as $graphic) {
            $graphic->remove($player);
        }
    }

    /**
     * @return InvMenuGraphicNetworkTranslator|null
     */
    public function getNetworkTranslator() : ?InvMenuGraphicNetworkTranslator
    {
        return $this->first()->getNetworkTranslator();
    }

    /**
     * @return Vector3
     */
    public function getPosition() : Vector3
    {
        return $this->first()->getPosition();
    }
}
