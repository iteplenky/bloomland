<?php


namespace BloomLand\Core\bossbar;


use pocketmine\plugin\Plugin;

class API
{

    /**
     * @param Plugin $plugin
     */
    public static function load(Plugin $plugin)
	{
		PacketListener::register($plugin);
	}
}