<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\sqlite3\SQLite3;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class SeeMoneyCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('seemoney', 'Просмотреть баланс другого игрока', '/seemoney', ['checkmoney']);
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if (isset($args[0])) {

                    if (($target = Core::getAPI()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                        if ($target->getLowerCaseName() == $player->getLowerCaseName()) {

                            $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы пытаетесь просмотреть §bсобственный баланс§r.');
                            $player->sendMessage(Core::getAPI()->getPrefix() . $player->translate('coins.get', [number_format($player->getMoney(), 0, '', ' ')]));
                            
                        } else {

                            $player->sendMessage(Core::getAPI()->getPrefix() . 'Игровой баланс игрока §b' . $target->getName() . ' §r> §b' . number_format($target->getMoney(), 0, '', ' ') . ' §rмонет.');
                            $target->sendMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $player->getName() . ' §rпросмотрел Ваш игровой баланс.');
                            
                        }
                        
                    } else {
                        
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок сейчас §cне в игре§r.');
                        
                    }
                    
                } else {
                    
                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Чтобы §bпросмотреть баланс§r другого игрока, используйте: /seemoney <§bигрок§r>');
                    
                }
                    
            }

            return true;
        }
    }

?>