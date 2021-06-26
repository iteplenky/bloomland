<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use BloomLand\Core\utils\Network;
    use BloomLand\Core\utils\API;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class KickCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('kick', 'Выгнать нарушителя правил', '/kick');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if (!empty($args[0])) {

                    $name = array_shift($args);
                    if (!is_null(Core::getAPI()->getServer()->getPlayerByPrefix($name))) {
                        $intruder = Core::getAPI()->getServer()->getPlayerByPrefix($name);

                        if (empty($args[0])) {
                            $player->sendMessage(Core::getPrefix() . '§eГерой§r, Вы выгнали §b' . $intruder->getName() . '§r, не указав причину, пожалуйста осведомите его о причине.');
                            $args[0] = 'неизвестна';
                        }
                        
                        
                        $intruder->kick('§l| §rВы выгнаны игроком §b' . $player->getName() . '§r.' . PHP_EOL . '§l| §rПричина: §e' . implode(" ", $args) . PHP_EOL . PHP_EOL . '§l| §rОставить жалобу: §bvk.com/bl_pe');
                        Core::getAPI()->getServer()->broadcastMessage(Core::getPrefix() . 'Игрок §b' . $player->getName() . ' §rвыгнал нарушителя §d' . $intruder->getName() . '§r, по причине: §e' . implode(" ", $args));

                    } else {

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок сейчас §cне в игре§r.');

                    }

                } else 
                    $player->sendMessage(Core::getPrefix() . 'Чтобы §bвыгнать нарушителя §rиспользуйте: /kick §r<§7"§bник игрока§7"§r> §r<§eпричина§r>');

            } 
            return true;
        }
        

    }

?>