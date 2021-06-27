<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\event\Listener;

    use pocketmine\event\server\CommandEvent;
    use pocketmine\command\ConsoleCommandSender;

    class CommandBlock implements Listener 
    {
        public function __construct()
        {
            Core::getAPI()->getServer()->getPluginManager()->registerEvents($this, Core::getAPI());
        }

        public function handleBlockCommand(CommandEvent $event) : void
        {
            $command = explode(":", $event->getCommand());
            $command = $command[1] ?? $command[0];

            $sender = $event->getSender();
            $blockedCommands = Core::getAPI()->getConfig()->get("blocked-commands", []);

            if (!is_array($blockedCommands)) {

                Core::getAPI()->getServer()->getLogger()->error("CONFIG: blocked-commands должен быть массивом.");
                return;

            }

            $datum = $blockedCommands[$command] ?? null;

            if ($datum !== null) {

                if (($datum["console"] ?? false) && $sender instanceof ConsoleCommandSender) {

                    $event->cancel();

                } else if (($datum["in-game"] ?? false) && $sender instanceof BLPlayer) {

                    $event->cancel();

                }

            }

            if ($event->isCancelled()) {

                $sender->sendMessage(Core::getAPI()->getPrefix() . 'Использование команды §cограничено §rдля использования.');
                
            }

        }

    }

?>
