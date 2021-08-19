<?php


namespace BloomLand\Core\controllers;


use BloomLand\Core\Core;

use BloomLand\Core\entity\NPCBase;

use pocketmine\data\bedrock\EntityLegacyIds;

use pocketmine\entity\EntityFactory;
use pocketmine\entity\EntityDataHelper;

use pocketmine\world\World;
use pocketmine\nbt\tag\CompoundTag;

class Entities
{

    private Core $plugin;

    /**
     * Entities constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->loadEntities();
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return $this->plugin;
    }

    public function loadEntities() : void
    {
        EntityFactory::getInstance()->register(NPCBase::class, function (World $world, CompoundTag $nbt) : NPCBase {
            return new NPCBase(EntityDataHelper::parseLocation($nbt, $world), null);
        }, ['NPCBase', 'npc:base'], EntityLegacyIds::NPC);
    }
}