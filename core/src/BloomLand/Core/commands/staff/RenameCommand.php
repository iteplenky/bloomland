<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class RenameCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('rename', 'Переименовать предмет', '/rename');
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                $heldItem = $player->getInventory()->getItemInHand();

                if ($heldItem->getId() != 0) {

                    if (isset($args[0])) {
                    
                        if (strlen($args[0]) < 12) {

                            $heldItem->setCustomName($args[0]);
                    
                            $player->getInventory()->setItemInHand($heldItem);
                            
                            $player->sendMessage($prefix . 'Вы §cсменили название§r предмета на §o' . $args[0] . '§r.');

                        } else {

                            $player->sendMessage($prefix . 'Название предмета уж§c слишком велико§r.'); 

                        }
                        
                    } else {

                        $player->sendMessage($prefix . 'Чтобы §bпереименовать§r предмет нужно указать его §cновое название§r.'); 

                    }
                    
                } else {

                    $player->sendMessage($prefix . 'Чтобы §bпереименовать§r предмет нужно взять его в §cруку§r.'); 

                }

            }

            return true;
        }

    }

?>
