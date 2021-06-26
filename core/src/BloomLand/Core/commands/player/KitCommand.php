<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\item\ItemFactory;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class KitCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('kit', 'Получить стартовый набор для начала', '/kit');
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    $this->addCustomItems($player);
                    
                }

            }
            return true;
        }

        private function addCustomItems(CommandSender $player): void 
        {
            $config = Core::getAPI()->getConfig();

		    if ($config->exists('kitItems')) {

                foreach ($config->get('kitItems') as [$itemId, $meta, $count]) {
                
                    print_r($config->get('kitItems'));

                    $item = ItemFactory::getInstance()->get($itemId, $meta, $count);
                    $item->setCustomName('§r§7<§aНабор для новичков§7>' . PHP_EOL . '§fИгрока §b' . $player->getName());

                    if (!empty($player->getInventory()->addItem($item))) {

                        $player->sendTitle('§c§lНАБОР', '§fВаш инвентарь §bполностью§f забился', 10, 30, 10);
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы получили неполный §bнабор§r. Следующий раз когда можно будет взять повторно: через§b 1 день§r.');

                        $player->getWorld()->dropItem($player->getEyePos(), $item);
                        return;
            
                    }
    
                }
    
                $player->sendTitle('§a§lНАБОР', '§fДля начала игры получен!', 10, 25, 10);
                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы получили §bнабор§r. Следующий раз когда можно будет взять повторно: через§b 1 день§r.');

            } else {

                $player->sendMessage(Core::getAPI()->getPrefix() . 'Начальный набор для игры еще §cне настроен§r.');
                
            }

        }

    }

?>