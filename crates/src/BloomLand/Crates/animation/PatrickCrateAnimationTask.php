<?php 


namespace BloomLand\Crates\animation;

    
    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use BloomLand\Crates\crate\PatrickCrate;

    use BloomLand\Core\utils\API;

    use pocketmine\scheduler\Task;

    use pocketmine\math\Vector3;
    use pocketmine\utils\TextFormat;

    use pocketmine\network\mcpe\protocol\AnimateEntityPacket;

    class PatrickCrateAnimationTask extends Task
    {
        private $entity;
        private $player;

        private $time = 50;

        public function __construct(PatrickCrate $entity, BLPlayer $player)
        {
            $this->entity = $entity;
            $this->player = $player;
            Core::getAPI()->getScheduler()->scheduleRepeatingTask($this, 20 / 4);
        }

        private function getPlayer() : BLPlayer
        {
            return $this->player;
        }

        private function getCrate() : PatrickCrate
        {
            return $this->entity;
        }

        private function crateAnimate() : void
        {
            $pk = AnimateEntityPacket::create('animation.patrick_chest.new', '', '', '', 0, [$this->getCrate()->getId()]);

            Core::getAPI()->getServer()->broadcastPackets($this->getCrate()->getViewers(), [$pk]);
        }

        public function onRun() : void
        {
            $this->time--;

            if (!$this->getCrate()->isAlive()) $this->time = 0;

            $player = $this->getPlayer();

            switch ($this->time) {

                case 49:
                    $this->crateAnimate();
                    break;
            
                case 35:
                    API::playSoundPacket($this->getPlayer(), 'sfx.crates.crate_open');
                    break;

                case 32:
                    API::playSoundPacket($this->getPlayer(), 'sfx.crates.easter.beginning_song');
                    break;

                case 14:
                    if ($player->isOnline()) {

                        $player->sendTitle('§l' . $this->result, '§lваш приз', 2, 30, 10);

                    }

                    $location = $this->getCrate()->getLocation();

                    $vector = new Vector3($location->x , $location->y + 0.2, $location->z);
    
                    API::sendParticlePacket($this->getCrate(), $vector, 'bloomland:moneycrate_money');
                    
                    $player->addMoney(str_replace(' ', '', (TextFormat::clean($this->result))));
                                        
                    Core::getAPI()->getServer()->broadcastMessage(Core::getAPI()->getPrefix() . 'Игроку §b' . $player->getName() .
                    ' §rиз §7<§eСундук с монетами§7> §rвыпало §l' . $this->result . '§r монет!');
                    break;
            
                case 8:
                    if ($player->isOnline()) {

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы открыли сундук и получили §bсвой приз§r. Следующий раз когда можно будет открыть повторно: через§b 30 секунд§r.');
                        
                        API::playSoundPacket($this->getPlayer(), 'sfx.crates.crate_close');
                    }
                    break;

                case 0:
                    unset(PatrickCrate::$animation[array_search($this->getCrate()->getId(), PatrickCrate::$animation)]);
                    $this->getHandler()->cancel();
                    break;

                default:
                    if ($this->time > 18 and $this->time < 32) {

                        if ($player->isOnline()) {
                            
                            $list = [
                                '§a10 000',
                                '§e30 000',
                                '§680 000',
                                '§c0',
                                '§b1 000 000',
                                '§a10 000',
                            ];
    
                            $rand_keys = array_rand($list, 2);
    
                            $this->result = $list[$rand_keys[0]];;
                            
                            $player->sendTitle('§l' . $this->result, '', 5, 5, 5);
                        }
    
                    }
                    break;
                    
            }

        }
        
    }

?>
