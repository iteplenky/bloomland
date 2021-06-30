<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\event\Listener;

    use pocketmine\event\player\PlayerChatEvent;

    class ChatGameListener implements Listener 
    {
        public function __construct()
        {
            Core::getAPI()->getServer()->getPluginManager()->registerEvents($this, Core::getAPI());
        }

        public function handleChat(PlayerChatEvent $event) : void
        {
            $player = $event->getPlayer();
            $message = $event->getMessage();

            if ($message == Core::getAPI()->chatGame) {
                
                $player->addMoney(100 * mt_rand(1, 2));

                $player->sendTitle('', '§fбонус начислен', 10, 20, 5);
                
                foreach (Core::getAPI()->getServer()->getOnlinePlayers() as $players) {
                    
                    $players->sendMessage(Core::getAPI()->getPrefix() . ' Игрок §b' . $player->getName() . ' §rугадал правильный ответ и получает монетный бонус.');
                    
                }

                $event->cancel();
                Core::getAPI()->chatGame = '';
                return;

            } 

        }

    }

?>
