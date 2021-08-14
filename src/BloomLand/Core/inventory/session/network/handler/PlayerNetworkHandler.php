<?php


namespace BloomLand\Core\inventory\session\network\handler;


use Closure;

use BloomLand\Core\inventory\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler
{

    /**
     * @param Closure $then
     * @return NetworkStackLatencyEntry
     */
    public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}