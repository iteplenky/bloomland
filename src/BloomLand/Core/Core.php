<?php


namespace BloomLand\Core;


use BloomLand\Core\controllers\Commands;
use BloomLand\Core\controllers\Listeners;
use BloomLand\Core\controllers\Tasks;

use BloomLand\Core\provider\SQLite3Provider;
use BloomLand\Core\provider\ProviderInterface;
use pocketmine\plugin\PluginBase;

use pocketmine\permission\Permission;
use pocketmine\permission\DefaultPermissions;

use pocketmine\utils\SingletonTrait;

class Core extends PluginBase
{

    use SingletonTrait;

    /**
     * @var string
     */
    private string $prefix = ' > ';

    /**
     * @var ProviderInterface
     */
    private ProviderInterface $provider;

    protected function onLoad() : void
    {
        self::setInstance($this);
    }

    protected function onEnable() : void
    {
        $this->registerPermissions();
        $this->loadControllers();
        $this->loadProvider();
    }

    private function loadControllers() : void
    {
        $pluginManager = $this->getServer()->getPluginManager();

        $pluginManager->registerEvents(new Commands(), $this);
        $pluginManager->registerEvents(new Listeners(), $this);
        $pluginManager->registerEvents(new Tasks(), $this);
    }

    private function registerPermissions() : void
    {
        $parent = DefaultPermissions::registerPermission(new Permission('core', 'Родитель для всех разрешений.'));

        $commands = DefaultPermissions::registerPermission(new Permission('core.command', 'Родительское разрешение для команд.'), [$parent]);

        DefaultPermissions::registerPermission(new Permission('core.command.list'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.coins'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.spawn'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.afk'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.near'), [$commands]);

        $chat = DefaultPermissions::registerPermission(new Permission('core.chat', 'Родительское разрешение для чата.'), [$parent]);

        DefaultPermissions::registerPermission(new Permission('core.chat.bypass'), [$chat]);
        DefaultPermissions::registerPermission(new Permission('core.chat.colors'), [$chat]);

        $commands->recalculatePermissibles();
        $parent->recalculatePermissibles();
    }

    private function loadProvider() : void
    {
        $this->provider = new SQLite3Provider($this);
    }

    /**
     * @return string
     */
    public function getPrefix() : string
    {
        return $this->prefix;
    }

    /**
     * @return ProviderInterface
     */
    public function getProvider() : ProviderInterface
    {
        return $this->provider;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key) : mixed
    {
        return $this->getConfig()->get($key, 'Key ' . $key . ' not found.');
    }
}
