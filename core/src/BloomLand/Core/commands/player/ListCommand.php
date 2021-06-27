<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    class ListCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('list', 'Список всех игроков', '/list');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $player->sendMessage(Core::getAPI()->getPrefix() . 'Сейчас играет §b' . count($player->getServer()->getOnlinePlayers()) . 
                ' §rиз §3' . $player->getServer()->getMaxPlayers() . '§r.');

            }
            return true;
 
        }

    }

?>
