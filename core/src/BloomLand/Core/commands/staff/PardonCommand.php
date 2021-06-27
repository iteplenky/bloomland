<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    
    use BloomLand\Core\base\Ban;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class PardonCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('pardon', 'Разблокировать игрока', '/pardon', ['unban']);
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if (!empty($args[0])) {

                    $name = array_shift($args);                    

                    $intruder = $name;

                    if (!Ban::isBanned(strtolower($intruder))) {

                        $player->sendMessage($prefix . 'Игрок §b' . $intruder . ' §rне заблокирован.');
                        return false;

                    }

                    if (empty($args[0])) {

                        $player->sendMessage($prefix . '§eГерой§r, Вы разблокировали §b' . $intruder . '§r, не указав причину, пожалуйста осведомите всех о причине.');
                        $args[0] = 'неизвестна';
                    
                    }

                    Ban::remove(strtolower($intruder));

                    $this->getPlugin()->getServer()->broadcastMessage($prefix . 'Игрок §b' . $intruder . ' §rразблокирован от руки §d' . $player->getName() . 
                    '§r, по причине: §e' . implode(" ", $args) . '§r.');

                } else {

                    $player->sendMessage($prefix . 'Чтобы §bразблокировать игрока §rиспользуйте: /pardon §r<§7"§bник игрока§7"§r> §r<§eпричина§r>');

                }

            } 

            return true;
        }
        
    }

?>
