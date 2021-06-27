<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class ClearInventoryCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('ci', 'Очистить игровой инвентарь', '/ci', ['clearinventory']);
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                $cleared = 0;

                if (isset($args[0])) {

                    if (($target = $this->getPlugin()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                        $contents = array_merge($target->getInventory()->getContents(), $target->getArmorInventory()->getContents());
                        
                        foreach ($contents as $content) $cleared += $content->getCount();

                        $target->getInventory()->clearAll();
                        $target->getArmorInventory()->clearAll();

                        $target->sendMessage($prefix . 'Игрок §b' . $player->getName() . ' §rочистил Ваш ивентарь.');
                        
                        if ($cleared > 0) 
                            $player->sendMessage($prefix . 'Было удалено §b' . $cleared . '§r предметов в инвентаре игрока §c' . $target->getName() . '§r.');

                        else 
                            $player->sendMessage($prefix . 'Вы очистили§b пустой§r инвентарь игрока §c' . $target->getName() . '§r.');
                    
                    } else {

                        $player->sendMessage($prefix . 'Игрок сейчас §cне в игре§r.');

                    }

                } else {

                    $contents = array_merge($player->getInventory()->getContents(), $player->getArmorInventory()->getContents());
                    
                    foreach ($contents as $content) $cleared += $content->getCount();

                    $player->getInventory()->clearAll();
                    $player->getArmorInventory()->clearAll();
                    
                    if ($cleared > 0) 
                        $player->sendMessage($prefix . 'Было удалено §b' . $cleared . '§r предметов в инвентаре.');

                    else 
                        $player->sendMessage($prefix . 'Вы очистили§b пустой§r инвентарь.');

                }
                    
            }

            return true;
        }

    }

?>
