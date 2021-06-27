<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class ReplyCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('reply', 'Быстрый ответ на тайное сообщение', '/reply');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $prefix = Core::getAPI()->getPrefix();

                if (isset($args[0])) {

                    if ($player->hasIterlocutor()) {

                        if (($target = Core::getAPI()->getServer()->getPlayerByPrefix($player->getInterlocutor())) instanceof BLPlayer) {

                            $target->sendMessage(PHP_EOL . $prefix . 'Игрок §b' . $player->getName() . ' §rответил Вам §e' . implode(" ", $args) . '§r.' . PHP_EOL);
                            $player->sendMessage($prefix . 'Вы ответили сообщением: §e' . implode(" ", $args) . '§r игроку §d' . $target->getName() . ' §r');
                            
                        } else $player->sendMessage($prefix . 'Игрок сейчас §cне в игре§r.');

                    } else $player->sendMessage($prefix . 'Вам еще §bникто не написал§r, чтобы Вы смогли ответить на его сообщение§r.');
                    
                } else $player->sendMessage($prefix . 'Чтобы ответить на §bличное сообщение§r, используйте: /reply <§7...§bсообщение§r>');
                    
            }

            return true;
        }
    }

?>
