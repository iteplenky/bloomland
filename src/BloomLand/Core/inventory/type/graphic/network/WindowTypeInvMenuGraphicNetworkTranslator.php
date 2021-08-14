<?php

declare(strict_types=1);

namespace BloomLand\Core\inventory\type\graphic\network;

use BloomLand\Core\inventory\session\InvMenuInfo;
use BloomLand\Core\inventory\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

final class WindowTypeInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator{

    private int $window_type;

    public function __construct(int $window_type){
        $this->window_type = $window_type;
    }

    public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void{
        $packet->type = $this->window_type;
    }
}
