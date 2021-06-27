<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\event\Listener;

    use pocketmine\event\server\DataPacketReceiveEvent;
    use pocketmine\network\mcpe\protocol\PlayerActionPacket;
    use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

    class PlayerSwimming implements Listener 
    {
        public function __construct()
        {
            Core::getAPI()->getServer()->getPluginManager()->registerEvents($this, Core::getAPI());
        }

        public function handleDataPacketReceive(DataPacketReceiveEvent $event) : void
        {
            $packet = $event->getPacket();
            
            if ($packet instanceof PlayerActionPacket) {

                $action = $packet->action;
                $player = $event->getOrigin()->getPlayer();
                
                switch ($action) {

                    case PlayerActionPacket::ACTION_START_SWIMMING:
                        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SWIMMING, true);
                        break;

                    case PlayerActionPacket::ACTION_STOP_SWIMMING:
                        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SWIMMING, false);
                        break;
                }

            }

        }

    }

?>
