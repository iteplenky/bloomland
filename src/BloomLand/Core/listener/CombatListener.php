<?php


namespace BloomLand\Core\listener;


use BloomLand\Core\Core;

use pocketmine\event\Listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;

use pocketmine\event\player\PlayerCommandPreprocessEvent;

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

    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getServer()->getPluginManager()->registerEvents( $this, $this->getPlugin() );
    }

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
        }
    }

    public function handleEntityTeleport(EntityTeleportEvent $event) : void
    {
        $entity = $event->getEntity();

        if ($entity instanceof Player && $entity->isFighting()) {
            $entity->sendMessage('Вас кто-то §bпытался §rтелепортировать во время сражения.');
            $event->cancel();
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