<?php


namespace BloomLand\Core\command\defaults;

    use BloomLand\Core\command\BaseCommand;

    use pocketmine\player\Player;

    class ListCommand extends BaseCommand
    {

        public function __construct()
        {
            parent::__construct('list', 'Список игроков в сети.', 'list');
            $this->setPermission('core.command.list');
        }

        public function onExecute(Player $player, array $args, string $prefix) : void
        {
            if ($this->getPlugin()->isEnabled()) {

                $nowPlaying = count($player->getServer()->getOnlinePlayers());
                $slots = $player->getServer()->getMaxPlayers();

                $player->sendMessage($prefix . 'Сейчас играет: ' . $nowPlaying . ' из ' . $slots . '.');

            }

        }

    }