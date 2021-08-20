<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

class CookCommand extends BaseCommand
{

    /**
     * @var array
     */
    private array $map = [
        365 => 366, // chicken
        363 => 364, // beef
        349 => 350, // fish
        319 => 320, // pork
        411 => 412, // rabbit
        423 => 424, // mutton
        392 => 393, // potato

    ];

    /**
     * CookCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('cook', 'Приготовить еду в руках.', ['bake']);
        $this->setPermission('core.command.cook');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $itemInHand = $player->getInventory()->getItemInHand();

        if ($itemInHand->getId() == 0) {
            $player->sendMessage('Перед готовкой нужно взять еду в руку.');
            return;
        }

        if (!isset($this->map[$itemInHand->getId()])) {
            $player->sendMessage('Я не знаю что можно приготовить из этого.');
            return;
        }

        $newItem = ItemFactory::getInstance()->get(
            $this->map[$itemInHand->getId()], $itemInHand->getMeta(), $itemInHand->getCount()
        );

        $player->sendMessage('Еда в руках приготовлена.');
        $player->getInventory()->setItemInHand($newItem);
    }
}