<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    use BloomLand\Core\sqlite3\SQLite3;

    class SeeMoneyCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('seemoney', 'Просмотреть баланс другого игрока', '/seemoney', ['checkmoney']);
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

                            $player->sendMessage($prefix . 'Вы пытаетесь просмотреть §bсобственный баланс§r.');
                            $player->sendMessage($prefix . $player->translate('coins.get', [number_format($player->getMoney(), 0, '', ' ')]));
                            
                        } else {

                            $player->sendMessage($prefix . 'Игровой баланс игрока §b' . $target->getName() . ' §r> §b' . number_format($target->getMoney(), 0, '', ' ') . ' §rмонет.');
                            $target->sendMessage($prefix . 'Игрок §b' . $player->getName() . ' §rпросмотрел Ваш игровой баланс.');
                            
                        }
                        
                    } else {
                        
                        $player->sendMessage($prefix . 'Игрок сейчас §cне в игре§r.');
                        
                    }
                    
                } else {
                    
                    $player->sendMessage($prefix . 'Чтобы §bпросмотреть баланс§r другого игрока, используйте: /seemoney <§bигрок§r>');
                    
                }
                    
            }

            return true;
        }
        
    }

?>
