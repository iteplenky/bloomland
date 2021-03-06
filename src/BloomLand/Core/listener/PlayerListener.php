<?php


namespace BloomLand\Core\listener;


use BloomLand\Core\Core;
use BloomLand\Core\BLPlayer;

use BloomLand\Core\chat\ChatManager;

use BloomLand\Core\scoreboard\ScoreboardFactory;

use BloomLand\Core\utils\Utils;
use pocketmine\event\Listener;

use pocketmine\event\entity\{
    EntityDamageEvent,
    EntityRegainHealthEvent
};

use pocketmine\event\player\{
    PlayerChatEvent,
    PlayerJoinEvent,
    PlayerQuitEvent,
    PlayerRespawnEvent,
    PlayerCreationEvent,
    PlayerPreLoginEvent};

use pocketmine\network\mcpe\protocol\{
    EmotePacket,
    PlayerActionPacket,
    types\entity\EntityMetadataFlags,
};

use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PlayerListener implements Listener
{

    protected const CHAT_FLOOD_TIME = 1;

    /**
     * @var Core
     */
    private Core $plugin;

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
     * @return Core
     */
    private function getPlugin() : Core
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
        $device = $this->devices[$player->getLowerCaseName()];

        $player->joined($device);

        $ping = $player->getNetworkSession()->getPing();
        $status = Utils::pingToStatus($ping);

        $player->sendMessage(PHP_EOL . ' §r> Добро пожаловать на сервер: §bBloom§fLand §rBedrock 1.18');
        $player->sendMessage(PHP_EOL . ' §r> Ваш статус подключения: ' . $status . ' §8(§7' . $ping . '§8)' .
            PHP_EOL . ' ');
        $player->sendTitle('§l§bBloom§fLand', 'Добро пожаловать!', 10, 20, 10);

        $this->getPlugin()->getServer()->broadcastPopup(' §f[§b+§f] §f' . $player->getName());
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
                $player->sendMessage($this->getPlugin()->getPrefix() . 'Вы отправляете сообщения §bслишком часто§r.');
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
        $event->setQuitMessage('');

        $player = $event->getPlayer();
        if ($player->isAfk()) {
            $player->setAfk(false);
        }
        ScoreboardFactory::removeScoreboard($player);
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function handleDataReceive(DataPacketReceiveEvent $event) : void
    {
        $pk = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();

        if ($pk instanceof EmotePacket) {

            $this->getPlugin()->getServer()->broadcastPackets($player->getViewers(), [EmotePacket::create($player->getId(), $pk->getEmoteId(), 1 << 0)]);
        }

        if ($pk instanceof PlayerActionPacket) {

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