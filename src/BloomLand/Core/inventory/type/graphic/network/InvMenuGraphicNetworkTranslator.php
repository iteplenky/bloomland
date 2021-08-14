<?php


namespace BloomLand\Core\inventory\type\graphic\network;


use BloomLand\Core\inventory\session\InvMenuInfo;
use BloomLand\Core\inventory\session\PlayerSession;

use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator
{

    /**
     * @param PlayerSession $session
     * @param InvMenuInfo $current
     * @param ContainerOpenPacket $packet
     */
    public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}