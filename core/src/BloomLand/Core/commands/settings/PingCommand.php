<?php


namespace BloomLand\Core\commands\settings;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class PingCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('ping', 'Скорость соединения с сервером', '/ping <Вы|Игрок>');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    if (isset($args[0])) {

                        $prefix = Core::getAPI()->getServer()->getPlayerByPrefix($args[0]);

                        $target = !is_null($prefix) ? 
                        $player->sendMessage(Core::getPrefix() . '§b' . $prefix->getName() . ' §r' . self::getPing($prefix)) : 
                        $player->sendMessage($player->translate('targetNotFound'));
                        
                    } else {

                        $player->sendMessage(Core::getPrefix() . $player->translate('yoursSmth') . ' ' . self::getPing($player));

                    }

                }

            }
            return true;
        }

        public static function getPing($target): string
        {
            $ping = $target->getNetworkSession()->getPing();
            
            $status = $target->translate('other.connectionVeryBad');

            if ($ping < 40) $status = $target->translate('other.connectionNice');

            elseif ($ping < 80) $status = $target->translate('other.connectionOk');

			elseif ($ping < 150) $status = $target->translate('other.connectionUnStable');

            elseif ($ping < 250) $status = $target->translate('other.connectionBad');

            return $target->translate('other.connectionStatus') . ': ' . $status . '§r.';
        }

    }

?>