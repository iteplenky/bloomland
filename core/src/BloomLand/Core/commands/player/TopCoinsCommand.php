<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    use BloomLand\Core\plugin\AsyncTopsUpdate;

    class TopCoinsCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('topcoins', 'Список самых богатых игроков', '/topcoins', ['topmoney']);
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    Core::getAPI()->getServer()->getAsyncPool()->submitTask(new AsyncTopsUpdate(
                        Core::getAPI()->getDataFolder(), 
                        $player->getId()
                    ));
                    
                } 

            }

            return true;
        }

    }

?>
