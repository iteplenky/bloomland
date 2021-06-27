<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\item\ItemFactory;

    class KitCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('kit', 'Получить стартовый набор для начала', '/kit');
        }

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    $this->addCustomItems($player);
                    
                }

            }
            return true;
        }

        private function addCustomItems(CommandSender $player) : void 
        {
            $prefix = $this->getPlugin()->getPrefix();
            $config = $this->getPlugin()->getConfig();

	    if ($config->exists('kitItems')) {

                foreach ($config->get('kitItems') as [$itemId, $meta, $count]) {
                
                    $item = ItemFactory::getInstance()->get($itemId, $meta, $count);
                    $item->setCustomName('§r§7<§aНабор для новичков§7>' . PHP_EOL . '§fИгрока §b' . $player->getName());

                    if (!empty($player->getInventory()->addItem($item))) {

                        $player->getWorld()->dropItem($player->getEyePos(), $item);
            
                    }
    
                }
    
                $player->sendTitle('§a§lНАБОР', '§fДля начала игры получен!', 10, 25, 10);
                $player->sendMessage($prefix . 'Вы получили §bнабор§r. Следующий раз когда можно будет взять повторно: через§b 1 день§r.');

            } else {

                $player->sendMessage($prefix . 'Начальный набор для игры еще §cне настроен§r.');
                
            }

        }

    }

?>
