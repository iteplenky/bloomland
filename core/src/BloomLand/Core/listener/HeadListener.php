<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use BloomLand\Core\entity\Head;

    use pocketmine\event\Listener;

    use pocketmine\event\player\PlayerDeathEvent;

    class HeadListener implements Listener 
    {
        public function __construct()
        {
            Core::getAPI()->getServer()->getPluginManager()->registerEvents($this, Core::getAPI());
        }

        public function handleDeathEvent(PlayerDeathEvent $event) : void
        {
            if ($event->isCancelled()) return;

            $player = $event->getPlayer();

            $nbt = Head::createNBT($player->getLocation()->asVector3());

            $nbt->setTag(new CompoundTag('Skin', [
                new StringTag('Name', $player->getName()),
                new ByteArrayTag('Data', $player->getSkin()->getSkinData())
            ]));

            $entity = new Head($player->getWorld(), $nbt);
            $name = $player->getName();
            $entity->setNameTag('игрок ' . $name);
            $entity->setNameTagVisible(true);
            $entity->spawnToAll();
            // $this->getScheduler()->scheduleRepeatingTask(new clearTask($entity, $name,$this),20);
        }

    }

?>
