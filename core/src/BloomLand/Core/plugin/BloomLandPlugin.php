<?php

declare(strict_types=1);


namespace BloomLand\Core\plugin;


	use BloomLand\Core\Core;

	use pocketmine\plugin\PluginBase;

	abstract class BloomLandPlugin extends PluginBase 
	{
		public const TABLE_PLAYERS = 'players';
		public const TABLE_SKYBLOCK_MISSIONS = 'skyblockMissions';
		public const TABLE_SKYBLOCK_PLAYERS = 'skyblockPlayers';
		public const TABLE_CRIMINAL_RECORDS = 'criminals';
		public const TABLE_PROMOTIONS = 'promotions';

		public function onEnable() : void
		{
			Core::getAPI()->getLogger()->info('§e' . $this->getDescription()->getName() . " загружается...");
		}

		public function getResourcesDir() : string
		{
			return $this->getFile() . 'resources' . DIRECTORY_SEPARATOR;
		}
	}

?>