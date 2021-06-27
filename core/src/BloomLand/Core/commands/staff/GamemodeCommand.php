<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    use pocketmine\player\GameMode;

    class GamemodeCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('gm', 'Сменить игровой режим', '/gm', ['gamemode']);
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if (isset($args[0])) {

                    if (($target = $this->getPlugin()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                        if ($target->getLowerCaseName() == $player->getLowerCaseName()) {

                            if ($player->isSurvival()) {

                                $player->setGamemode(GameMode::CREATIVE());
                                $player->sendMessage($prefix . 'Вы сменили свой режим на §bТворческий§r.');
                
                            } else {
                
                                $player->setGamemode(GameMode::SURVIVAL());
                                $player->sendMessage($prefix . 'Вы сменили свой режим на §cВыживание§r.');

                            }
                            
                        } else {

                            if ($target->isSurvival()) {

                                $target->setGamemode(GameMode::CREATIVE());
                                $target->sendMessage($prefix . 'Игрок §b' . $player->getName() . ' §rсменил Вам режим на §bТворческий§r.');
                                $player->sendMessage($prefix . 'Вы сменили игроку §b' . $target->getName() . ' §rигровой режим на §bТворческий§r.');
                                
                            } else {
                
                                $target->setGamemode(GameMode::SURVIVAL());
                                $target->sendMessage($prefix . 'Игрок §b' . $player->getName() . ' §rсменил Вам режим на §cВыживание§r.');
                                $player->sendMessage($prefix . 'Вы сменили игроку §b' . $target->getName() . ' §rигровой на §cВыживание§r.');

                            }

                        }

    
                    } else {

                        $player->sendMessage($prefix . 'Игрок §cне в игре§r.');

                    }

                } else {

                    if ($player->isSurvival()) {

                        $player->setGamemode(GameMode::CREATIVE());
                        $player->sendMessage($prefix . 'Вы сменили свой режим на §bТворческий§r.');
        
                    } else {
                        $player->setGamemode(GameMode::SURVIVAL());
                        $player->sendMessage($prefix . 'Вы сменили свой режим на §cВыживание§r.');
                        
                    }

                }

            }

            return true;
        }

    }

?>
