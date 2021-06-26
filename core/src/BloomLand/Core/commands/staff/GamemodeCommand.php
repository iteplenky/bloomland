<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\player\GameMode;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class GamemodeCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('gm', 'Сменить игровой режим', '/gm', ['gamemode']);
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if (isset($args[0])) {

                    if (($target = Core::getAPI()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                        if ($target->getLowerCaseName() == $player->getLowerCaseName()) {

                            if ($player->isCreative()) {

                                $player->setGamemode(GameMode::SURVIVAL());
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы сменили свой режим на §cВыживание§r.');
                
                            } else {
                
                                $player->setGamemode(GameMode::CREATIVE());
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы сменили свой режим на §bТворческий§r.');
                            }
                            
                        } else {

                            if ($target->isCreative()) {

                                $target->setGamemode(GameMode::SURVIVAL());
                                $target->sendMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $player->getName() . ' §rсменил Вам режим на §cВыживание§r.');
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы сменили игроку §b' . $target->getName() . ' §rигровой на §cВыживание§r.');
                                
                            } else {
                
                                $target->setGamemode(GameMode::CREATIVE());
                                $target->sendMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $player->getName() . ' §rсменил Вам режим на §bТворческий§r.');
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы сменили игроку §b' . $target->getName() . ' §rигровой режим на §bТворческий§r.');
                            }

                        }

    
                    } else {

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок §cне в игре§r.');

                    }

                } else {

                    if ($player->isCreative()) {

                        $player->setGamemode(GameMode::SURVIVAL());
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы сменили свой режим на §cВыживание§r.');
        
                    } else {
        
                        $player->setGamemode(GameMode::CREATIVE());
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы сменили свой режим на §bТворческий§r.');
                    }

                }

            }

            return true;
        }
    }

?>