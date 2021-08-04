<?php


namespace BloomLand\Core\listener;


use BloomLand\Core\Core;

use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;

use pocketmine\event\player\PlayerCommandPreprocessEvent;

use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\EnderPearl;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

class CombatListener implements Listener
{

    protected const WHITELISTED = [
        '/gamemode',
        '/tp',
        '/ban',
        '/kick',
        '/spawn'
    ];

    /**
     * @var Core|null
     */
    private ?Core $plugin;

    /**
     * @var array
     */
    private array $cooldownEnchantedApple = [];

    /**
     * @var array
     */
    private array $cooldownGoldenApple = [];

    /**
     * CombatListener constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getServer()->getPluginManager()->registerEvents( $this, $this->getPlugin() );
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     */
    public function handlePlayerCommand(PlayerCommandPreprocessEvent $event):void
    {
        $player = $event->getPlayer();

        if ($player instanceof Player) {
            if (strpos($event->getMessage(), '/') != 0) {
                return;
            }

            if (in_array(explode(' ', $event->getMessage())[0], self::WHITELISTED)) {
                return;
            }

            if ($player->isFighting()) {
                $player->sendMessage('Вы не можете §bиспользовать§r команды во время сражения.');
                $event->cancel();
            }
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function handleEntityDamage(EntityDamageEvent $event) : void
    {
        if ($event->isCancelled()) {
            return;
        }

        $entity = $event->getEntity();

        if ($entity instanceof Player && $event instanceof EntityDamageByEntityEvent) {

            $damager = $event->getDamager();

            if (!$damager instanceof Player) {
                return;
            }

            if ($damager->getUniqueId()->toString() == $entity->getUniqueId()->toString()) {
                return;
            }

            if ($damager->isCreative() || $entity->isCreative()) {
                $event->cancel();
                return;
            }

            if ($entity->isFlying() || $entity->getAllowFlight() && $entity->isSurvival()) {
                $event->cancel();
                return;
            }

            if ($damager->isFlying() || $damager->getAllowFlight() && $damager->isSurvival()) {
                $event->cancel();
                return;
            }

            if ($event->getModifier(EntityDamageEvent::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN) < 0.0) {
                $event->cancel();
            }

            if (!$entity->isFighting()) {
                $entity->sendMessage('Вы в режиме §bсражения§r. Если Вы покинете игру, то§b погибните§r.');
                $entity->sendMessage('Ваш соперник играет с §b' . $damager->getDevice() . '§r.');
            }

            if (!$damager->isFighting()) {
                $damager->sendMessage('Вы в режиме §bсражения§r. Если Вы покинете игру, то§b погибните§r.');
                $damager->sendMessage('Ваш соперник играет с §b' . $entity->getDevice() . '§r.');
            }

            $damager->setFighting();
            $entity->setFighting();

            $event->setKnockBack(0.415);
        }
    }

    /**
     * @param PlayerItemConsumeEvent $event
     */
    public function handlePlayerItem(PlayerItemConsumeEvent $event) {
        $player = $event->getPlayer();

        if ($player->isFighting()) {

            $item = $event->getItem();

            if ($item->equals(VanillaItems::ENCHANTED_GOLDEN_APPLE(), false, false)) {

                $cooldownEnchantedApple = $this->cooldownEnchantedApple[$player->getUniqueId()->toString()];

                if (isset($cooldownEnchantedApple)) {
                    if ((time() - $cooldownEnchantedApple) < 40) {
                        $time = 40 - (time() - $cooldownEnchantedApple);
                        $player->sendMessage('§bПомедленнее§r! До следующего раза осталось: §b' . $time . ' §rсекунд.');
                        $event->cancel();
                    } else {
                        $this->cooldownEnchantedApple[$player->getUniqueId()->toString()] = time();
                    }
                } else {
                    $this->cooldownEnchantedApple[$player->getUniqueId()->toString()] = time();
                }
            } elseif ($item->equals(VanillaItems::GOLDEN_APPLE(), false, false)) {

                $cooldownGoldenApple = $this->cooldownGoldenApple[$player->getUniqueId()->toString()];

                if (isset($cooldownGoldenApple)) {
                    if ((time() - $cooldownGoldenApple) < 20) {
                        $time = 20 - (time() - $cooldownGoldenApple);
                        $player->sendMessage('§bПомедленнее§r! До следующего раза осталось: §b' . $time . ' §rсекунд.');
                        $event->cancel();
                    } else {
                        $this->cooldownGoldenApple[$player->getUniqueId()->toString()] = time();
                    }
                } else {
                    $this->cooldownGoldenApple[$player->getUniqueId()->toString()] = time();
                }
            }
        }
    }

    /**
     * @param EntityTeleportEvent $event
     */
    public function handleEntityTeleport(EntityTeleportEvent $event) : void
    {
        $entity = $event->getEntity();

        if ($entity instanceof Player && $entity->isFighting()) {
            $entity->sendMessage('Вас кто-то §bпытался §rтелепортировать во время сражения.');
            $event->cancel();
        }
    }

    /**
     * @param ProjectileLaunchEvent $event
     */
    public function handleProjectile(ProjectileLaunchEvent $event) : void
    {
        $player = $event->getEntity()->getOwningEntity();

        if ($event->getEntity() instanceof EnderPearl && $player->isFighting()) {
            $player->sendMessage('§bОпаньки§r! Вы не можете убегать со сражения.');
            $event->cancel();
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function handlePlayerDeath(PlayerDeathEvent $event) : void
    {
        $event->setDeathMessage('');
        $event->getPlayer()->setFighting(false);
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function handlePlayerQuit(PlayerQuitEvent $event) : void
    {
        $player = $event->getPlayer();

        if ($player->isFighting() && $player->isAlive() && $player->isConnected()) {
            $player->setHealth(0);
        }
    }

    /**
     * @param ProjectileHitEvent $event
     */
    public function handleProjectileHit(ProjectileHitEvent $event) : void
    {
        $projectile = $event->getEntity();
        $entity = $projectile->getOwningEntity();

        if ($entity instanceof Player && $event instanceof ProjectileHitEntityEvent) {

            $target = $event->getEntityHit();

            if ($target instanceof Player && $target->isSurvival() && $entity->isSurvival()) {
                $pk = new PlaySoundPacket();
                $pk->soundName = 'random.orb';

                $pk->x = $entity->getLocation()->x;
                $pk->y = $entity->getLocation()->y;
                $pk->z = $entity->getLocation()->z;

                $pk->volume = 100;
                $pk->pitch = 1;

                $entity->getNetworkSession()->sendDataPacket($pk);
            }
        }
    }

    /**
     * @return Core|null
     */
    public function getPlugin() : ?Core
    {
        return $this->plugin;
    }
}