<?php


namespace BloomLand\Core\listener;


use BloomLand\Core\Core;

use pocketmine\event\Listener;

use pocketmine\crafting\CraftingGrid;

use pocketmine\event\{
    inventory\CraftItemEvent,
    inventory\InventoryTransactionEvent,
    player\PlayerDropItemEvent,
    player\PlayerInteractEvent,
    block\BlockBreakEvent,
    block\BlockPlaceEvent
};

use pocketmine\inventory\transaction\action\DropItemAction;

class ItemLimitListener implements Listener
{

    private ?Core $plugin;

    /**
     * @var array|int[]
     */
    private array $break = [7];

    /**
     * @var array|int[]
     */
    private array $place = [7, 8, 9, 10, 11, 51, 52, 79, 90, 95, 119, 120, 199, 250];

    /**
     * @var array|int[]
     */
    private array $interact = [23, 54, 58, 61, 62, 116, 117, 125, 130, 145, 146, 154, 199, 218, 458];

    /**
     * ItemLimitListener constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    /**
     * @return Core|null
     */
    private function getPlugin() : ?Core
    {
        return $this->plugin;
    }

    /**
     * @param InventoryTransactionEvent $event
     */
    public function handlePlayerTransaction(InventoryTransactionEvent $event) : void
    {
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();

        if ($player->isCreative()) {
            foreach ($transaction->getInventories() as $inventory) {
                if ($inventory instanceof CraftingGrid) {
                    foreach ($transaction->getActions() as $action) {
                        if ($action instanceof DropItemAction) {
                            $inventory->removeItem($action->getTargetItem());
                        }
                    }
                }
            }
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function handleBlockBreak(BlockBreakEvent $event) : void
    {
        $player = $event->getPlayer();

        if ($player->isOp()) {
            return;
        }

        if (in_array($event->getBlock()->getId(), $this->break)) {
            $event->cancel();
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function handleBlockPlace(BlockPlaceEvent $event) : void
    {
        $player = $event->getPlayer();

        if ($player->isOp()) {
            return;
        }

        if (in_array($event->getBlock()->getId(), $this->place)) {
            $event->cancel();
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function handlePlayerInteract(PlayerInteractEvent $event) : void
    {
        $player = $event->getPlayer();

        if ($player->isOp()) {
            return;
        }

        if ($player->isCreative() && in_array($event->getBlock()->getId(), $this->interact)) {
            $event->cancel();
        }
    }

    /**
     * @param PlayerDropItemEvent $event
     */
    public function handlePlayerDrop(PlayerDropItemEvent $event) : void
    {
        $player = $event->getPlayer();

        if (!$player->isOp() && $player->isCreative()) {
            $event->cancel();
        }
    }

    /**
     * @param CraftItemEvent $event
     */
    public function handleCraftItem(CraftItemEvent $event) : void
    {
        $player = $event->getPlayer();

        if (!$player->isOp() && $player->isCreative()) {
            $event->cancel();
        }
    }
}