<?php


namespace BloomLand\Core\controllers;


use pocketmine\permission\Permission;
use pocketmine\permission\DefaultPermissions;

class Permissions
{

    /**
     * Permissions constructor.
     */
    public function __construct()
    {
        $this->registerPermissions();
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
        DefaultPermissions::registerPermission(new Permission('core.command.ci'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.heal'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.fly'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.say'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.kill'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.rename'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.repair'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.xyz'), [$commands]);
        DefaultPermissions::registerPermission(new Permission('core.command.size'), [$commands]);

        $chat = DefaultPermissions::registerPermission(new Permission('core.chat', 'Родительское разрешение для чата.'), [$parent]);

        DefaultPermissions::registerPermission(new Permission('core.chat.bypass'), [$chat]);
        DefaultPermissions::registerPermission(new Permission('core.chat.colors'), [$chat]);

        $commands->recalculatePermissibles();
        $parent->recalculatePermissibles();
    }
}