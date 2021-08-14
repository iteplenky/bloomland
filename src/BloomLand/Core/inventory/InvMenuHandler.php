<?php


namespace BloomLand\Core\inventory;


use InvalidArgumentException;

use BloomLand\Core\inventory\session\PlayerManager;
use BloomLand\Core\inventory\type\InvMenuTypeRegistry;

use pocketmine\plugin\Plugin;
use pocketmine\Server;

final class InvMenuHandler
{

    /**
     * @var Plugin|null
     */
    private static ?Plugin $registrant = null;

    /**
     * @var InvMenuTypeRegistry
     */
    private static InvMenuTypeRegistry $type_registry;

    /**
     * @var PlayerManager
     */
    private static PlayerManager $player_manager;

    /**
     * @param Plugin $plugin
     */
    public static function register(Plugin $plugin) : void
    {
		if (self::isRegistered()) {
			throw new InvalidArgumentException($plugin->getName() . ' attempted to register ' . self::class . ' twice.');
		}

		self::$registrant = $plugin;
		self::$type_registry = new InvMenuTypeRegistry();
		self::$player_manager = new PlayerManager(self::getRegistrant());
		Server::getInstance()->getPluginManager()->registerEvents(new InvMenuEventHandler(self::getPlayerManager()), $plugin);
	}

    /**
     * @return bool
     */
    public static function isRegistered() : bool
    {
		return self::$registrant instanceof Plugin;
	}

    /**
     * @return Plugin
     */
    public static function getRegistrant() : Plugin
    {
		return self::$registrant;
	}

    /**
     * @return InvMenuTypeRegistry
     */
    public static function getTypeRegistry() : InvMenuTypeRegistry
    {
		return self::$type_registry;
	}

    /**
     * @return PlayerManager
     */
    public static function getPlayerManager() : PlayerManager
    {
		return self::$player_manager;
	}
}