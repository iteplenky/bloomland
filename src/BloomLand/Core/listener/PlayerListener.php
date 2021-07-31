<?php


namespace BloomLand\Core\listener;


use BloomLand\Core\Core;
use BloomLand\Core\BLPlayer;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

class PlayerListener implements Listener
{

    private ?Core $plugin;

    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    private function getPlugin() : ?Core
    {
        return $this->plugin;
    }

    public function handlePlayerCreation(PlayerCreationEvent $event) : void
    {
        $event->setPlayerClass(BLPlayer::class);
    }

    public function handlePlayerJoin(PlayerJoinEvent $event) : void
    {
        $event->setJoinMessage('');

        $player = $event->getPlayer();

        $player->sendMessage('Добро пожаловать на сервер!');

        $this->getPlugin()->getServer()->broadcastMessage('Игрок ' . $player->getName() . ' присоединился.');
    }

    public function handlePlayerQuit(PlayerQuitEvent $event) : void
    {
        $event->setQuitMessage(null);
    }

    public function handleDataReceive(DataPacketReceiveEvent $event) : void
    {
        $pk = $event->getPacket();

        if ($pk instanceof EmotePacket) {

            $player = $event->getOrigin()->getPlayer();

            $this->getPlugin()->getServer()->broadcastPackets($player->getViewers(), [EmotePacket::create($player->getId(), $pk->getEmoteId(), 1 << 0)]);
        }

        if ($pk instanceof PlayerActionPacket) {

            $player = $event->getOrigin()->getPlayer();

            switch ($pk->action) {
                case PlayerActionPacket::ACTION_START_SNEAK:
                    $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::CAN_SHOW_NAMETAG, true);
                    break;

                case PlayerActionPacket::ACTION_START_SWIMMING:
                    $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SWIMMING, true);
                    break;

                case PlayerActionPacket::ACTION_STOP_SWIMMING:
                    $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SWIMMING, false);
                    break;

                default:
                    break;

            }
        }
    }
}