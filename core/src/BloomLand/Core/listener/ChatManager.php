<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    use BloomLand\Chat\ChatFilter; 

    use BloomLand\Core\task\TagTask; 

    use pocketmine\event\Listener;

    use pocketmine\event\player\PlayerChatEvent;

    use pocketmine\utils\TextFormat;

    class ChatManager implements Listener 
    {
        public function __construct()
        {
            Core::getAPI()->getServer()->getPluginManager()->registerEvents($this, Core::getAPI());
        }

        private const CHAT_NONE = 0;
        private const CHAT_BADWORD = 1;
        private const CHAT_AD = 2;

        private const CHAT_FLOOD_TIME = 1;

        private $warnings = [];

        public static $tm = [];
        public static $first = []; 
        public static $new = []; 
        
        public function handleChat(PlayerChatEvent $event) : void
        {
            $player = $event->getPlayer();
            $message = $event->getMessage();
            
            if (time() - $player->getLastChatTime() <= self::CHAT_FLOOD_TIME) {

                $player->sendMessage(Core::getAPI()->getPrefix() . $player->translate('chat.slowdown'));
                $event->cancel();
                return;

            }

            $filter = $player->getCore()->getChatFilter()->check($message);

            if ($filter !== ChatFilter::CHAT_NONE) {

                if ($filter == self::CHAT_BADWORD) {

                    if (isset($this->warnings[$player->getName()])) {

                        $this->warnings[$player->getName()]++;

                        if ($this->warnings[$player->getName()] > 10) {

                            $event->cancel();
                            $player->kick('§b§lGUARD §r- система, которая следит за каждым Вашим сообщением.' . PHP_EOL . PHP_EOL . '§r> Вы слишком часто §bругаетесь§r, постарайтесь исправиться.');
                        }

                    } else 
                        $this->warnings[$player->getName()] = 1;

                    $event->cancel();
                    $player->sendMessage(' ');
                    $player->sendMessage(' §r> Ваше сообщение содержало §cплохое слово§r, поэтому оно не было отправлено.');
                    // $player->sendMessage(' §r> Если Вы считаете что была допущена §cошибка§r, сообщите: §b/bug');
                    $player->sendMessage(' ');

                } else {

                    $event->cancel();
                    Core::getAPI()->getServer()->broadcastMessage(' §r§b§lGUARD §r> Игрок §c' . $player->getName() . '§r пытался рекламировать §dсторонние сервисы§r и был наказан.');
                    $player->kick('§b§lGUARD §r- система, которая следит за каждым Вашим сообщением.' . PHP_EOL . PHP_EOL . '§l§b> §rНа сервере §cнельзя§r рекламировать другие сервисы.');
                }

                return;
            }

            $message = mb_strtolower($message, 'utf-8');
    
            $player->setLastChatTime(time());
            
            $message = TextFormat::clean($message);

            $event->setMessage($message);

            if (!in_array($player->getName(), self::$tm)) {
    
                Core::getAPI()->getScheduler()->scheduleRepeatingTask(new TagTask($player, $message, $this), 20);
                
                if (in_array($player->getName(), self::$first)) self::$new[] = $player->getName();
                
            }

        }

    }

?>
