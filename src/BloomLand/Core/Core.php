<?php


namespace BloomLand\Core;


use BloomLand\Core\controllers\Commands;
use BloomLand\Core\controllers\Listeners;

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

        $this->getLogger()->info('Настройка завершена.');
    }

    private function loadControllers() : void
    {
        $pluginManager = $this->getServer()->getPluginManager();

        $pluginManager->registerEvents(new Commands(), $this);
        $pluginManager->registerEvents(new Listeners(), $this);
    }

    private function registerPermissions() : void
    {
        $parent = DefaultPermissions::registerPermission(new Permission('core', 'Батя всех прав.'));

        $commands = DefaultPermissions::registerPermission(new Permission('core.command', 'Мама всех команд.'), [$parent]);

        DefaultPermissions::registerPermission(new Permission('core.command.list'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.coins'), [$commands]);

        $chat = DefaultPermissions::registerPermission(new Permission('core.chat', 'Мама всех прав к чату.'), [$parent]);

        DefaultPermissions::registerPermission(new Permission('core.chat.bypass'), [$chat]);
        DefaultPermissions::registerPermission(new Permission('core.chat.colors'), [$chat]);

        $commands->recalculatePermissibles();
        $parent->recalculatePermissibles();
    }

    private function loadProvider() : void
    {
        $this->provider = new SQLite3Provider($this);
        $this->getLogger()->info('База данных: ' . $this->getProvider()->getName());
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
}
