<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use pocketmine\command\utils\InvalidCommandSyntaxException;
    use pocketmine\entity\Location;
    use pocketmine\lang\TranslationContainer;
    use pocketmine\utils\AssumptionFailedError;

    class TeleportCommand extends Command 
    {
        public const MAX_COORD = 30000000;
        public const MIN_COORD = -30000000;

        public function __construct()
        {
            parent::__construct('tp', 'Быстрое перемещение в пространстве', '/tp');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                switch (count($args)) {
                    
                    case 1: // /tp targetPlayer
                        $targetArgs = $args;
                        $subject = $player;

                        $targetPlayer = $this->findPlayer($player, $targetArgs[0]);
                        if ($targetPlayer === null) {
                            return true;
                        }

                        Core::getAPI()->getServer()->broadcastMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $player->getName() . ' §rпереносится к §e' . $targetPlayer->getName() . '§r.');
                        break;

                    case 3: // /tp x y z
                    case 5: // /tp x y z yaw pitch - TODO: 5 args could be target x y z yaw :(
                        Core::getAPI()->getServer()->broadcastMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $player->getName() . ' §rпереносится по координатам на X: §c' . $args[0] . '§r Y: §e' . $args[1] . ' §rZ: §a' . $args[2]);
                        $subject = $player;
                        $targetArgs = $args;
                        break;
                    case 2: // /tp player1 player2
                        $targetArgs = $args;
                        $subject = $player;
                        $targetPlayer = $this->findPlayer($player, $targetArgs[0]);
                        if ($targetPlayer === null) {
                            return true;
                        }
                        $targetPlayer1 = $this->findPlayer($player, $targetArgs[1]);
                        if ($targetPlayer1 === null) {
                            return true;
                        }
                        Core::getAPI()->getServer()->broadcastMessage(Core::getAPI()->getPrefix() . 'Игрок §e' . $player->getName() . ' §rпереносит §b' . 
                        $player->getServer()->getPlayerByPrefix($args[0])->getName() . ' §rк §b' . $player->getServer()->getPlayerByPrefix($args[1])->getName() . '§r.');
                        break;
                    case 4: // /tp player1 x y z - TODO: 4 args could be x y z yaw :(
                    case 6: // /tp player1 x y z yaw pitch
                        $subject = $this->findPlayer($player, $args[0]);
                        if ($subject === null) {
                            return true;
                        }
                        $targetArgs = $args;
                        array_shift($targetArgs);

                        Core::getAPI()->getServer()->broadcastMessage(Core::getAPI()->getPrefix() . 'Игрок §e' . $player->getName() . ' §rпереносит §b' . 
                        $player->getServer()->getPlayerByPrefix($args[0])->getName() . ' §rпо координатам §rX: §c' . $args[1] . '§r Y: §e' . $args[2] . ' §rZ: §a' . $args[3]);
                        break;

                    default:
                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Менеджер перемещений: /tp <§bигрок§r> <§3игрок§r:§cx§r> <§ey§r> <§az§r>');
                    return true;
                    break;
                }

                switch(count($targetArgs)) {
                    case 1:
                        $targetPlayer = $this->findPlayer($player, $targetArgs[0]);
                        if($targetPlayer === null){
                            return true;
                        }
        
                        $subject->teleport($targetPlayer->getLocation());
                        // Core::getAPI()->getServer()->broadcastMessage(' Игрок ' . $player->getName() . ' перенес ' . $targetPlayer->getName() . ' ');
                        // Command::broadcastCommandMessage($player, new TranslationContainer("commands.tp.success", [$subject->getName(), $targetPlayer->getName()]));
        
                        return true;
                    case 2:
                        $player->getServer()->getPlayerByPrefix($args[0])->teleport($player->getServer()->getPlayerByPrefix($args[1])->getLocation());
                        return true;
                    case 3:
                    case 5:
                        $base = $subject->getLocation();
                        if(count($targetArgs) === 5){
                            $yaw = (float) $targetArgs[3];
                            $pitch = (float) $targetArgs[4];
                        }else{
                            $yaw = $base->yaw;
                            $pitch = $base->pitch;
                        }
        
                        $x = $this->getRelativeDouble($base->x, $player, $targetArgs[0]);
                        $y = $this->getRelativeDouble($base->y, $player, $targetArgs[1], 0, 256);
                        $z = $this->getRelativeDouble($base->z, $player, $targetArgs[2]);
                        $targetLocation = new Location($x, $y, $z, $yaw, $pitch, $base->getWorld());
        
                        $subject->teleport($targetLocation);
                        return true;
                }

            } 
            return true;
        }
        
        private function findPlayer(CommandSender $player, string $playerName) : ?BLPlayer
        {
            $subject = $player->getServer()->getPlayerByPrefix($playerName);
            if ($subject === null) {
                $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок сейчас §cне в игре§r.');
                return null;
            }
            return $subject;
        }

        protected function getRelativeDouble(float $original, CommandSender $sender, string $input, float $min = self::MIN_COORD, float $max = self::MAX_COORD) : float{
            if ($input[0] === "~") {

                $value = $this->getDouble($sender, substr($input, 1));
    
                return $original + $value;
            }
            return $this->getDouble($sender, $input, $min, $max);
        }

        protected function getDouble(CommandSender $sender, $value, float $min = self::MIN_COORD, float $max = self::MAX_COORD) : float{
            $i = (double) $value;
    
            if ($i < $min) {
                $i = $min;
            } elseif ($i > $max) {
                $i = $max;
            }
    
            return $i;
        }

    }

?>