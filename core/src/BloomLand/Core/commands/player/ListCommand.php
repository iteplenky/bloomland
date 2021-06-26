<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\item\ItemFactory;

    use BloomLand\Core\inventory\InventoryMenu;
    
    class ListCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('list', 'Список всех игроков', '/list');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                // if ($player instanceof BLPlayer) {

                    // $playerNames = Core::getAPI()->getServer()->getOnlinePlayers();
                    
                    // sort($playerNames, SORT_STRING);

                    $inv = new InventoryMenu(InventoryMenu::INVENTORY_TYPE_HOPPER);
                    $inv->setName("§bInventory");
                    $items =
                        [
                            0 => ItemFactory::getInstance()->get(1, 0, 1)->setCustomName("§r§e§lMarket"),
                        ];
    
                    $inv->setItem($items);
                    $inv->send($player);
    
                    if (CustomInventory::isOpeningInventoryMenu($player)) $player->sendMessage('open');
                    else $player->sendMessage('not open');
            
                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Сейчас играет §b' . count($player->getServer()->getOnlinePlayers()) . ' §rиз §3' . $player->getServer()->getMaxPlayers() . '§r.');
                    // $player->sendMessage(Core::getAPI()->getPrefix() . implode(', ', $playerNames->getName()));

                // }

            }
            return true;
 
        }

    }

?>