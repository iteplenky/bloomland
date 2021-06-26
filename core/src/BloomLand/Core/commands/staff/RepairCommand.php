<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\item\enchantment\EnchantmentInstance;
    use pocketmine\item\enchantment\VanillaEnchantments;

    use pocketmine\item\Durable;

    class RepairCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('repair', 'Восстановить предмет', '/repair');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $heldItem = $player->getInventory()->getItemInHand();
                $item = $heldItem->getId();

                if ($item != 0) {

                    if ($heldItem instanceof Durable and $heldItem->getDamage() > 0) {

                        $heldItem->setDamage(0);

                        // $heldItem->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PUNCH(), 1));

                        $player->getInventory()->setItemInHand($heldItem);
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Предмет в руке §bвосстановлен§r.');

                    } else 
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы уверены в том, что этот предмет можно §cпочинить§r?');
                } else 
                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Чтобы §bвосстановить§r предмет нужно взять его в §cруку§r.'); 
            }

            return true;
        }
    }

?>