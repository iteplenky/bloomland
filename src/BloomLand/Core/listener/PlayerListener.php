<?php


namespace BloomLand\Core\listener;


use BloomLand\Core\Core;
use BloomLand\Core\BLPlayer;
use BloomLand\Chat\ChatManager;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;

use pocketmine\event\player\{PlayerChatEvent,
    PlayerCreationEvent,
    PlayerJoinEvent,
    PlayerPreLoginEvent,
    PlayerQuitEvent,
    PlayerRespawnEvent
};

use pocketmine\network\mcpe\protocol\{
    EmotePacket,
    PlayerActionPacket,
    types\entity\EntityMetadataFlags
};

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PlayerListener implements Listener
{

    protected const CHAT_FLOOD_TIME = 1;

    private ?Core $plugin;

    /**
     * @var array
     */
    private array $devices = [];

    /**
     * PlayerListener constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    /**
     * @return Core|null
     */
    private function getPlugin() : ?Core
    {
        return $this->plugin;
    }

    /**
     * @param PlayerCreationEvent $event
     */
    public function handlePlayerCreation(PlayerCreationEvent $event) : void
    {
        $event->setPlayerClass(BLPlayer::class);
    }

    /**
     * @param PlayerPreLoginEvent $event
     */
    public function handlePlayerPreLogin(PlayerPreLoginEvent $event) : void
    {
        $username = strtolower($event->getPlayerInfo()->getUsername());

        $deviceOS = $event->getPlayerInfo()->getExtraData()['DeviceOS'];

        static $device = [
            'Неизвестно',
            'Android',
            'iOS',
            'macOS',
            'FireOS',
            'GearVR',
            'HoloLens',
            'Windows 10',
            'Windows',
            'Dedicated',
            'tvOS',
            'PlayStation',
            'Nintendo',
            'Xbox',
            'Windows Phone'
        ];

        if (ctype_upper(substr($deviceOS, 0, 3))) {
            $this->devices[$username] = 'Toolbox';
        } else {
            $this->devices[$username] = $device[$deviceOS] ?? 'Неизвестно';
        }
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function handlePlayerJoin(PlayerJoinEvent $event) : void
    {
        $event->setJoinMessage('');

        $player = $event->getPlayer();

        $player->setDevice($this->devices[$player->getLowerCaseName()]);

        $player->sendMessage('Добро пожаловать на сервер!');

        $this->getPlugin()->getServer()->broadcastMessage('Игрок ' . $player->getName() . ' присоединился.');
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function handlePlayerChat(PlayerChatEvent $event) : void
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();

        if (!$player->hasPermission('core.chat.bypass')) {
            if (time() - $player->getLastChatTime() <= self::CHAT_FLOOD_TIME) {
                $player->sendMessage($this->getPlugin()->getPrefix() . 'Вы отправляете сообщения слишком часто.');
                $event->cancel();
                return;
            }

            $message = ChatManager::filterText(mb_strtolower($message, 'utf-8'));
        }

        $player->setLastChatTime(time());

        if (!$player->hasPermission('core.chat.colors')) {
            $message = TextFormat::clean($message);
        }

        $event->setMessage($message);
    }

    /**
     * @param EntityDamageEvent $event
     */
    protected function handleEntityDamage(EntityDamageEvent $event) : void
    {
        $player = $event->getEntity();

        if ($player instanceof Player) {
            if ($player->isFighting()) {
                $player->setScoreTag($player->getStringHealth());
            } else {
                $player->setScoreTag('');
            }
        }
    }

    /**
     * @param EntityRegainHealthEvent $event
     */
    public function handleEntityRegain(EntityRegainHealthEvent $event) : void
    {
        $player = $event->getEntity();

        if ($player instanceof Player) {
            if ($player->isFighting()) {
                $player->setScoreTag($player->getStringHealth());
            } else {
                $player->setScoreTag('');
            }
        }
    }

    /**
     * @param PlayerRespawnEvent $event
     */
    public function handlePlayerRespawn(PlayerRespawnEvent $event) : void
    {
        $event->setRespawnPosition($event->getPlayer()->getWorld()->getSpawnLocation());
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function handlePlayerQuit(PlayerQuitEvent $event) : void
    {
        $event->setQuitMessage(null);

        $player = $event->getPlayer();
        if ($player->isAfk()) {
            $player->setAfk(false);
        }
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
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