<?php


namespace BloomLand\Core\plugin;


	use BloomLand\Core\Core;

	use pocketmine\plugin\PluginBase;

	abstract class BloomLandPlugin extends PluginBase 
	{
		public function onEnable() : void
		{
			Core::getAPI()->getLogger()->info('§e' . $this->getDescription()->getName() . " загружается...");
		}
		
	}

?>
