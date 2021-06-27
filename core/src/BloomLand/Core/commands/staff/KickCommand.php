<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class KickCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('kick', 'Выгнать нарушителя правил', '/kick');
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if (!empty($args[0])) {

                    $name = array_shift($args);

                    if (!is_null($this->getPlugin()->getServer()->getPlayerByPrefix($name))) {

                        $intruder = $this->getPlugin()->getServer()->getPlayerByPrefix($name);

                        if (empty($args[0])) {
                        
                            $player->sendMessage($prefix . '§eГерой§r, Вы выгнали §b' . $intruder->getName() . 
                            '§r, не указав причину, пожалуйста осведомите его о причине.');
                            $args[0] = 'неизвестна';
                        
                        }
                        
                        
                        $intruder->kick('§l| §rВы выгнаны игроком §b' . $player->getName() . '§r.' . PHP_EOL . 
                        '§l| §rПричина: §e' . implode(" ", $args) . PHP_EOL . PHP_EOL . '§l| §rОставить жалобу: §bvk.com/bl_pe');

                        $this->getPlugin()->getServer()->broadcastMessage($prefix . 'Игрок §b' . $player->getName() . 
                        ' §rвыгнал нарушителя §d' . $intruder->getName() . '§r, по причине: §e' . implode(" ", $args));

                    } else {

                        $player->sendMessage($this->getPlugin()->getPrefix() . 'Игрок сейчас §cне в игре§r.');

                    }

                } else {

                    $player->sendMessage($prefix . 'Чтобы §bвыгнать нарушителя §rиспользуйте: /kick §r<§7"§bник игрока§7"§r> §r<§eпричина§r>');
                
                }

            } 
            return true;
        }
        

    }

?>
