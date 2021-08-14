<?php


namespace BloomLand\Core\inventory\transaction;


use Closure;
use InvalidStateException;

use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class DeterministicInvMenuTransaction implements InvMenuTransaction
{

    /**
     * @var InvMenuTransaction
     */
    private InvMenuTransaction $inner;

    /**
     * @var InvMenuTransactionResult
     */
    private InvMenuTransactionResult $result;

    /**
     * DeterministicInvMenuTransaction constructor.
     * @param InvMenuTransaction $transaction
     * @param InvMenuTransactionResult $result
     */
    public function __construct(InvMenuTransaction $transaction, InvMenuTransactionResult $result)
    {
		$this->inner = $transaction;
		$this->result = $result;
	}

    /**
     * @return InvMenuTransactionResult
     */
    public function continue() : InvMenuTransactionResult
    {
		throw new InvalidStateException("Cannot change state of deterministic transactions");
	}

    /**
     * @return InvMenuTransactionResult
     */
    public function discard() : InvMenuTransactionResult
    {
		throw new InvalidStateException("Cannot change state of deterministic transactions");
	}

    /**
     * @param Closure|null $callback
     */
    public function then(?Closure $callback) : void
    {
		$this->result->then($callback);
	}

    /**
     * @return Player
     */
    public function getPlayer() : Player
    {
		return $this->inner->getPlayer();
	}

    /**
     * @return Item
     */
    public function getOut() : Item
    {
		return $this->inner->getOut();
	}

    /**
     * @return Item
     */
    public function getIn() : Item
    {
		return $this->inner->getIn();
	}

    /**
     * @return Item
     */
    public function getItemClicked() : Item
    {
		return $this->inner->getItemClicked();
	}

    /**
     * @return Item
     */
    public function getItemClickedWith() : Item
    {
		return $this->inner->getItemClickedWith();
	}

    /**
     * @return SlotChangeAction
     */
    public function getAction() : SlotChangeAction
    {
		return $this->inner->getAction();
	}

    /**
     * @return InventoryTransaction
     */
    public function getTransaction() : InventoryTransaction
    {
		return $this->inner->getTransaction();
	}
}