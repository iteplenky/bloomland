<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\math\Vector3;
    use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;

    class KingCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('king', 'Создать партикл короны', '/king', ['crown']);
        }
        
        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $pk = new SpawnParticleEffectPacket();

                $vector = $player->getLocation()->asVector3()->add(0.5, mt_rand(1.5, 4.5), 0.5);

                $pk->position = $vector;
                $pk->particleName = 'bloomland:crown';
                
                $this->getPlugin()->getServer()->broadcastPackets($player->getViewers(), [$pk]);

                $player->>getNetworkSession()->sendDataPacket($pk);
                    
                $player->sendMessage($this->getPlugin()->getPrefix() . '§eКоролевский§r партикл! Вы создали §bпартикл§r с коронами.');
                
            }

            return true;
        }

    }

?>
