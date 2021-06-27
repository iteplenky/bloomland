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
            parent::__construct('size', 'Изменить собственный размер', '/size');
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if (Core::getAPI()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

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
                                $player->sendMessage($prefix . 'Убедитесь, что вводите §bправильно§r.');
                                $player->sendMessage($prefix . 'Чтобы изменить свой §bразмер§r, используйте: /size <§bsmall§r/§breset§r/§bbig§r>');
                                break;
                        }                        
                        
                    } else {
                        
                        $player->sendMessage($prefix . 'Чтобы изменить свой §bразмер§r, используйте: /size <§bsmall§r/§breset§r/§bbig§r>');
                        
                    }

                }

            }

            return true;
        }

    }

?>
