<?php


declare(strict_types=1);


namespace BloomLand\Core\utils;

	use pocketmine\entity\Entity;
	use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
	use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
	use pocketmine\network\mcpe\protocol\SetScorePacket;
	use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
	use BloomLand\Core\lang\Language;
	use BloomLand\Core\BLPlayer;

	class Scoreboard{

		public const SORT_ASCENDING = 0;
		public const SORT_DESCENDING = 1;

		public const DISPLAY_SIDEBAR = 'sidebar'; // Ekranın sağında
		public const DISPLAY_PLAYER_LIST = 'list'; // Oyuncu listesinin altında
		public const DISPLAY_BELOW_NAME = 'belowname'; // İsminin altında

		public const CRITERIA_DUMMY = 'dummy';

		public const MAX_LINE = 15;

		/** @var string */
		private $objectiveName, $displayName;
		/** @var string */
		private $criteriaName = self::CRITERIA_DUMMY;
		/** @var string */
		private $displaySlot = self::DISPLAY_SIDEBAR;
		/** @var int */
		private $sortOrder = self::SORT_ASCENDING;
		/** @var int */
		private $scoreboardId;

		/** @var BLPlayer[] */
		private $player;
		/** @var bool */
		private $sended = false;
		/** @var ScorePacketEntry[] */
		private $lines = [];

		public function __construct(BLPlayer $player, string $displayName){
			$this->objectiveName = uniqid();
			$this->displayName = $displayName;
			$this->scoreboardId = mt_rand(1, 10000);
			$this->player = $player;
		}

		public function rename(string $name, bool $translate = false) : void{
			$this->displayName = $name;
			$this->remove();
			$this->display($translate);
		}

		public function display(bool $translate = false) : void{
			$displayName = $translate ? $this->player->translate($this->displayName) : $this->displayName;

			$pk = new SetDisplayObjectivePacket();
			$pk->displaySlot = $this->displaySlot;
			$pk->objectiveName = $this->objectiveName;
			$pk->displayName = $displayName;
			$pk->criteriaName = $this->criteriaName;
			$pk->sortOrder = $this->sortOrder;
			$this->player->getNetworkSession()->sendDataPacket($pk);

			if($this->displaySlot === self::DISPLAY_BELOW_NAME){
				$this->player->setScoreTag($displayName);
			}

			$this->sended = true;
			$this->sendAllLines($translate);
		}

		public function remove() : void{
			$pk = new RemoveObjectivePacket();
			$pk->objectiveName = $this->objectiveName;
			$this->player->getNetworkSession()->sendDataPacket($pk);

			$this->sended = false;
		}

		public function setLine(int $line, $messageOrEntity, bool $translate = false) : void{
			if($line < 1 || $line > self::MAX_LINE){
				throw new \InvalidArgumentException();
			}

			if(isset($this->lines[$line])){
				$entry = $this->lines[$line];
				$this->verify($entry, $messageOrEntity);
			}else{
				$entry = new ScorePacketEntry();
				$entry->objectiveName = $this->objectiveName;
				$entry->scoreboardId = $this->scoreboardId + $line;
				$entry->score = $line;
				$this->verify($entry, $messageOrEntity);
			}

			$this->lines[$line] = $entry;
			$this->sendLine($line, $translate);
		}

		private function verify(ScorePacketEntry $entry, $messageOrEntity) : void{
			if(is_string($messageOrEntity) or is_numeric($messageOrEntity)){
				$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
				$entry->customName = $messageOrEntity;
				$entry->entityUniqueId = null;
			}elseif($messageOrEntity instanceof Entity){
				$entry->type = $messageOrEntity instanceof BLPlayer ? ScorePacketEntry::TYPE_PLAYER : ScorePacketEntry::TYPE_ENTITY;
				$entry->entityUniqueId = $messageOrEntity->getId();
				$entry->customName = null;
			}else{
				throw new \InvalidArgumentException();
			}
		}

		public function removeLine(int $line) : void{
			if(isset($this->lines[$line]) and $this->sended){
				$pk = new SetScorePacket();
				$pk->type = SetScorePacket::TYPE_REMOVE;
				$pk->entries = [$this->lines[$line]];
				$this->player->getNetworkSession()->sendDataPacket($pk);
			}
		}

		public function sendLine(int $line, bool $translate = false) : void{
			if(!$this->sended){
				return;
			}

			$this->removeLine($line);

			$pk = new SetScorePacket();
			$pk->type = SetScorePacket::TYPE_CHANGE;
			$pk->entries = [];

			if($translate){
				$pk->entries[] = $this->translateLine($this->player, $this->lines[$line]);
			}else{
				$pk->entries[] = $this->lines[$line];
			}

			$this->player->getNetworkSession()->sendDataPackets([$pk]);
		}

		public function sendAllLines(bool $translate = false) : void{
			if(!$this->sended){
				return;
			}

			$pk = new SetScorePacket();
			$pk->type = SetScorePacket::TYPE_CHANGE;
			$pk->entries = [];

			if($translate){
				foreach($this->lines as $entry){
					$pk->entries[] = $this->translateLine($this->player, $entry);
				}
			}else{
				$pk->entries = $this->lines;
			}

			$this->player->getNetworkSession()->sendDataPackets([$pk]);
		}

		public function translateLine(BLPlayer $player, ScorePacketEntry $entry) : ScorePacketEntry{
			if($entry->type === ScorePacketEntry::TYPE_FAKE_PLAYER){
				$entry->customName = Language::translateOrExtended($player->getLanguage(), $entry->customName, [], '@');
			}

			return $entry;
		}
	}

?>