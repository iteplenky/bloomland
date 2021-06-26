<?php


namespace BloomLand\Core;


    use BloomLand\Core\Core;

    use pocketmine\utils\TextFormat;

    class MessageEntry
    {
        /** @var array */
        private $lastMessages = [];
        /** @var BLPlayer */
        private $player;

        public function __construct(BLPlayer $player){
            $this->player = $player;
        }

        public function sendMessage(BLPlayer $target, string $message) : void
        {
            if (empty($message)) 
                $this->player->sendMessage(Core::getAPI()->getPrefix() . TextFormat::RED . $this->player->translate("forms.message.notBlank"));
        
            else {
                $message = TextFormat::clean($message);
                
                if ($target->isOnline() && $target->messages !== null) {

                    $message = new Message($message, $this->player->getName());
                    $target->messages->takeMessage($this->player, $message);
                    $this->player->sendMessage(Core::getAPI()->getPrefix() . TextFormat::GREEN . $this->player->translate("message.sendMessage.success", 
                    [TextFormat::YELLOW . $target->getName() . TextFormat::GRAY]));
                
                } else 
                    $this->player->sendMessage(Core::getAPI()->getPrefix() . TextFormat::RED . $this->player->translate("message.sendMessage.offline"));
            }

        }

        public function takeMessage(BLPlayer $sender, Message $message) : void
        {
            $this->addToLastMessages($sender->getName(), $message);
            $sender->messages->addToLastMessages($this->player->getName(), $message);
            $this->player->sendMessage(Core::getAPI()->getPrefix() . $this->player->translate('message.takeMessage.popup', [TextFormat::GOLD . 
            $sender->getName() . TextFormat::GRAY]));
        }

        public function addToLastMessages(string $senderName, Message $message) : void
        {
            $this->lastMessages[$senderName][] = $message;
        }

        public function getMessages(string $senderName) : array
        {
            return $this->lastMessages[$senderName] ?? [];
        }

    }

    class Message
    {
        /** @var string */
        public $message;
        /** @var string */
        public $author;
        /** @var int */
        public $timestamp;

        public function __construct(string $message, string $author, int $timestamp = null)
        {
            $this->message = $message;
            $this->author = $author;
            $this->timestamp = $timestamp ?? time();
        }

        public function dateToString(BLPlayer $player) : string{
            $diff = time() - $this->timestamp;

            if($diff > 3600){ // 60 * 60
                $translate = ['message.time.hour', [floor($diff / 3600)]];
            }elseif($diff > 60){ // 60
                $translate = ['message.time.minute', [floor($diff / 60)]];
            }else{
                $translate = ['message.time.second', [$diff]];
            }

            return "[" . $player->translate(...$translate) . "]";
        }

    }

?>
