<?php


namespace BloomLand\Core\commands\settings;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
    use pocketmine\network\mcpe\protocol\types\BoolGameRule;

    class XYZCommand extends Command
    {

        public function __construct()
        {
            parent::__construct('xyz', 'Управление координатами', '/xyz');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    $pk = new GameRulesChangedPacket();
                    $pk->gameRules = ["showcoordinates" => new BoolGameRule(true, true)];
                    $player->getNetworkSession()->sendDataPacket($pk);

                }
            }
            return true;
        }
    }

?>