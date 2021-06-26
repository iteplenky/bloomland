<?php


namespace BloomLand\Core\task;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\scheduler\Task;

    use pocketmine\item\{
        Armor, VanillaItems
    };

    use pocketmine\Server;
    use pocketmine\color\Color;
    
    class RainbowTask extends Task
    {
        private $h = 0.0;

        public function onRun() : void
        {
            $this->hsl2rgb($this->h, 100.0, 50.0, $r, $g, $b);
         
            $this->h = ($this->h + 1) % 360;

            foreach (Server::getInstance()->getOnlinePlayers() as $key => $player) {
         
                foreach ($player->getArmorInventory()->getContents(true) as $slot => $item) {
         
                    if (in_array($item->getId(), [VanillaItems::LEATHER_CAP()->getId(), VanillaItems::LEATHER_TUNIC()->getId(), VanillaItems::LEATHER_PANTS()->getId(), VanillaItems::LEATHER_BOOTS()->getId()])) {
         
                        $item->setCustomColor(new Color($r, $g, $b));
                    
                    }

                }

            }
            
        }

        public function hsl2rgb(float $h, float $s, float $l, ?int &$r, ?int &$g, ?int &$b) : void
        {
            if ($s == 0) $r = $g = $b = $l * 255;
            
            else {

                $temp2 = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
                $temp1 = 2 * $l - $temp2;
    
                $h /= 360;
                $rgb = [($h + 1 / 3) % 1, $h, ($h + 2 / 3) % 1];
                
                for ($i = 0; $i < 3; ++$i) {

                    $rgb[$i] = $rgb[$i] < 1 / 6 ? $temp1 + ($temp2 - $temp1) * 6 * $rgb[$i] : $rgb[$i];
                
                }
                
                $r = (int) $rgb[0];
                $g = (int) $rgb[1];
                $b = (int) $rgb[2];
            }
            
        }

    }

?>
