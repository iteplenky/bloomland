<?php


namespace BloomLand\Core;


    use BloomLand\Core\controllers\Commands;

    use pocketmine\plugin\PluginBase;

    use pocketmine\permission\Permission;
    use pocketmine\permission\DefaultPermissions;
    use pocketmine\permission\DefaultPermissionNames;

    class Core extends PluginBase
    {

        private static ?Core $instance = null;

        protected function onLoad() : void
        {
            self::$instance = $this;
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
        }

        private function registerPermissions() : void
        {
            $parent = DefaultPermissions::registerPermission(new Permission('core', 'Батя всех прав.'));

            $commands = DefaultPermissions::registerPermission(new Permission('core.command', 'Мама всех команд.'), [$parent]);

            DefaultPermissions::registerPermission(new Permission('core.command.list'), [$commands]);

            $commands->recalculatePermissibles();
            $parent->recalculatePermissibles();
        }

        public static function getInstance() : Core
        {
            return self::$instance;
        }

    }
