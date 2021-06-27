<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class AfkCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('afk', 'Войти в режим АФК', '/afk');
        }

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if ($player instanceof BLPlayer) {

                    if (isset($this->getPlugin()->afk[$player->getLowerCaseName()]) and $this->getPlugin()->afk[$player->getLowerCaseName()] == 1) {

                        unset($this->getPlugin()->afk[$player->getLowerCaseName()]);

                        $player->sendMessage($prefix . 'Вы вышли из режима §bАФК§r.');
                        
                    } else {

                        $this->getPlugin()->afk[$player->getLowerCaseName()] = 1;

                        $player->sendMessage($prefix . 'Вы вошли в режим §bАФК§r.');

                    }

                }

            }

            return true;
        }

    }

?>
