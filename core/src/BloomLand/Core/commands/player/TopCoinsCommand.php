<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\sqlite3\SQLite3;
    use BloomLand\Core\plugin\AsyncTopsUpdate;
    use pocketmine\scheduler\AsyncTask;
    use pocketmine\Server;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
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

                    Server::getInstance()->getAsyncPool()->submitTask(new AsyncTopsUpdate(Core::getAPI()->getDataFolder(), $player->getId()));
                    
                } 

            }

            return true;
        }

    }

?>