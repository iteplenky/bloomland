<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\event\Listener;

    use pocketmine\event\server\CommandEvent;

    class SpyManager implements Listener 
    {
        public function __construct()
        {
            Core::getAPI()->getServer()->getPluginManager()->registerEvents($this, Core::getAPI());
        }

        public function handleSpyCommand(CommandEvent $event) : void
        {
            if ($event->isCancelled()) return;

            $sender = $event->getSender();
            $cmd = $event->getCommand();
    
            if ($sender instanceof BLPlayer) {

                Core::getAPI()->getServer()->getLogger()->info('§7> §b' . $sender->getName() . '§7: §a/' . $cmd);

            }

        }

    }

?>
