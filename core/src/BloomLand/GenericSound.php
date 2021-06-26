<?php


namespace BloomLand;


	use pocketmine\math\Vector3;
	use pocketmine\world\sound\Sound;

	use pocketmine\network\mcpe\protocol\ClientboundPacket;
	use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

	class GenericSound implements Sound
	{
		/** @var int */
		public $id;

		public function __construct(int $id)
		{
			$this->id = $id;
		}

		/**
		 * @param Vector3|null $pos
		 *
		 * @return ClientboundPacket|ClientboundPacket[]
		 */
		public function encode(?Vector3 $pos) : array
		{
			return [LevelSoundEventPacket::create($this->id, $pos)];
		}
	}

?>
