<?php


namespace iStickers;


    use pocketmine\plugin\PluginBase;
    use pocketmine\event\Listener;

	use pocketmine\utils\Config;
    
    use pocketmine\event\player\PlayerChatEvent;

    class Main extends PluginBase implements Listener
    {

        public function onEnable() : void 
        {
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
        }

        public function handleChat(PlayerChatEvent $event) : void 
        {
            $message = $event->getMessage();

            $stickers = (explode(" ", $message));

            foreach ($stickers as $sticker) {

				if ($this->getConfig()->exists($sticker)) {

					$new = str_replace($sticker, $this->getConfig()->get($sticker), $message);

					$event->setMessage($new);

				}

            }

        }

    }

?>
