<?php


namespace BloomLand\Core\inventory\transaction;


use Closure;

final class InvMenuTransactionResult{

    /**
     * @var bool
     */
    private bool $cancelled;

    /**
     * @var Closure|null
     */
    private ?Closure $post_transaction_callback = null;

    /**
     * InvMenuTransactionResult constructor.
     * @param bool $cancelled
     */
    public function __construct(bool $cancelled)
    {
        $this->cancelled = $cancelled;
    }

    /**
     * @return bool
     */
    public function isCancelled() : bool
    {
        return $this->cancelled;
    }

    /**
     * 
     * Notify when we have escaped from the event stack trace and the
     * client's network stack trace.
     * Useful for sending forms and other stuff that cant be sent right
     * after closing inventory.
     * 
     * @param Closure|null $callback
     * @return $this
     */
    public function then(?Closure $callback) : self
    {
        $this->post_transaction_callback = $callback;
        return $this;
    }

    /**
     * @return Closure|null
     */
    public function getPostTransactionCallback() : ?Closure
    {
        return $this->post_transaction_callback;
    }
}
