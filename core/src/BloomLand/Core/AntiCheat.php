<?php


namespace BloomLand\Core;


    use pocketmine\player\Player;
    use pocketmine\event\Listener;
    use pocketmine\event\player\PlayerQuitEvent;

    use pocketmine\entity\effect\Effect;
    use pocketmine\entity\effect\VanillaEffects;

    use pocketmine\event\block\BlockBreakEvent;
    use pocketmine\event\player\PlayerInteractEvent;

    class AntiCheat implements Listener
    {
        public const ANTI_INSTA_BREAK = 0;

        private static $disableAC = [];
        private static $breakTimes = [];
        private static $warnCount = [];

        public function onInteract(PlayerInteractEvent $event): void
        {
            $player = $event->getPlayer();
            $item = $event->getItem();

            if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                
                // self::updateBreakTime($player);

            }

        }

        public function onBlockBreak(BlockBreakEvent $event): void 
        { 
            // self::checkInstaBreak($event); 
        }

        public function onQuit(PlayerQuitEvent $event): void
        {
            $player = $event->getPlayer();

            self::removeFromselfs($player);
            $event->setQuitMessage('');
        }

        public static function checkInstaBreak(BlockBreakEvent $event): void 
        {
            if (!self::isDisabled(self::ANTI_INSTA_BREAK) and !$event->getInstaBreak()) {

                $player = $event->getPlayer();
                $id = self::hash($player);

                if (isset(self::$breakTimes[$id])) {

                    $expectedTime = ceil($event->getBlock()->getBreakInfo()->getBreakTime($event->getItem()) * 20);
                    $expectedTime *= 1 - (0.2 * self::getEffectLevel($player, VanillaEffects::HASTE()));
                    $expectedTime *= 1 - (0.2 * self::getEffectLevel($player, VanillaEffects::MINING_FATIGUE()));
                    $expectedTime -= 1; 
                    $actualTime = ceil(microtime(true) * 20) - self::$breakTimes[$id];

                    if($actualTime < $expectedTime) {

                        $event->cancel();

                        $player->sendTitle('§l§cпомедленнее', 'вы ломаете слишком быстро', 5, 5, 5);

                        if (!isset(self::$warnCount[$id]))
                            self::$warnCount[$id] = 0;
                        
                        self::$warnCount[$id]++;

                        if (self::$warnCount[$id] > 10)
                            $player->kick(' > §c* §fНа сервере нельзя пользоваться читами §c*');
                    
                        } else
                        unset(self::$breakTimes[$id]);
                
                    } else {
                    $event->cancel();
                    $player->sendMessage(' §b§lЗАЩИТА §r> Вы добавлены в список §bвозможных§r нарушителей.');
                }

            }
            
        }

        private static function hash(BLPlayer $player): int 
        { 
            return $player->getId(); 
        }

        public static function removeFromAntiCheats(BLPlayer $player): void 
        { 
            unset(self::$breakTimes[self::hash($player)]); 
        }

        public static function updateBreakTime(BLPlayer $player): void 
        {
            if (!self::isDisabled(self::ANTI_INSTA_BREAK) and $player->isOnline())
                self::$breakTimes[self::hash($player)] = floor(microtime(true) * 20);
        }

        private static function getEffectLevel(BLPlayer $player, Effect $effect) : int 
        {
            $effect = $player->getEffects()->get($effect);
            return ($effect !== null) ? $effect->getEffectLevel() : 0;
        }

        public static function disableAntiCheat(int $id): void {
            self::$disableAC[$id] = true;
        }

        public static function enableAntiCheat(int $id): void {
            unset(self::$disableAC[$id]);
        }

        public static function isDisabled(int $id): bool {
            return isset(self::$disableAC[$id]);
        }
    }

?>