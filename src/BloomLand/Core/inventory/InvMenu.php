<?php


namespace BloomLand\Core\inventory;


use Closure;
use InvalidStateException;

use BloomLand\Core\inventory\inventory\SharedInvMenuSynchronizer;
use BloomLand\Core\inventory\session\InvMenuInfo;
use BloomLand\Core\inventory\transaction\DeterministicInvMenuTransaction;
use BloomLand\Core\inventory\transaction\InvMenuTransaction;
use BloomLand\Core\inventory\transaction\InvMenuTransactionResult;
use BloomLand\Core\inventory\transaction\SimpleInvMenuTransaction;
use BloomLand\Core\inventory\type\InvMenuType;
use BloomLand\Core\inventory\type\InvMenuTypeIds;

use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

class InvMenu implements InvMenuTypeIds
{

    /**
     * @param string $identifier
     * @param ...$args
     * @return InvMenu
     */
    public static function create(string $identifier, ...$args) : InvMenu
    {
        return new InvMenu(InvMenuHandler::getTypeRegistry()->get($identifier), ...$args);
    }

    /**
     * @param Closure|null $listener
     * @return Closure
     */
    public static function readonly(?Closure $listener = null) : Closure
    {
        return static function(InvMenuTransaction $transaction) use($listener) : InvMenuTransactionResult {
            $result = $transaction->discard();
            if ($listener !== null) {
                $listener(new DeterministicInvMenuTransaction($transaction, $result));
            }
            return $result;
        };
    }

    /**
     * @var InvMenuType
     */
    protected InvMenuType $type;

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var Closure|null
     */
    protected ?Closure $listener = null;

    /**
     * @var Closure|null
     */
    protected ?Closure $inventory_close_listener = null;

    /**
     * @var Inventory
     */
    protected Inventory $inventory;

    /**
     * @var SharedInvMenuSynchronizer|null
     */
    protected ?SharedInvMenuSynchronizer $synchronizer = null;

    /**
     * InvMenu constructor.
     * @param InvMenuType $type
     * @param Inventory|null $custom_inventory
     */
    public function __construct(InvMenuType $type, ?Inventory $custom_inventory = null)
    {
        if (!InvMenuHandler::isRegistered()) {
            throw new InvalidStateException("Tried creating menu before calling " . InvMenuHandler::class . "::register()");
        }
        $this->type = $type;
        $this->inventory = $this->type->createInventory();
        $this->setInventory($custom_inventory);
    }

    /**
     * @return InvMenuType
     */
    public function getType() : InvMenuType
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param Closure|null $listener
     * @return $this
     */
    public function setListener(?Closure $listener) : self
    {
        $this->listener = $listener;
        return $this;
    }

    /**
     * @param Closure|null $listener
     * @return $this
     */
    public function setInventoryCloseListener(?Closure $listener) : self
    {
        $this->inventory_close_listener = $listener;
        return $this;
    }

    /**
     * @param Player $player
     * @param string|null $name
     * @param Closure|null $callback
     */
    final public function send(Player $player, ?string $name = null, ?Closure $callback = null) : void
    {
        $session = InvMenuHandler::getPlayerManager()->get($player);
        $network = $session->getNetwork();
        $network->dropPending();

        $player->removeCurrentWindow();

        $network->waitUntil($network->getGraphicWaitDuration(), function(bool $success) use($player, $session, $name, $callback) : void{
            if ($success) {
                $graphic = $this->type->createGraphic($this, $player);
                if ($graphic !== null) {
                    $graphic->send($player, $name);
                    $session->setCurrentMenu(new InvMenuInfo($this, $graphic), $callback);
                } else {
                    $session->removeCurrentMenu();
                    if ($callback !== null) {
                        $callback(false);
                    }
                }
            } elseif ($callback !== null) {
                $callback(false);
            }
        });
    }

    /**
     * @return Inventory
     */
    public function getInventory() : Inventory
    {
        return $this->inventory;
    }

    /**
     * @param Inventory|null $custom_inventory
     */
    public function setInventory(?Inventory $custom_inventory) : void
    {
        if ($this->synchronizer !== null) {
            $this->synchronizer->destroy();
            $this->synchronizer = null;
        }

        if ($custom_inventory !== null) {
            $this->synchronizer = new SharedInvMenuSynchronizer($this, $custom_inventory);
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function sendInventory(Player $player) : bool
    {
        return $player->setCurrentWindow($this->getInventory());
    }

    /**
     * @param Player $player
     * @param Item $out
     * @param Item $in
     * @param SlotChangeAction $action
     * @param InventoryTransaction $transaction
     * @return InvMenuTransactionResult
     */
    public function handleInventoryTransaction(Player $player, Item $out, Item $in, SlotChangeAction $action,
                                               InventoryTransaction $transaction) : InvMenuTransactionResult
    {
        $inv_menu_txn = new SimpleInvMenuTransaction($player, $out, $in, $action, $transaction);
        return $this->listener !== null ? ($this->listener)($inv_menu_txn) : $inv_menu_txn->continue();
    }

    /**
     * @param Player $player
     */
    public function onClose(Player $player) : void
    {
        if ($this->inventory_close_listener !== null) {
            ($this->inventory_close_listener)($player, $this->getInventory());
        }

        InvMenuHandler::getPlayerManager()->get($player)->removeCurrentMenu();
    }
}