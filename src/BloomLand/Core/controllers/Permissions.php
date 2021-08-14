<?php


namespace BloomLand\Core\controllers;


use pocketmine\permission\Permission;
use pocketmine\permission\DefaultPermissions;

class Permissions
{

    /**
     * @var array|string[]
     */
    private array $commandPermissions = [
        'list', 'coins', 'spawn',
        'afk', 'near', 'ci',
        'heal', 'fly', 'say',
        'kill', 'rename', 'repair',
        'xyz', 'size', 'spy',
        'kick', 'god', 'trash'
    ];

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

        foreach ($this->commandPermissions as $permission) {
            DefaultPermissions::registerPermission(new Permission('core.command.' . $permission), [$commands]);
        }

        $chat = DefaultPermissions::registerPermission(new Permission('core.chat', 'Родительское разрешение для чата.'), [$parent]);

        DefaultPermissions::registerPermission(new Permission('core.chat.bypass'), [$chat]);
        DefaultPermissions::registerPermission(new Permission('core.chat.colors'), [$chat]);

        $commands->recalculatePermissibles();
        $parent->recalculatePermissibles();
    }
}