<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use BloomLand\Core\utils\API;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\entity\effect\EffectInstance;
    use pocketmine\entity\effect\VanillaEffects;

    class RelaxCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('relax', 'Почувствовать спокойствие на короткий срок', '/relax');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

               API::playSoundPacket($player, 'ambient.outdoors');
                
               $player->sendTitle('', '', 20, 200, 20);
               
                $player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20, 4)); 
                $player->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 20, 8));
                    
            }

            return true;
        }
        
    }

?>
