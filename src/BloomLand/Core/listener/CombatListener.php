<?php


namespace BloomLand\Core\listener;


use BloomLand\Core\Core;

use pocketmine\player\Player;
use pocketmine\event\Listener;

use pocketmine\event\entity\{
    ProjectileHitEntityEvent,
    ProjectileHitEvent,
    EntityDamageByEntityEvent,
    EntityDamageEvent,
    EntityTeleportEvent
};

use pocketmine\event\player\{
    PlayerCommandPreprocessEvent,
    PlayerDeathEvent,
    PlayerQuitEvent
};

use pocketmine\network\mcpe\protocol\PlaySoundPacket;

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
     * @var Core
     */
    private Core $plugin;

    /**
     * CombatListener constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     */
    public function handlePlayerCommand(PlayerCommandPreprocessEvent $event) : void
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
                $player->sendMessage('Вы не можете использовать команды во время сражения.');
                $event->cancel();
            }
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function handleEntityDamage(EntityDamageEvent $event) : void
    {
        $entity = $event->getEntity();

        if ($entity instanceof Player && $event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {

                if ($damager->isCreative() || $entity->isCreative()) {
                    $event->cancel();
                    return;
                }
                if ($damager->isFlying() || $damager->getAllowFlight() && $damager->isSurvival()) {
                    $event->cancel();
                    return;
                }
                if ($entity->isFlying() || $entity->getAllowFlight() && $entity->isSurvival()) {
                    $event->cancel();
                    return;
                }
                if ($event->getModifier(EntityDamageEvent::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN) < 0.0) {
                    $event->cancel();
                    return;
                }
                if (!$damager->isFighting()) {
                    $damager->sendMessage('Вы в режиме сражения. Если Вы покинете игру, то погибните.');
                    $damager->sendMessage('Ваш соперник играет с ' . $entity->getDevice() . '.');
                }
                if (!$entity->isFighting()) {
                    $entity->sendMessage('Вы в режиме сражения. Если Вы покинете игру, то погибните.');
                    $entity->sendMessage('Ваш соперник играет с §b' . $damager->getDevice() . '§r.');
                }

                $damager->setFighting();
                $entity->setFighting();

                $event->setKnockBack(0.415);
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
            $entity->sendMessage('Вас кто-то пытался телепортировать во время сражения.');
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

        if ($player->isFighting() && $player->isAlive()) {
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

                $pk->x = $entity->getLocation()->getX();
                $pk->y = $entity->getLocation()->getY();
                $pk->z = $entity->getLocation()->getZ();

                $pk->volume = 100;
                $pk->pitch = 1;

                $entity->getNetworkSession()->sendDataPacket($pk);
            }
        }
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return $this->plugin;
    }
}