<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\entity\MoneyCrate;
    use BloomLand\Core\entity\Booster;
    use BloomLand\Core\entity\EnchantedAsh;
    use BloomLand\Core\entity\Quester;
    use BloomLand\Core\entity\Buyer;
    use BloomLand\Core\entity\Yoda;
    use BloomLand\Core\entity\Bin;
    use BloomLand\Core\entity\DonateCrate;

    use BloomLand\Core\entity\floating\Floating;
    use BloomLand\Core\entity\npc\NPCTransfer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    
    use pocketmine\entity\Entity;
    use pocketmine\entity\Skin;
    use pocketmine\nbt\tag\CompoundTag;

    use pocketmine\entity\Location;
    use pocketmine\player\GameMode;
    use pocketmine\item\ItemFactory;
    class NpcCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('npc', 'Управленение объектами','/npc [name]');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    if (!empty($args[0])) {

                        switch ($args[0]) {

                            case 'mode':
                                $player->hidePlayer($player);

                                $player->getInventory()->clearAll();
                                $player->getArmorInventory()->clearAll();

                                $item = ItemFactory::getInstance()->get(-161, 0, 1);
                                $item->setCustomName('§r§fВыход из режима');
                                $player->getInventory()->setItem(8, $item);

                                $item = ItemFactory::getInstance()->get(348, 0, 1);
                                $item->setCustomName('§r§fСброс');
                                $player->getInventory()->setItem(6, $item);

                                $item = ItemFactory::getInstance()->get(341, 0, 1);
                                $item->setCustomName('§r§fВращение вокруг оси');
                                $player->getInventory()->setItem(4, $item);

                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы вошли в режим §cредактирования§r.');
                                break;
                                
                            case 'MoneyCrate': {
                                $nbt = MoneyCrate::createNBT($player->getPosition()->asVector3());
                                $entity = new MoneyCrate($player->getLocation(), MoneyCrate::getCustomSkin(), $nbt);
                                $entity->spawnToAll();
                                break;
                            }

                            case 'Booster': {
                                $nbt = Booster::createNBT($player->getPosition()->asVector3());
                                $entity = new Booster($player->getLocation(), Booster::getCustomSkin(), $nbt);
                                $entity->spawnToAll();
                                break;
                            }

                            case 'EnchantedAsh': {
                                $nbt = EnchantedAsh::createNBT($player->getPosition()->asVector3());
                                $entity = new EnchantedAsh($player->getLocation(), EnchantedAsh::getCustomSkin(), $nbt);
                                $entity->spawnToAll();
                                break;
                            }

                            case 'Yoda': {
                                $nbt = Yoda::createNBT($player->getPosition()->asVector3());
                                $entity = new Yoda($player->getLocation(), Yoda::getCustomSkin(), $nbt);
                                $entity->spawnToAll();
                                break;
                            }

                            case 'Bin': {
                                $nbt = Bin::createNBT($player->getPosition()->asVector3());
                                $entity = new Bin($player->getLocation(), Bin::getCustomSkin(), $nbt);
                                $entity->spawnToAll();
                                break;
                            }

                            case 'Quester': {
                                $nbt = Quester::createNBT($player->getPosition()->asVector3());
                                $entity = new Quester($player->getLocation(), Quester::getCustomSkin(), $nbt);
                                $entity->spawnToAll();
                                break;
                            }

                            case 'DonateCrate': {
                                $nbt = DonateCrate::createNBT($player->getPosition()->asVector3());
                                $entity = new DonateCrate($player->getLocation(), DonateCrate::getCustomSkin(), $nbt);
                                $entity->spawnToAll();
                                break;
                            }

                            // case 'Buyer': {
                            //     $nbt = Buyer::createNBT($player->getPosition()->asVector3());
                            //     $entity = new Buyer($player->getLocation(), Buyer::getCustomSkin(), $nbt);
                            //     $entity->spawnToAll();
                            //     break;
                            // }

                            // case 'Backpack': {
                            //     $nbt = Backpack::createNBT($player->getPosition()->asVector3());
                            //     $entity = new Backpack($player->getLocation(), Backpack::getCustomSkin(), $nbt);
                            //     $entity->spawnToAll();
                            //     break;
                            // }

                            // case 'Decoration': {
                            //     if (!empty($args[0])) {
                                    
                            //         switch ($args[1]) {

                            //             case 'todo': {

                            //                 break;

                            //             }

                            //         }

                            //     }
                            //     break;
                            // }

                        }

                    }

                }

            }
            return true;

        }

    }

?>