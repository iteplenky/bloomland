<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class RenameCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('rename', 'Восстановить параметры', '/rename');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $heldItem = $player->getInventory()->getItemInHand();

                if ($heldItem->getId() != 0) {
                    if (isset($args[0])) {
                        if (strlen($args[0]) < 12) {

                            $heldItem->setCustomName($args[0]);
                            $player->getInventory()->setItemInHand($heldItem);
                            
                            $player->sendMessage(' §r> Вы §cсменили название§r предмета на §o' . $args[0] . '§r.');

                        } else
                            $player->sendMessage(' §r> Название предмета уж§c слишком велико§r.'); 
                    } else 
                        $player->sendMessage(' §r> Чтобы §bпереименовать§r предмет нужно указать его §cновое название§r.'); 
                } else 
                    $player->sendMessage(' §r> Чтобы §bпереименовать§r предмет нужно взять его в §cруку§r.'); 

            }

            return true;
        }
    }

?>