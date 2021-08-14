<?php


namespace BloomLand\Core\inventory\session;


use ReflectionProperty;

use BloomLand\Core\inventory\session\network\PlayerNetwork;
use BloomLand\Core\inventory\session\network\handler\PlayerNetworkHandlerRegistry;

use JetBrains\PhpStorm\Pure;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

final class PlayerManager
{

    /**
     * @var PlayerNetworkHandlerRegistry
     */
    private PlayerNetworkHandlerRegistry $network_handler_registry;

    /**
     * @var array
     */
    private array $sessions = [];

    /**
     * PlayerManager constructor.
     * @param Plugin $registrant
     * @throws \ReflectionException
     */
    public function __construct(Plugin $registrant)
    {
        $this->network_handler_registry = new PlayerNetworkHandlerRegistry();

        $plugin_manager = Server::getInstance()->getPluginManager();
        $plugin_manager->registerEvent(PlayerLoginEvent::class, function(PlayerLoginEvent $event) : void{
            $this->create($event->getPlayer());
        }, EventPriority::MONITOR, $registrant);
        $plugin_manager->registerEvent(PlayerQuitEvent::class, function(PlayerQuitEvent $event) : void{
            $this->destroy($event->getPlayer());
        }, EventPriority::MONITOR, $registrant);
    }

    /**
     * @param Player $player
     */
    private function create(Player $player) : void
    {
        static $_playerInfo = null;
        if ($_playerInfo === null) {
            $_playerInfo = new ReflectionProperty(Player::class, "playerInfo");
            $_playerInfo->setAccessible(true);
        }

        $this->sessions[$player->getId()] = new PlayerSession($player, new PlayerNetwork(
            $player->getNetworkSession(),
            $this->network_handler_registry->get($_playerInfo->getValue($player)->getExtraData()["DeviceOS"] ?? -1)
        ));
    }

    /**
     * @param Player $player
     */
    private function destroy(Player $player) : void
    {
        if (isset($this->sessions[$player_id = $player->getId()])) {
            $this->sessions[$player_id]->finalize();
            unset($this->sessions[$player_id]);
        }
    }

    /**
     * @param Player $player
     * @return PlayerSession
     */
    #[Pure]
    public function get(Player $player) : PlayerSession
    {
        return $this->sessions[$player->getId()];
    }

    /**
     * @param Player $player
     * @return PlayerSession|null
     */
    #[Pure]
    public function getNullable(Player $player) : ?PlayerSession
    {
        return $this->sessions[$player->getId()] ?? null;
    }

    /**
     * @return PlayerNetworkHandlerRegistry
     */
    public function getNetworkHandlerRegistry() : PlayerNetworkHandlerRegistry
    {
        return $this->network_handler_registry;
    }
}
