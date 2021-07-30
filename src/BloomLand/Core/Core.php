<?php


namespace BloomLand\Core;


use BloomLand\Core\controllers\Commands;
use BloomLand\Core\controllers\Listeners;

use pocketmine\plugin\PluginBase;

use pocketmine\permission\Permission;
use pocketmine\permission\DefaultPermissions;

use pocketmine\utils\SingletonTrait;

class Core extends PluginBase
{

    use SingletonTrait;

    private string $prefix = ' > ';

    protected function onLoad() : void
    {
        self::setInstance($this);
    }

    protected function onEnable() : void
    {
        $this->registerPermissions();
        $this->loadControllers();
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

        $commands->recalculatePermissibles();
        $parent->recalculatePermissibles();
    }

    public function getPrefix() : string
    {
        return $this->prefix;
    }
}
