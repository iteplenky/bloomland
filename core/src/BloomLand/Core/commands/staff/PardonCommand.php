<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use BloomLand\Core\utils\Network;
    use BloomLand\Core\utils\API;
    
    use BloomLand\Core\base\Ban;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class PardonCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('pardon', 'Разблокировать игрока', '/pardon', ['unban']);
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if (!empty($args[0])) {

                    $name = array_shift($args);                    

                    $intruder = $name;

                    if (!Ban::isBanned(strtolower($intruder))) {

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $intruder . ' §rне заблокирован.');
                        return false;

                    }

                    if (empty($args[0])) {
                        $player->sendMessage(Core::getAPI()->getPrefix() . '§eГерой§r, Вы разблокировали §b' . $intruder . '§r, не указав причину, пожалуйста осведомите всех о причине.');
                        $args[0] = 'неизвестна';
                    }

                    Ban::remove(strtolower($intruder));

                    Core::getAPI()->getServer()->broadcastMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $intruder . ' §rразблокирован от руки §d' . $player->getName() . '§r, по причине: §e' . implode(" ", $args) . '§r.');

                } else 
                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Чтобы §bразблокировать игрока §rиспользуйте: /pardon §r<§7"§bник игрока§7"§r> §r<§eпричина§r>');

            } 

            return true;
        }
        
    }

?>