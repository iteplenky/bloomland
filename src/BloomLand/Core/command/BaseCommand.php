<?php


namespace BloomLand\Core\command;


use BloomLand\Core\Core;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\player\Player;

class BaseCommand extends Command
{

    private Core $plugin;

    /**
     * BaseCommand constructor.
     * @param string $name
     * @param string $description
     * @param string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(string $name, string $description = '', array $aliases = [], ?string $usageMessage = null)
    {
        parent::__construct($name, $description, $usageMessage, $aliases);

        $this->plugin = Core::getInstance();
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (!$this->getPlugin()->isEnabled()) {
            return;
        }

        $this->setPermissionMessage($this->getPrefix() . 'У вас §cнедостаточно прав§r.');

        if (!$this->testPermission($sender)) {
            return;
        }

        $this->onExecute($sender, $args);
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        // instead of execute, onExecute is now used
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return $this->plugin;
    }

    /**
     * @return string
     */
    #[Pure] 
    public function getPrefix() : string
    {
        return $this->getPlugin()->getPrefix();
    }
}