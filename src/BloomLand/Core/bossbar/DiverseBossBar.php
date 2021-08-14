<?php


namespace BloomLand\Core\bossbar;


use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeMap;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\player\Player;

class DiverseBossBar extends BossBar
{

    /**
     * @var array
     */
    private array $titles = [];

    /**
     * @var array
     */
    private array $subTitles = [];

    /**
     * @var array
     */
    private array $attributeMaps = [];

    /**
     * @param Player $player
     * @return BossBar
     */
    public function addPlayer(Player $player) : BossBar
	{
		$this->attributeMaps[$player->getId()] = clone parent::getAttributeMap();
		return parent::addPlayer($player);
	}

    /**
     * @param Player $player
     * @return BossBar
     */
    public function removePlayer(Player $player) : BossBar
	{
		unset($this->attributeMaps[$player->getId()]);
		return parent::removePlayer($player);
	}

    /**
     * @param Player $player
     * @return $this
     */
    public function resetFor(Player $player) : DiverseBossBar
	{
		unset ($this->attributeMaps[$player->getId()], $this->titles[$player->getId()], $this->subTitles[$player->getId()]);
		$this->sendAttributesPacket([$player]);
		$this->sendBossPacket([$player]);
		return $this;
	}

    /**
     * @return $this
     */
    public function resetForAll() : DiverseBossBar
	{
		foreach ($this->getPlayers() as $player) {
			$this->resetFor($player);
		}
		return $this;
	}

    /**
     * @param Player $player
     * @return string
     */
    #[Pure]
    public function getTitleFor(Player $player): string
	{
		return $this->titles[$player->getId()] ?? $this->getTitle();
	}

    /**
     * @param array $players
     * @param string $title
     * @return $this
     */
    public function setTitleFor(array $players, string $title = '') : DiverseBossBar
	{
		foreach ($players as $player) {
			$this->titles[$player->getId()] = $title;
			$this->sendBossTextPacket([$player]);
		}
		return $this;
	}

    /**
     * @param Player $player
     * @return string
     */
    #[Pure]
    public function getSubTitleFor(Player $player) : string
	{
		return $this->subTitles[$player->getId()] ?? $this->getSubTitle();
	}

    /**
     * @param array $players
     * @param string $subTitle
     * @return $this
     */
    public function setSubTitleFor(array $players, string $subTitle = '') : DiverseBossBar
	{
		foreach ($players as $player) {
			$this->subTitles[$player->getId()] = $subTitle;
			$this->sendBossTextPacket([$player]);
		}
		return $this;
	}

    /**
     * @param Player $player
     * @return string
     */
    public function getFullTitleFor(Player $player) : string
	{
		$text = $this->titles[$player->getId()] ?? '';
		if (!empty($this->subTitles[$player->getId()] ?? '')) {
			$text .= "\n\n" . $this->subTitles[$player->getId()] ?? '';
		}
		if (empty($text)) $text = $this->getFullTitle();
		return mb_convert_encoding($text, 'UTF-8');
	}

    /**
     * @param array $players
     * @param float $percentage
     * @return $this
     */
    public function setPercentageFor(array $players, float $percentage) : DiverseBossBar
	{
		$percentage = (float)min(1.0, max(0.00, $percentage));
		foreach ($players as $player) {
			$this->getAttributeMap($player)->get(Attribute::HEALTH)->setValue($percentage *
                $this->getAttributeMap($player)->get(Attribute::HEALTH)->getMaxValue(), true, true);
		}
		$this->sendAttributesPacket($players);
		$this->sendBossHealthPacket($players);

		return $this;
	}

    /**
     * @param Player $player
     * @return float
     */
    public function getPercentageFor(Player $player) : float
	{
		return $this->getAttributeMap($player)->get(Attribute::HEALTH)->getValue() / 100;
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
			$player->getNetworkSession()->sendDataPacket($this->addDefaults($player, $pk));
		}
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
			$player->getNetworkSession()->sendDataPacket($this->addDefaults($player, $pk));
		}
	}

    /**
     * @param array $players
     */
    protected function sendBossTextPacket(array $players) : void
	{
		$pk = new BossEventPacket();
		$pk->eventType = BossEventPacket::TYPE_TITLE;
		foreach ($players as $player) {
			if (!$player->isConnected()) {
                continue;
            }
			$pk->bossEid = $this->entityId ?? $player->getId();
			$pk->title = $this->getFullTitleFor($player);
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
		foreach ($players as $player) {
			if (!$player->isConnected()) {
                continue;
            }
			$pk->entries = $this->getAttributeMap($player)->needSend();
			$player->getNetworkSession()->sendDataPacket($pk);
		}
	}

    /**
     * @param array $players
     */
    protected function sendBossHealthPacket(array $players) : void
	{
		$pk = new BossEventPacket();
		$pk->eventType = BossEventPacket::TYPE_HEALTH_PERCENT;
		foreach ($players as $player) {
			if (!$player->isConnected()) {
                continue;
            }
			$pk->bossEid = $this->entityId ?? $player->getId();
			$pk->healthPercent = $this->getPercentageFor($player);
			$player->getNetworkSession()->sendDataPacket($pk);
		}
	}

    /**
     * @param Player $player
     * @param BossEventPacket $pk
     * @return BossEventPacket
     */
    private function addDefaults(Player $player, BossEventPacket $pk) : BossEventPacket
	{
		$pk->title = $this->getFullTitleFor($player);
		$pk->healthPercent = $this->getPercentageFor($player);
		$pk->unknownShort = 1;
		$pk->color = 0;
		$pk->overlay = 0;
		return $pk;
	}

    /**
     * @param Player|null $player
     * @return AttributeMap
     */
    public function getAttributeMap(Player $player = null) : AttributeMap
	{
		if ($player instanceof Player) {
			return $this->attributeMaps[$player->getId()] ?? parent::getAttributeMap();
		}
		return parent::getAttributeMap();
	}

    /**
     * @param Player|null $player
     * @return EntityMetadataCollection
     */
    public function getPropertyManager(Player $player = null) : EntityMetadataCollection
	{
		$propertyManager = /*clone*/ $this->propertyManager;
		if ($player instanceof Player) $propertyManager->setString(EntityMetadataProperties::NAMETAG, $this->getFullTitleFor($player));
		else $propertyManager->setString(EntityMetadataProperties::NAMETAG, $this->getFullTitle());
		return $propertyManager;
	}

    /**
     * @return string
     */
    public function __toString() : string
	{
		return __CLASS__ . " ID: $this->entityId, Titles: " . count($this->titles) . ", Subtitles: " .
            count($this->subTitles) . " [Defaults: " . parent::__toString() . "]";
	}
}