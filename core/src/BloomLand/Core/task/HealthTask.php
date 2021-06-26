<?php


namespace BloomLand\Core\task;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use BloomLand\Core\utils\API;
    use pocketmine\entity\effect\VanillaEffects;
    use pocketmine\entity\effect\EffectInstance;

    use pocketmine\scheduler\Task;

    use _64FF00\PurePerms\PPGroup;

    class HealthTask extends Task
    {
        public function __construct()
        {
            // $this->player = $player;
        }

        public function onRun() : void
        {
            foreach (Core::getAPI()->getServer()->getOnlinePlayers() as $player) {
                
                $ping = $player->getNetworkSession()->getPing();
                $status = '';

                if ($ping < 60) $status = '';

                elseif ($ping < 140) $status = '';

                elseif ($ping < 250) $status = '';

                elseif ($ping < 400) $status = '';

                if ($player->isCreative()) 

                    $mode = 'В §bТворческом §fрежиме';

                else {

                    if ($player->getAbsorption() > 0) 
                        $mode = round($player->getHealth()) . ' ' . round($player->getAbsorption()) . '';

                    else 
                        $mode = round($player->getHealth()) . '';

                }

                if (($chat = Core::getAPI()->getServer()->getPluginManager()->getPlugin('PureChat')) != null) {

                    
                    $player->setNameTag($status . $chat->getNametag($player) . PHP_EOL . $mode . 'keks');

                    // $chat->setNameTag($status . $player .PHP_EOL . $mode);

                    
                } else {

                    $player->setNameTag('...');

                }

            }
            
        }

    }

?>
