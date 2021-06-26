<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\math\Vector3;
    use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;

    class ConfettiCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('cf', 'Создать партикл конфетти', '/cf', ['confetti']);
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

            
                $pk = new SpawnParticleEffectPacket();

                $vector = $player->getLocation()->asVector3()->add(0.5, mt_rand(1.5, 4.5), 0.5);

                $pk->position = $vector;
                $pk->particleName = 'bloomland:confetti';
                Core::getAPI()->getServer()->broadcastPackets(Core::getAPI()->getServer()->getOnlinePlayers(), [$pk]);

            
                $player->sendMessage(Core::getAPI()->getPrefix() . 'Хлоп! Над Вами §bконфетти§r.');
                
            }

            return true;
        }

    }

?>