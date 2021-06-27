<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class FlyCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('fly', 'Управление режимом полета', '/fly');
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix(); 

                if (!$player->isCreative()) {

                    if ($player->getAllowFlight()) {
    
                        $player->setAllowFlight(false);
                        $player->setFlying(false);
    
                        $player->sendMessage($prefix . 'Вы §cвыключили§r режим полета.');
    
                    } else {

                        $player->setAllowFlight(true);
                        $player->setFlying(true);
    
                        $player->sendMessage($prefix . 'Вы §bвключили§r режим полета.');
                        
                    }
    
                } else
                    $player->sendMessage($prefix . 'Вы §cне можете §rуправлять режимом полета во время§b Творческого §rрежима.');

            }

            return true;
        }
        
    }

?>
