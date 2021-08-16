<?php


namespace BloomLand\Core\entity\custom;


use BloomLand\Core\entity\NPCBase;

use pocketmine\color\Color;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;

use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;

use pocketmine\world\particle\InstantEnchantParticle;

class Booster extends NPCBase
{

    /**
     * @var int
     */
    private int $particleData = 0;

    /**
     * @var int
     */
    private int $particleSleep = 3;

    /**
     * Booster constructor.
     * @param Location $location
     * @param CompoundTag|null $nbt
     */
    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $this->bakeGeometrySkin(), $nbt);
        $this->setScale(1.1);
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick) : bool
    {
        $x = $this->getLocation()->getFloorX();
        $y = $this->getLocation()->getFloorY();
        $z = $this->getLocation()->getFloorZ();

        if (time() >= $this->getParticleData()) {
            $this->setParticleData(time() + $this->getParticleSleep());

            for ($i = 0; $i < 5; $i++) {
                $pk = new SpawnParticleEffectPacket();

                $vector = new Vector3($x + mt_rand(-2.5, 2.5), $y + mt_rand(0, 2.5), $z + mt_rand(-2.5, 2.5));
                $pk->position = $vector;
                $pk->particleName = 'minecraft:dragon_breath_trail';

                $this->getPlugin()->getServer()->broadcastPackets($this->getViewers(), [$pk]);
            }
        } else {
            $vector = new Vector3($x + mt_rand(-1.5, 1.5), $y + mt_rand(0, 1.5), $z + mt_rand(-1.5, 1.5));
            $particle = new InstantEnchantParticle(new Color(255, 255, 153));

            $this->getLocation()->getWorld()->addParticle($vector, $particle);
        }
        return parent::onUpdate($currentTick);
    }

    /**
     * @param Player $player
     */
    public function onCollideWithPlayer(Player $player) : void
    {
        $player->sendMessage('oops');
    }

    public function onTouch(Player $player) : void
    {
        $player->sendMessage('Hello!');
    }

    /**
     * @return string
     */
    public function getGeometryId() : string
    {
        return $this->geometryId;
    }

    /**
     * @return string
     */
    public function getGeometryName() : string
    {
        return $this->geometryName;
    }

    /**
     * @return string
     */
    public function getPngName() : string
    {
        return $this->pngName;
    }

    /**
     * @var string
     */
    private string $geometryId = 'sword';
    
    /**
     * @var string
     */
    private string $geometryName = 'sword';
    
    /**
     * @var string
     */
    private string $pngName = 'sword_compact';

    /**
     * @return int
     */
    public function getParticleData() : int
    {
        return $this->particleData;
    }

    /**
     * @param int $particleData
     */
    public function setParticleData(int $particleData) : void
    {
        $this->particleData = $particleData;
    }

    /**
     * @return int
     */
    public function getParticleSleep() : int
    {
        return $this->particleSleep;
    }
}