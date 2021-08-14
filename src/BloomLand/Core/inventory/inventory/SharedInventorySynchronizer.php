<?php


namespace BloomLand\Core\inventory\inventory;


use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryListener;
use pocketmine\item\Item;

final class SharedInventorySynchronizer implements InventoryListener
{

    /**
     * @var Inventory
     */
    protected Inventory $inventory;

    /**
     * SharedInventorySynchronizer constructor.
     * @param Inventory $inventory
     */
    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * @return Inventory
     */
    public function getSynchronizingInventory() : Inventory
    {
        return $this->inventory;
    }

    /**
     * @param Inventory $inventory
     * @param array $oldContents
     */
    public function onContentChange(Inventory $inventory, array $oldContents) : void
    {
        $this->inventory->setContents($inventory->getContents());
    }

    /**
     * @param Inventory $inventory
     * @param int $slot
     * @param Item $oldItem
     */
    public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem) : void
    {
        $this->inventory->setItem($slot, $inventory->getItem($slot));
    }
}
