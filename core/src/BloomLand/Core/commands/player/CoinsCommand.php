<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\sqlite3\SQLite3;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    class CoinsCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('coins', 'Ваш баланс монет', '/coins', ['money']);
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    if (count($args) == 3 and Core::getAPI()->getServer()->isOp($player->getName())) {

                        $target = $args[1];
                        
                        if (is_numeric($args[2])) {

                            if ($args[2] > 0) {
                                
                                if ($args[2] % 1 == 0) {
                                    
                                    $balance = SQLite3::getIntValue(strtolower($target), 'coins');

                                    switch ($args[0]) {
                                        case 'add':
                                            SQLite3::updateValue(strtolower($target), 'coins', $balance + (int) $args[2]);
                                            $player->sendMessage('успешно');
                                            break;
            
                                        case 'remove':
                                            SQLite3::updateValue(strtolower($target), 'coins', $balance - (int) $args[2]);
                                            $player->sendMessage('успешно');
                                            break;
            
                                        default: 
                                            $player->sendMessage(' Укажите один из параметров: <§badd§r/§bremove§r>');
                                            break;
                                    }

                                }
                            
                            }

                        }

                    } else 
                        $player->sendMessage(Core::getAPI()->getPrefix() . $player->translate('coins.get', [number_format($player->getMoney(), 0, '', ' ')]));

                }

            }
            return true;
        }
    }

?>