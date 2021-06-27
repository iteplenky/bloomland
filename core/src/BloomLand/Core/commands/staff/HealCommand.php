<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class HealCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('heal', 'Восстановить параметры', '/heal', ['feed']);
        }
        
        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $player->setHealth($player->getMaxHealth());
                $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
        
                $player->sendMessage($this->getPlugin()->getPrefix() . 'Вы §bвосстановили§r свои параметры§r.');

            }

            return true;
        }
    }

?>
