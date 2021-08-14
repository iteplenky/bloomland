<?php


namespace BloomLand\Core\inventory\transaction;


use JetBrains\PhpStorm\Pure;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class SimpleInvMenuTransaction implements InvMenuTransaction
{

    /**
     * @var Player
     */
    private Player $player;

    /**
     * @var Item
     */
    private Item $out;

    /**
     * @var Item
     */
    private Item $in;

    /**
     * @var SlotChangeAction
     */
    private SlotChangeAction $action;

    /**
     * @var InventoryTransaction
     */
    private InventoryTransaction $transaction;

    /**
     * SimpleInvMenuTransaction constructor.
     * @param Player $player
     * @param Item $out
     * @param Item $in
     * @param SlotChangeAction $action
     * @param InventoryTransaction $transaction
     */
    public function __construct(Player $player, Item $out, Item $in, SlotChangeAction $action, InventoryTransaction $transaction)
    {
        $this->player = $player;
        $this->out = $out;
        $this->in = $in;
        $this->action = $action;
        $this->transaction = $transaction;
    }

    /**
     * @return Player
     */
    public function getPlayer() : Player
    {
        return $this->player;
    }

    /**
     * @return Item
     */
    public function getOut() : Item
    {
        return $this->out;
    }

    /**
     * @return Item
     */
    public function getIn() : Item
    {
        return $this->in;
    }

    /**
     * @return Item
     */
    #[Pure] 
    public function getItemClicked() : Item
    {
        return $this->getOut();
    }

    /**
     * @return Item
     */
    #[Pure]
      public function getItemClickedWith() : Item
    {
        return $this->getIn();
    }

    /**
     * @return SlotChangeAction
     */
    public function getAction() : SlotChangeAction
    {
        return $this->action;
    }

    /**
     * @return InventoryTransaction
     */
    public function getTransaction() : InventoryTransaction
    {
        return $this->transaction;
    }

    /**
     * @return InvMenuTransactionResult
     */
    #[Pure]
    public function continue() : InvMenuTransactionResult
    {
        return new InvMenuTransactionResult(false);
    }

    /**
     * @return InvMenuTransactionResult
     */
    #[Pure] 
    public function discard() : InvMenuTransactionResult
    {
        return new InvMenuTransactionResult(true);
    }
}
