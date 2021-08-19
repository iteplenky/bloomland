<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;

use pocketmine\player\Player;

class NightVisionCommand extends BaseCommand
{

    /**
     * NightVisionCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('nv', 'Управление ночным зрением.', ['nightvision']);
        $this->setPermission('core.command.nv');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $effect = VanillaEffects::NIGHT_VISION();

        if ($player->getEffects()->has($effect) && $player->getEffects()->get($effect)->getDuration() > 10000) {
            $player->getEffects()->get($effect)->setDuration(0);
            $player->sendMessage('§cВы сняли с себя ночное зрение.');
            return;
        }

        $player->getEffects()->add(new EffectInstance($effect, 999999, 1, false, true));
        $player->sendMessage('§aВы наложили эффект ночного зрения.');
    }
}