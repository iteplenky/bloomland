<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class SizeCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('size', 'Переместиться на место появления', '/size');
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    if (isset($args[0])) {

                        switch ($args[0]) {
                            case 'small':
                                $player->setScale(0.8);
                                break;

                            case 'reset':
                                $player->setScale(1.0);
                                break;

                            case 'big':
                                $player->setScale(1.4);
                                break;
                            
                            default:
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Убедитесь, что вводите §bправильно§r.');
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Чтобы изменить свой §bразмер§r, используйте: /size <§bsmall§r/§breset§r/§bbig§r>');
                                break;
                        }                        
                        
                    } else {
                        
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Чтобы изменить свой §bразмер§r, используйте: /size <§bsmall§r/§breset§r/§bbig§r>');
                        
                    }

                }

            }

            return true;
        }

    }

?>