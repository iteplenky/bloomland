<?php

namespace BloomLand\Core\bossbar;

use GlobalLogger;
use InvalidArgumentException;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeFactory;
use pocketmine\entity\AttributeMap;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class BossBar
{

    /**
     * @var array
     */
    private array $players = [];

    /**
     * @var string
     */
    private string $title = "";

    /**
     * @var string
     */
    private string $subTitle = "";

    /**
     * @var null
     */
    public $entityId = null;

    /**
     * @var AttributeMap
     */
    private AttributeMap $attributeMap;

    /**
     * @var EntityMetadataCollection
     */
    protected EntityMetadataCollection $propertyManager;

    /**
     * BossBar constructor.
     */
    public function __construct()
	{
		$this->attributeMap = new AttributeMap();
		$this->getAttributeMap()->add(AttributeFactory::getInstance()->mustGet(Attribute::HEALTH)->setMaxValue(100.0)->setMinValue(0.0)->setDefaultValue(100.0));
		$this->propertyManager = new EntityMetadataCollection();
		$this->propertyManager->setLong(EntityMetadataProperties::FLAGS, 0
			^ 1 << EntityMetadataFlags::SILENT
			^ 1 << EntityMetadataFlags::INVISIBLE
			^ 1 << EntityMetadataFlags::NO_AI
			^ 1 << EntityMetadataFlags::FIRE_IMMUNE);
		$this->propertyManager->setShort(EntityMetadataProperties::MAX_AIR, 400);
		$this->propertyManager->setString(EntityMetadataProperties::NAMETAG, $this->getFullTitle());
		$this->propertyManager->setLong(EntityMetadataProperties::LEAD_HOLDER_EID, -1);
		$this->propertyManager->setFloat(EntityMetadataProperties::SCALE, 0);
		$this->propertyManager->setFloat(EntityMetadataProperties::BOUNDING_BOX_WIDTH, 0.0);
		$this->propertyManager->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 0.0);
	}

    /**
     * @return array
     */
    public function getPlayers() : array
	{
		return $this->players;
	}

    /**
     * @param array $players
     * @return $this
     */
    public function addPlayers(array $players) : BossBar
	{
		foreach ($players as $player) {
			$this->addPlayer($player);
		}
		return $this;
	}

	/**
	 * @param Player $player
	 * @return BossBar
	 */
	public function addPlayer(Player $player) : BossBar
	{
		if (isset($this->players[$player->getId()])) {
            return $this;
        }
		$this->sendBossPacket([$player]);
		$this->players[$player->getId()] = $player;
		return $this;
	}

    /**
     * @param Player $player
     * @return $this
     */
    public function removePlayer(Player $player) : BossBar
	{
		if (!isset($this->players[$player->getId()])) {
			GlobalLogger::get()->debug("Removed player that was not added to the boss bar (" . $this . ")");
			return $this;
		}
		$this->sendRemoveBossPacket([$player]);
		unset($this->players[$player->getId()]);
		return $this;
	}

    /**
     * @param array $players
     * @return $this
     */
    public function removePlayers(array $players) : BossBar
	{
		foreach ($players as $player) {
			$this->removePlayer($player);
		}
		return $this;
	}

    /**
     * @return $this
     */
    public function removeAllPlayers() : BossBar
	{
		foreach ($this->getPlayers() as $player) {
            $this->removePlayer($player);
        }
		return $this;
	}

    /**
     * @return string
     */
    public function getTitle() : string
	{
		return $this->title;
	}

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title = '') : BossBar
	{
		$this->title = $title;

		$this->sendBossTextPacket($this->getPlayers());
		return $this;
	}

    /**
     * @return string
     */
    public function getSubTitle() : string
	{
		return $this->subTitle;
	}

	public function setSubTitle(string $subTitle = '') : BossBar
	{
		$this->subTitle = $subTitle;

		$this->sendBossTextPacket($this->getPlayers());
		return $this;
	}

	public function getFullTitle() : string
	{
		$text = $this->title;
		if (!empty($this->subTitle)) {
			$text .= "\n\n" . $this->subTitle;
		}
		return mb_convert_encoding($text, 'UTF-8');
	}

    /**
     * @param float $percentage
     * @return $this
     */
    public function setPercentage(float $percentage) : BossBar
	{
		$percentage = (float) min(1.0, max(0.0, $percentage));
		$this->getAttributeMap()->get(Attribute::HEALTH)->setValue($percentage * $this->getAttributeMap()->get(Attribute::HEALTH)->getMaxValue(), true, true);
		$this->sendBossHealthPacket($this->getPlayers());

		return $this;
	}

    /**
     * @return float
     */
    public function getPercentage() : float
	{
		return $this->getAttributeMap()->get(Attribute::HEALTH)->getValue() / 100;
	}

    /**
     * @param array $players
     */
    public function hideFrom(array $players): void
	{
        $pk = new BossEventPacket();
        $pk->bossEid = $this->entityId;
        $pk->eventType = BossEventPacket::TYPE_HIDE;
        $this->broadcastPacket($players, $pk);
	}

    public function hideFromAll() : void
	{
		$this->hideFrom($this->getPlayers());
	}

    /**
     * @param array $players
     */
    public function showTo(array $players) : void
	{
		$pk = new BossEventPacket();
		$pk->eventType = BossEventPacket::TYPE_SHOW;
		foreach ($players as $player) {
			if (!$player->isConnected()) {
                continue;
            }
			$pk->bossEid = $this->entityId ?? $player->getId();
			$player->getNetworkSession()->sendDataPacket($this->addDefaults($pk));
		}
	}

	public function showToAll() : void
	{
		$this->showTo($this->getPlayers());
	}

    /**
     * @return Entity|null
     */
    public function getEntity() : ?Entity
	{
        return $this->entityId === null ? null : Server::getInstance()->getWorldManager()->findEntity($this->entityId);
    }

    /**
     * @param Entity|null $entity
     * @return $this
     */
    public function setEntity(?Entity $entity = null) : BossBar
	{
		if ($entity instanceof Entity && ($entity->isClosed() || $entity->isFlaggedForDespawn())) {
            throw new InvalidArgumentException( "Entity $entity can not be used since its not valid anymore (closed or flagged for despawn)" );
        }
		if ($this->getEntity() instanceof Entity && !$entity instanceof Player) {
            $this->getEntity()->flagForDespawn();
        }
		else {
			$pk = new RemoveActorPacket();
			$pk->entityUniqueId = $this->entityId;
			Server::getInstance()->broadcastPackets($this->getPlayers(), [$pk]);
		}
		if ($entity instanceof Entity) {
			$this->entityId = $entity->getId();
			$this->attributeMap = $entity->getAttributeMap();
			$this->getAttributeMap()->add($entity->getAttributeMap()->get(Attribute::HEALTH));
			$this->propertyManager = $entity->getNetworkProperties();
			if (!$entity instanceof Player) {
                $entity->despawnFromAll();
            }
		} else {
			$this->entityId = Entity::nextRuntimeId();
		}
		$this->sendBossPacket($this->getPlayers());
		return $this;
	}

    /**
     * @param bool $removeEntity
     * @return $this
     */
    public function resetEntity(bool $removeEntity = false) : BossBar
	{
		if ($removeEntity && $this->getEntity() instanceof Entity && !$this->getEntity() instanceof Player) {
            $this->getEntity()->close();
        }
		return $this->setEntity();
	}

    /**
     * @param array $players
     */
    protected function sendBossPacket(array $players) : void
	{
		$pk = new BossEventPacket();
		$pk->eventType = BossEventPacket::TYPE_SHOW;
		foreach ($players as $player) {
			if (!$player->isConnected()) {
                continue;
            }
			$pk->bossEid = $this->entityId ?? $player->getId();
			$player->getNetworkSession()->sendDataPacket($this->addDefaults($pk));
		}
	}

    /**
     * @param array $players
     */
    protected function sendRemoveBossPacket(array $players) : void
	{
		$pk = new BossEventPacket();
		$pk->bossEid = $this->entityId;
		$pk->eventType = BossEventPacket::TYPE_HIDE;
		$this->broadcastPacket($players, $pk);
	}

    /**
     * @param array $players
     */
    protected function sendBossTextPacket(array $players) : void
	{
		$pk = new BossEventPacket();
		$pk->eventType = BossEventPacket::TYPE_TITLE;
		$pk->title = $this->getFullTitle();
		foreach ($players as $player) {
			if (!$player->isConnected()) {
                continue;
            }
			$pk->bossEid = $this->entityId ?? $player->getId();
			$player->getNetworkSession()->sendDataPacket($pk);
		}
	}

    /**
     * @param array $players
     */
    protected function sendAttributesPacket(array $players) : void
	{
		if ($this->entityId === null) {
            return;
        }
		$pk = new UpdateAttributesPacket();
		$pk->entityRuntimeId = $this->entityId;
		$pk->entries = $this->getAttributeMap()->needSend();
		Server::getInstance()->broadcastPackets($players, [$pk]);
	}

    /**
     * @param array $players
     */
    protected function sendBossHealthPacket(array $players) : void
	{
		$pk = new BossEventPacket();
		$pk->eventType = BossEventPacket::TYPE_HEALTH_PERCENT;
		$pk->healthPercent = $this->getPercentage();
		foreach ($players as $player) {
			if (!$player->isConnected()) {
                continue;
            }
			$pk->bossEid = $this->entityId ?? $player->getId();
			$player->getNetworkSession()->sendDataPacket($pk);
		}
	}

    /**
     * @param BossEventPacket $pk
     * @return BossEventPacket
     */
    private function addDefaults(BossEventPacket $pk) : BossEventPacket
	{
		$pk->title = $this->getFullTitle();
		$pk->healthPercent = $this->getPercentage();
		$pk->unknownShort = 1;
		$pk->color = 0;
		$pk->overlay = 0;
		return $pk;
	}


    /**
     * @return string
     */
    public function __toString() : string
	{
		return __CLASS__ . " ID: $this->entityId, Players: " . count($this->players) .
            ", Title: \"$this->title\", Subtitle: \"$this->subTitle\", Percentage: \"" . $this->getPercentage() . "\"";
	}

    /**
     * @return AttributeMap
     */
    public function getAttributeMap() : AttributeMap
	{
		return $this->attributeMap;
	}

    /**
     * @return EntityMetadataCollection
     */
    protected function getPropertyManager(): EntityMetadataCollection
	{
		return $this->propertyManager;
	}

    /**
     * @param array $players
     * @param BossEventPacket $pk
     */
    private function broadcastPacket(array $players, BossEventPacket $pk) : void
	{
		foreach ($players as $player) {
			if (!$player->isConnected()) {
                continue;
            }
			$pk->bossEid = $player->getId();
			$player->getNetworkSession()->sendDataPacket($pk);
		}
	}
}