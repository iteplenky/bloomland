<?php


namespace BloomLand\Core\utils;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\math\Vector3;
    use pocketmine\entity\Location;
    use pocketmine\world\Position;

    use pocketmine\network\mcpe\protocol\PlaySoundPacket;
    use pocketmine\network\mcpe\protocol\AnimateEntityPacket;
    use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;

    use pocketmine\entity\effect\EffectInstance;
    use pocketmine\entity\effect\VanillaEffects;

    use InvalidArgumentException;

    class API
    {
        public static function sendDialog(BLPlayer $player, string $person, string $prefix, string $text) : void 
        {
            
            $player->sendMessage(' ');
            $player->sendMessage(' ');
            
            $player->sendMessage('              ' . $prefix);
            $player->sendMessage(' ' . $person);
            $player->sendMessage('             ' . $text);
            
            $player->sendMessage(' ');
            $player->sendMessage(' ');
        }

        public static function sendErrorPackets(BLPlayer $player) : void 
        {
            self::playSoundPacket($player, 'sfx.error');

            $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 5, 4, false));
            $player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 5, 4, false));
        } 

        public static function playSoundPacket(BLPlayer $player, string $soundName, int $volume = 100, int $pitch = 1) : void 
        {
            $pk = new PlaySoundPacket();
            $pk->soundName = $soundName;

            $pk->x = $player->getLocation()->x;
            $pk->y = $player->getLocation()->y;
            $pk->z = $player->getLocation()->z;
            
            $pk->volume = $volume;
            $pk->pitch = $pitch;

            $player->getNetworkSession()->sendDataPacket($pk);
        }

        public static function getTimeFormat(int $duration) : string 
        {
            $hours = (int) ($duration / 60 / 60);

            $minutes = (int) ($duration / 60) - $hours * 60;
            
            $seconds = (int) $duration - $hours * 60 * 60 - $minutes * 60;
            
            return $time = ($hours == 0 ? '00':$hours) . ':' . ($minutes == 0 ? '00':($minutes < 10? '0'.$minutes:$minutes)) . ':' . ($seconds == 0 ? '00':($seconds < 10? '0'.$seconds:$seconds));
        }

        public static function sendParticlePacket($entity, $vector, $particleName) : void 
        {
            $pk = new SpawnParticleEffectPacket();
            
            $pk->position = $vector;
            $pk->particleName = $particleName;

            foreach ($entity->getViewers() as $player) {

                $player->getNetworkSession()->sendDataPacket($pk);

            }

        }

        /**
         * @return string
         */
        public static function getCountry(string $ip): string
        {
            $query = @unserialize(file_get_contents("http://ip-api.com/php/". $ip));

            if ($query["status"] === "success") {

                $cc = strtolower($query["countryCode"]);

                if (in_array($cc, array("en","us"))) {

                    return "en";

                } else if (in_array($cc, array("fr", "be", "lu", "ca"))) {

                    return "fr";

                } else if (in_array($cc, array("es", "br", "me"))) {

                    return "es";

                } else if (in_array($cc, array("de"))) {

                    return "de";
                    
                }
            }
            return "en";
        }

        /**
         * @param string $ip
         * @return string
         */
        public static function getProxy(string $ip): string
        {
            $resp = file_get_contents('http://proxycheck.io/v2/'.$ip . '?key=111111-222222-333333-444444&vpn=1', FILE_TEXT);
            $details = json_decode($resp);

            if (!isset($details->$ip->proxy)) return "false";

            if ($details->$ip->proxy === "no") {

            return "false";

            } else {

                return "true";

            }

        }

        /**
         * @return int
         */
        public static function getMicroTime(): int
        {
            $mt = explode(' ', microtime()) ;
            $mt = ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
            return $mt;
        }

        public static function getSurroundingArea(Vector3 $pos1, Vector3 $pos2, int $radius): bool
        {
            $result = false;

            $minX = self::getMinMax($pos1->getX() - $radius ,$pos1->getX() + $radius)[0];
            $maxX = self::getMinMax($pos1->getX() - $radius ,$pos1->getX() + $radius)[1];

            $minY = self::getMinMax($pos1->getY() - $radius ,$pos1->getY() + $radius)[0];
            $maxY = self::getMinMax($pos1->getY() - $radius ,$pos1->getY() + $radius)[1];

            $minZ = self::getMinMax($pos1->getZ() - $radius ,$pos1->getZ() + $radius)[0];
            $maxZ = self::getMinMax($pos1->getZ() - $radius ,$pos1->getZ() + $radius)[1];
            
            $x = $pos2->getX();
            $y = $pos2->getY();
            $z = $pos2->getZ();

            if ($x > $minX and $x < $maxX) {

                if ($y > $minY and $y < $maxY) {

                    if ($z > $minZ and $z < $maxZ) {

                        $result = true;

                    }

                }

            }

            return $result;
        }

        /**
         * @param $x1
         * @param $x2
         * @return array
         */
        public static function getMinMax($x1, $x2): array
        {
            if ($x1 < $x2) {
                return array($x1, $x2);
            } else {
                return array($x2, $x1);
            }
        }

        public static function getTimeFormat2(int $sec): string
        {
            $day = floor($sec / 86400);

            $hours = $sec % 86400;

            $hour = floor($hours / 3600);

            $minutes = $hours % 3600;

            $minute = floor($minutes / 60);

            $remainings = $minutes % 60;

            $second = ceil($remainings);

            return $day . " Day " . $hour . " Hours " . $minute . " Minutes " . $second . " Second";
        }

        public static function unpackRawLocation(string $rawLocation): Position 
        {
            $rawLocationArray = json_decode($rawLocation, true, 512, JSON_THROW_ON_ERROR);

            $x = isset($rawLocationArray["x"]) ? (float)$rawLocationArray["x"] : null;
            if (is_null($x)) 
                throw new InvalidArgumentException(" > Не указана позиция координаты X");
            
            $y = isset($rawLocationArray["y"]) ? (float)$rawLocationArray["y"] : null;
            if (is_null($y)) 
                throw new InvalidArgumentException(" > Не указана позиция координаты Y");
            
            $z = isset($rawLocationArray["z"]) ? (float)$rawLocationArray["z"] : null;
            if (is_null($z)) 
                throw new InvalidArgumentException(" > Не указана позиция координаты Z");
            
            $worldName = isset($rawLocationArray["world"]) ? $rawLocationArray["world"] : null;
            if (is_null($worldName)) {
                $worldName = Core::getAPI()->getServer()->getDefaultLevel()->getFolderName();
                Core::getAPI()->getLogger()->notice(" > Вы не указали мир, поэтому был установлен §cworld");
            }

            if (!Core::getAPI()->getServer()->getWorldManager()->isWorldLoaded($worldName)) 
                Core::getAPI()->getServer()->getWorldManager()->loadWorld($worldName);
            
            $world = Core::getAPI()->getServer()->getWorldManager()->getWorldByName($worldName);
            if (is_null($world)) 
                throw new InvalidArgumentException(" > Мир не существует");

            return new Location($x, $y, $z, 0, 0, $world);
        }

        public static function packPositionToRaw(Position $location): string 
        {
            $rawLocationArray =
                [
                    'x' => $location->getX(),
                    'y' => $location->getY(),
                    'z' => $location->getZ(),
                    'world' => $location->getWorld()->getFolderName(),
                ];

            return json_encode($rawLocationArray, JSON_THROW_ON_ERROR);
        }

        public static function unpackRawVector(string $rawVector): Vector3 
        {
            $rawVectorArray = json_decode($rawVector, true, 512, JSON_THROW_ON_ERROR);

            $x = isset($rawVectorArray["x"]) ? (float)$rawVectorArray["x"] : null;
            if (is_null($x)) 
                throw new InvalidArgumentException(" > Не указана позиция координаты X");
            
            $y = isset($rawVectorArray["y"]) ? (float)$rawVectorArray["y"] : null;
            if (is_null($y)) 
                throw new InvalidArgumentException(" > Не указана позиция координаты Y");
            
            $z = isset($rawVectorArray["z"]) ? (float)$rawVectorArray["z"] : null;
            if (is_null($z)) 
                throw new InvalidArgumentException(" > Не указана позиция координаты Z");

            return new Vector3($x, $y, $z);
        }

        public static function packVectorToRaw(Vector3 $vector): string 
        {
            $rawVectorArray =
                [
                    'x' => $vector->getX(),
                    'y' => $vector->getY(),
                    'z' => $vector->getZ()
                ];

            return json_encode($rawVectorArray, JSON_THROW_ON_ERROR);
        }

        public static function colorCount(string $message): int 
        {
            $colors = "abcdef0123456789lo";
            $count = 0;
            for ($i = 0; $i < strlen($colors); $i++) 
                $count += substr_count($message, "§" . $colors[$i]);
            
            return $count;
        }
    }

?>