<?php


namespace BloomLand\Core\inventory\session\network;


use Closure;

final class NetworkStackLatencyEntry
{

    /**
     * @var int
     */
    public int $timestamp;

    /**
     * @var int
     */
    public int $network_timestamp;

    /**
     * @var Closure
     */
    public Closure $then;

    /**
     * NetworkStackLatencyEntry constructor.
     * @param int $timestamp
     * @param Closure $then
     * @param int|null $network_timestamp
     */
    public function __construct(int $timestamp, Closure $then, ?int $network_timestamp = null)
    {
        $this->timestamp = $timestamp;
        $this->then = $then;
        $this->network_timestamp = $network_timestamp ?? $timestamp;
    }
}
