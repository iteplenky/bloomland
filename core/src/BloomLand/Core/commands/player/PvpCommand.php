<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\entity\Location;
    use pocketmine\network\mcpe\protocol\OnScreenTextureAnimationPacket;

    class PvpCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('pvp', 'Переместиться на арену для сражений', '/pvp');
        }

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if ($player instanceof BLPlayer) {

                    $player->teleport(new Location(100, 100, 100, 0, 0, $player->getWorld()));
                    
                    $player->sendMessage($prefix . 'Вы переместились в место §bдля сражений§r.');

                    $pk = new OnScreenTextureAnimationPacket();
                    $pk->effectId = 5; 
                    $player->getNetworkSession()->sendDataPacket($pk);
                    
                }

            }

            return true;
        }

    }

?>
