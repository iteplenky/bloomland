<?php


namespace BloomLand\Core\entity;


use BloomLand\Core\Core;
use JetBrains\PhpStorm\Pure;

use pocketmine\entity\{Entity, EntitySizeInfo, Human, Location, Skin};

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

class NPCBase extends Human
{

    private const NPC_SKIN_FOLDER = 'geometry' . DIRECTORY_SEPARATOR;

    /**
     * @var string
     */
    private string $pngName = '';

    /**
     * @var string
     */
    private string $geometryName = '';

    /**
     * @var string
     */
    private string $geometryId = '';

    /**
     * NPCBase constructor.
     * @param Location $location
     * @param Skin $skin
     * @param CompoundTag|null $nbt
     */
    public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $skin, $nbt);
    }

    public function getSkinImageFromString(string $string) : string
    {
        $img = imagecreatefromstring($string);
        [$width, $height] = getimagesizefromstring($string);
        $bytes = '';

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                $argb = imagecolorat($img, $x, $y);
                $bytes .= chr(($argb >> 16) & 0xff) . chr(($argb >> 8) & 0xff) .
                    chr($argb & 0xff) . chr(((~($argb >> 24)) << 1) & 0xff);
            }
        }

        imagedestroy($img);
        return $bytes;
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getSkinImageFromResources(string $filename) : string
    {
        /** @var resource $png */
        $png = $this->getPlugin()->getResource($filename);
        /** @var string $pngContent */
        $pngContent = stream_get_contents($png);

        fclose($png);

        return $this->getSkinImageFromString($pngContent);
    }

    /**
     * @return Skin
     */
    public function bakeGeometrySkin() : Skin
    {
        $skinImage = $this->getSkinImageFromResources(self::NPC_SKIN_FOLDER . $this->getPngName() . '.png');
        $geometry = $this->getPlugin()->getResource(self::NPC_SKIN_FOLDER . $this->getGeometryName() . '.geo.json');
        $geometryData = stream_get_contents($geometry);

        fclose($geometry);

        return new Skin($this->getName(), $skinImage, '', 'geometry.' . $this->getGeometryId(), $geometryData);
    }

    public function onTouch(Player $player) : void
    {
        // Clear interact with entity.
    }

    public function attack(EntityDamageEvent $source) : void
    {
        if ($source instanceof EntityDamageByEntityEvent) {
            $player = $source->getDamager();
            if ($player instanceof Player) {
                $this->onTouch($player);
            }
        }
        $source->cancel();
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
     * @return bool
     */
    public function canSaveWithChunk() : bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function canBeCollidedWith() : bool
    {
        return false;
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function canCollideWith(Entity $entity) : bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function canBeMovedByCurrents() : bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function canBreathe() : bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return "NPC#" . spl_object_id($this);
    }

    /**
     * @return EntitySizeInfo
     */
    #[Pure]
    protected function getInitialSizeInfo() : EntitySizeInfo
    {
        return new EntitySizeInfo(1.8, 0.6, 1.62);
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return Core::getInstance();
    }
}