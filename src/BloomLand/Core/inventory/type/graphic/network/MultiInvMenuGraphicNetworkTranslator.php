<?php


namespace BloomLand\Core\inventory\type\graphic\network;


use BloomLand\Core\inventory\session\InvMenuInfo;
use BloomLand\Core\inventory\session\PlayerSession;

use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

final class MultiInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator
{

    /**
     * @var InvMenuGraphicNetworkTranslator[]
     */
    private array $translators;

    /**
     * MultiInvMenuGraphicNetworkTranslator constructor.
     * @param array $translators
     */
    public function __construct(array $translators)
    {
		$this->translators = $translators;
	}

    /**
     * @param PlayerSession $session
     * @param InvMenuInfo $current
     * @param ContainerOpenPacket $packet
     */
    public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void
    {
		foreach($this->translators as $translator){
			$translator->translate($session, $current, $packet);
		}
	}
}