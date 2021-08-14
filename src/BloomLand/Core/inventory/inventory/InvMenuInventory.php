<?php


namespace BloomLand\Core\inventory\inventory;


use pocketmine\block\inventory\BlockInventory;
use pocketmine\inventory\SimpleInventory;
use pocketmine\world\Position;

final class InvMenuInventory extends SimpleInventory implements BlockInventory
{

    /**
     * @var Position
     */
    protected Position $holder;

    /**
     * InvMenuInventory constructor.
     * @param int $size
     */
    public function __construct(int $size)
    {
        parent::__construct($size);
        $this->holder = new Position(0, 0, 0, null);
    }

    /**
     * @return Position
     */
    public function getHolder() : Position
    {
        return $this->holder;
    }
}
