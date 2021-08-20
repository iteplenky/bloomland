<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

class KitCommand extends BaseCommand
{

    /**
     * KitCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('kit', 'Стартовый набор.');
        $this->setPermission('core.command.kit');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $config = $this->getPlugin()->getConfig();

        if ($config->exists('kitItems')) {
            foreach ($config->get('kitItems') as [$id, $meta, $count]) {

                $item = ItemFactory::getInstance()->get($id, $meta, $count);

                $item->setCustomName('§r§7<§aНабор для новичка§7>' . PHP_EOL . '§fИгрока §b' . $player->getName());
                if (!empty($player->getInventory()->addItem($item))) {
                    $player->getWorld()->dropItem($player->getEyePos(), $item);
                }
            }
            $player->sendTitle('§b§lНАБОР', 'Для начала игры получен!', 10, 25, 10);
            $player->sendMessage(
                'Вы получили §bнабор§r. Следующий раз когда можно будет взять повторно: через §b1 день§r.'
            );
        }
    }
}