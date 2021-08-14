<?php


namespace BloomLand\Core\inventory\session\network\handler;


use Closure;

use BloomLand\Core\inventory\session\network\NetworkStackLatencyEntry;

final class ClosurePlayerNetworkHandler implements PlayerNetworkHandler
{

    /**
     * @var Closure
     */
    private Closure $creator;

    /**
     * ClosurePlayerNetworkHandler constructor.
     * @param Closure $creator
     */
    public function __construct(Closure $creator)
    {
		$this->creator = $creator;
	}

    /**
     * @param Closure $then
     * @return NetworkStackLatencyEntry
     */
    public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry
    {
		return ($this->creator)($then);
	}
}