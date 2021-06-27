<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    use BloomLand\Core\sqlite3\SQLite3;

    class CoinsCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('coins', 'Ваш баланс монет', '/coins', ['money']);
        }

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if ($player instanceof BLPlayer) {

                    if (count($args) == 3 and $this->getPlugin()->getServer()->isOp($player->getName())) {

                        $target = $args[1];
                        
                        if (is_numeric($args[2])) {

                            if ($args[2] > 0) {
                                
                                if ($args[2] % 1 == 0) {
                                    
                                    $balance = SQLite3::getIntValue(strtolower($target), 'coins');

                                    switch ($args[0]) {

                                        case 'add':
                                            // SQLite3::updateValue(strtolower($target), 'coins', $balance + (int) $args[2]);
                                            $player->sendMessage($prefix . 'Баланс отредактирован пользователю: §b' . $target . '§r.');
                                            break;
            
                                        case 'remove':
                                            // SQLite3::updateValue(strtolower($target), 'coins', $balance - (int) $args[2]);
                                            $player->sendMessage($prefix . 'Баланс отредактирован пользователю: §b' . $target . '§r.');
                                            break;
            
                                        default: 
                                            $player->sendMessage($prefix . 'Укажите один из параметров: <§badd§r/§bremove§r>');
                                            break;
                                    }

                                }
                            
                            }

                        }

                    } else 
                        $player->sendMessage($prefix . $player->translate('coins.get', [number_format($player->getMoney(), 0, '', ' ')]));

                }

            }

            return true;
        }
        
    }

?>
