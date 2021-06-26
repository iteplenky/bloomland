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

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $pk = new SpawnParticleEffectPacket();

                $vector = $player->getLocation()->asVector3()->add(0.5, mt_rand(1.5, 4.5), 0.5);

                $pk->position = $vector;
                $pk->particleName = 'bloomland:crown';
                Core::getAPI()->getServer()->broadcastPackets(Core::getAPI()->getServer()->getOnlinePlayers(), [$pk]);


                $player->sendMessage(Core::getAPI()->getPrefix() . '§eКоролевский§r партикл! Вы создали §bпартикл§r с коронами.');
                
            }

            return true;
        }

    }

?>