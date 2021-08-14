<?php


namespace BloomLand\Core\inventory\inventory;


use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryListener;
use pocketmine\item\Item;

final class SharedInventoryNotifier implements InventoryListener
{

    /**
     * @var Inventory
     */
    protected Inventory $inventory;

    /**
     * @var SharedInventorySynchronizer
     */
    protected SharedInventorySynchronizer $synchronizer;

    /**
     * SharedInventoryNotifier constructor.
     * @param Inventory $inventory
     * @param SharedInventorySynchronizer $synchronizer
     */
    public function __construct(Inventory $inventory, SharedInventorySynchronizer $synchronizer)
    {
        $this->inventory = $inventory;
        $this->synchronizer = $synchronizer;
    }

    /**
     * @param Inventory $inventory
     * @param array $oldContents
     */
    public function onContentChange(Inventory $inventory, array $oldContents) : void
    {
        $this->inventory->getListeners()->remove($this->synchronizer);
        $this->inventory->setContents($inventory->getContents());
        $this->inventory->getListeners()->add($this->synchronizer);
    }

    /**
     * @param Inventory $inventory
     * @param int $slot
     * @param Item $oldItem
     */
    public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem) : void
    {
        if ($slot < $inventory->getSize()) {
            $this->inventory->getListeners()->remove($this->synchronizer);
            $this->inventory->setItem($slot, $inventory->getItem($slot));
            $this->inventory->getListeners()->add($this->synchronizer);
        }
    }
}
