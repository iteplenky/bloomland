<?php


namespace BloomLand\Core\inventory\session\network;


use Closure;
use SplQueue;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

use BloomLand\Core\inventory\session\network\handler\PlayerNetworkHandler;
use BloomLand\Core\inventory\session\PlayerSession;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;

final class PlayerNetwork
{

    /**
     * @var NetworkSession
     */
    private NetworkSession $session;

    /**
     * @var PlayerNetworkHandler
     */
    private PlayerNetworkHandler $handler;

    /**
     * @var NetworkStackLatencyEntry|null
     */
    private ?NetworkStackLatencyEntry $current = null;

    /**
     * @var int
     */
    private int $graphic_wait_duration = 200;

    /**
     * @var SplQueue
     */
    private SplQueue $queue;

    /**
     * PlayerNetwork constructor.
     * @param NetworkSession $session
     * @param PlayerNetworkHandler $handler
     */
    #[Pure]
    public function __construct(NetworkSession $session, PlayerNetworkHandler $handler)
    {
        $this->session = $session;
        $this->handler = $handler;
        $this->queue = new SplQueue();
    }

    /**
     * @return int
     */
    public function getGraphicWaitDuration() : int
    {
        return $this->graphic_wait_duration;
    }

    /**
     * Duration (in milliseconds) to wait between sending the graphic (block)
     * and sending the inventory.
     *
     * @param int $graphic_wait_duration
     */
    public function setGraphicWaitDuration(int $graphic_wait_duration) : void
    {
        if ($graphic_wait_duration < 0) {
            throw new InvalidArgumentException("graphic_wait_duration must be >= 0, got {$graphic_wait_duration}");
        }

        $this->graphic_wait_duration = $graphic_wait_duration;
    }

    public function dropPending() : void
    {
        foreach ($this->queue as $entry) {
            ($entry->then)(false);
        }
        $this->queue = new SplQueue();
        $this->setCurrent(null);
    }

    /**
     * @param Closure $then
     */
    public function wait(Closure $then) : void
    {
        $entry = $this->handler->createNetworkStackLatencyEntry($then);
        if ($this->current !== null) {
            $this->queue->enqueue($entry);
        } else {
            $this->setCurrent($entry);
        }
    }

    /**
     *
     * Waits at least $wait_ms before calling $then(true).
     *
     * @param int $wait_ms
     * @param Closure $then
     * @param int|null $since_ms
     */
    public function waitUntil(int $wait_ms, Closure $then, ?int $since_ms = null) : void
    {
        if ($since_ms === null) {
            $since_ms = (int) floor(microtime(true) * 1000);
        }
        $this->wait(function(bool $success) use($since_ms, $wait_ms, $then) : void{
            if ($success && ((microtime(true) * 1000) - $since_ms) < $wait_ms) {
                $this->waitUntil($wait_ms, $then, $since_ms);
            }else{
                $then($success);
            }
        });
    }

    /**
     * @param NetworkStackLatencyEntry|null $entry
     */
    private function setCurrent(?NetworkStackLatencyEntry $entry) : void
    {
        if ($this->current !== null) {
            $this->processCurrent(false);
            $this->current = null;
        }

        if ($entry !== null) {
            $pk = new NetworkStackLatencyPacket();
            $pk->timestamp = $entry->network_timestamp;
            $pk->needResponse = true;
            if ($this->session->sendDataPacket($pk)) {
                $this->current = $entry;
            } else {
                ($entry->then)(false);
            }
        }
    }

    /**
     * @param bool $success
     */
    private function processCurrent(bool $success) : void
    {
        if ($this->current !== null) {
            ($this->current->then)($success);
            $this->current = null;
            if (!$this->queue->isEmpty()) {
                $this->setCurrent($this->queue->dequeue());
            }
        }
    }

    /**
     * @param int $timestamp
     */
    public function notify(int $timestamp) : void
    {
        if ($this->current !== null && $timestamp === $this->current->timestamp) {
            $this->processCurrent(true);
        }
    }

    /**
     * @param PlayerSession $session
     * @param ContainerOpenPacket $packet
     * @return bool
     */
    public function translateContainerOpen(PlayerSession $session, ContainerOpenPacket $packet) : bool
    {
        $inventory = $this->session->getInvManager()->getWindow($packet->windowId);
        if (
            $inventory !== null &&
            ($current = $session->getCurrent()) !== null &&
            $current->menu->getInventory() === $inventory &&
            ($translation = $current->graphic->getNetworkTranslator()) !== null
        ) {
            $translation->translate($session, $current, $packet);
            return true;
        }
        return false;
    }
}
