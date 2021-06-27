<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\item\Durable;

    class RepairCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('repair', 'Восстановить предмет', '/repair');
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                $heldItem = $player->getInventory()->getItemInHand();
                $item = $heldItem->getId();

                if ($item != 0) {

                    if ($heldItem instanceof Durable and $heldItem->getDamage() > 0) {

                        $heldItem->setDamage(0);

                        $player->getInventory()->setItemInHand($heldItem);
                        $player->sendMessage($prefix . 'Предмет в руке §bвосстановлен§r.');

                    } else {

                        $player->sendMessage($prefix . 'Вы уверены в том, что этот предмет можно §cпочинить§r?');

                    }

                } else {

                    $player->sendMessage($prefix . 'Чтобы §bвосстановить§r предмет нужно взять его в §cруку§r.'); 
               
                }

            }

            return true;
        }
        
    }

?>
