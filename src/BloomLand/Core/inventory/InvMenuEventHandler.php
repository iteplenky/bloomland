<?php


namespace BloomLand\Core\inventory;


use BloomLand\Core\inventory\session\PlayerManager;

use pocketmine\event\Listener;

use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;

use pocketmine\inventory\transaction\action\SlotChangeAction;

use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;

final class InvMenuEventHandler implements Listener
{

    /**
     * @var PlayerManager
     */
    private PlayerManager $player_manager;

    /**
     * InvMenuEventHandler constructor.
     * @param PlayerManager $player_manager
     */
    public function __construct(PlayerManager $player_manager)
    {
		$this->player_manager = $player_manager;
	}

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event) : void
    {
		$packet = $event->getPacket();
		if ($packet instanceof NetworkStackLatencyPacket) {
			$session = $this->player_manager->getNullable($event->getOrigin()->getPlayer());
			if ($session !== null) {
				$session->getNetwork()->notify($packet->timestamp);
			}
		}
	}

	/**
	 * @param DataPacketSendEvent $event
	 */
	public function onDataPacketSend(DataPacketSendEvent $event) : void
    {
		$packets = $event->getPackets();
		if (count($packets) === 1) {
			$packet = reset($packets);
			if ($packet instanceof ContainerOpenPacket) {
				$targets = $event->getTargets();
				if (count($targets) === 1) {
					$target = reset($targets);
					$session = $this->player_manager->getNullable($target->getPlayer());
					if ($session !== null) {
						$session->getNetwork()->translateContainerOpen($session, $packet);
					}
				}
			}
		}
	}

	/**
	 * @param InventoryCloseEvent $event
	 */
	public function onInventoryClose(InventoryCloseEvent $event) : void
    {
		$player = $event->getPlayer();
		$session = $this->player_manager->getNullable($player);
		if ($session !== null) {
			$current = $session->getCurrent();
			if ($current !== null && $event->getInventory() === $current->menu->getInventory()) {
				$current->menu->onClose($player);
			}
		}
	}

	/**
	 * @param InventoryTransactionEvent $event
	 */
	public function onInventoryTransaction(InventoryTransactionEvent $event) : void
    {
		$transaction = $event->getTransaction();
		$player = $transaction->getSource();

		$player_instance = $this->player_manager->get($player);
		$current = $player_instance->getCurrent();
		if ($current !== null) {
			$inventory = $current->menu->getInventory();
			$network_stack_callbacks = [];
			foreach ($transaction->getActions() as $action) {
				if ($action instanceof SlotChangeAction && $action->getInventory() === $inventory) {
					$result = $current->menu->handleInventoryTransaction($player, $action->getSourceItem(), $action->getTargetItem(), $action, $transaction);
					$network_stack_callback = $result->getPostTransactionCallback();
					if ($network_stack_callback !== null) {
						$network_stack_callbacks[] = $network_stack_callback;
					}
					if ($result->isCancelled()) {
						$event->cancel();
						break;
					}
				}
			}
			if (!empty($network_stack_callbacks)) {
				$player_instance->getNetwork()->wait(static function(bool $success) use($player, $network_stack_callbacks) : void{
					if ($success) {
						foreach ($network_stack_callbacks as $callback) {
							$callback($player);
						}
					}
				});
			}
		}
	}
}