<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\event\entity\EntityDamageEvent;

    class KillCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('kill', 'Совершить свое или чужое самоубийство', '/kill');
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if (isset($args[0])) {

                    if (($target = Core::getAPI()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                        if ($target->getLowerCaseName() == $player->getLowerCaseName()) {

                            $target->attack(new EntityDamageEvent($target, EntityDamageEvent::CAUSE_SUICIDE, 1000));
                            $player->sendMessage($prefix . 'Вы совершили §bсамоубийство§r.');
                            
                        } else {

                            $target->attack(new EntityDamageEvent($target, EntityDamageEvent::CAUSE_SUICIDE, 1000));
                            $target->sendMessage($prefix . 'Игрок §b' . $player->getName() . ' §rубил вас при помощи команды.');
                            $player->sendMessage($prefix . 'Вы убили §b' . $target->getName() . ' §rпри помощи команды.');

                        }
                        
                    } else {
                        
                        $player->sendMessage($prefix . 'Игрок сейчас §cне в игре§r.');
                        
                    }
                    
                } else {
                    
                    $player->attack(new EntityDamageEvent($player, EntityDamageEvent::CAUSE_SUICIDE, 1000));
                    $player->sendMessage($prefix . 'Вы совершили §bсамоубийство§r.');

                }
                    
            }

            return true;
        }
        
    }

?>
