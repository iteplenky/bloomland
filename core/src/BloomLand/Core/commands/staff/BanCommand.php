<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use BloomLand\Core\utils\Network;
    use BloomLand\Core\utils\API;
    
    use BloomLand\Core\base\Ban;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class BanCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('ban', 'Заблокировать нарушителя правил', '/ban');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if (!empty($args[0])) {

                    $name = array_shift($args);
                    if (!is_null(Core::getAPI()->getServer()->getPlayerByPrefix($name))) {
                        
                        $intruder = Core::getAPI()->getServer()->getPlayerByPrefix($name);

                        if (empty($args[0])) {
                            $player->sendMessage(Core::getAPI()->getPrefix() . '§eГерой§r, Вы заблокировали §b' . $intruder->getName() . 
                            '§r, не указав причину, пожалуйста осведомите его о причине.');
                            $args[0] = 'неизвестна';
                        }

                        Ban::add($intruder->getLowerCaseName(), $player->getLowerCaseName(), implode(" ", $args));
                        
                        $intruder->kick('§l| §rВы заблокированы игроком §a' . $player->getName() . '§r.' . PHP_EOL . '§l| §rПричина: §e' . 
                        implode(" ", $args) . PHP_EOL . PHP_EOL . '§l| §rОставить жалобу: §bvk.com/bl_pe');
                        
                        Core::getAPI()->getServer()->broadcastMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $intruder->getName() . 
                       ' §rзаблокирован от руки §d' . $player->getName() . '§r, по причине: §e' . implode(" ", $args) . '§r.');

                    } else {

                        $intruder = $name;

                        if (Ban::isBanned(strtolower($intruder))) {

                            $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $intruder . 
                            ' §rуже ранее был заблокирован, по причине: §e' . Ban::get(strtolower($intruder), 'reason') . '§r.');
                            return false;

                        }

                        if (empty($args[0])) {
                            $player->sendMessage(Core::getAPI()->getPrefix() . '§eГерой§r, Вы заблокировали §b' . $intruder . 
                            '§r, не указав причину, пожалуйста осведомите его о причине.');
                            $args[0] = 'неизвестна';
                        }

                        Ban::add(strtolower($intruder), $player->getLowerCaseName(), implode(" ", $args));

                        Core::getAPI()->getServer()->broadcastMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $intruder . 
                        ' §rзаблокирован от руки §d' . $player->getName() . '§r, по причине: §e' . implode(" ", $args) . '§r.');
                    }

                } else 
                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Чтобы §bзаблокировать нарушителя §rиспользуйте: /ban §r<§7"§bник игрока§7"§r> §r<§eпричина§r>');

            } 

            return true;
        }
        
    }

?>
