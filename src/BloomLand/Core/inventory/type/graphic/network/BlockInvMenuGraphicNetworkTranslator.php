<?php


namespace BloomLand\Core\inventory\type\graphic\network;


use InvalidStateException;

use BloomLand\Core\inventory\session\InvMenuInfo;
use BloomLand\Core\inventory\session\PlayerSession;
use BloomLand\Core\inventory\type\graphic\PositionedInvMenuGraphic;

use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

final class BlockInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator
{

    /**
     * @return static
     */
    public static function instance() : self
    {
        static $instance = null;
        return $instance ??= new self();
    }

    /**
     * BlockInvMenuGraphicNetworkTranslator constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param PlayerSession $session
     * @param InvMenuInfo $current
     * @param ContainerOpenPacket $packet
     */
    public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void
    {
        $graphic = $current->graphic;
        if (!($graphic instanceof PositionedInvMenuGraphic)) {
            throw new InvalidStateException("Expected " . PositionedInvMenuGraphic::class . ", got " . get_class($graphic));
        }

        $pos = $graphic->getPosition();
        $packet->x = (int) $pos->x;
        $packet->y = (int) $pos->y;
        $packet->z = (int) $pos->z;
    }
}
