<?php


namespace BloomLand\Core;


use BloomLand\Core\controllers\Commands;
use BloomLand\Core\controllers\Entities;
use BloomLand\Core\controllers\Listeners;
use BloomLand\Core\controllers\Permissions;
use BloomLand\Core\controllers\Tasks;

use BloomLand\Core\provider\SQLite3Provider;
use BloomLand\Core\provider\ProviderInterface;
use BloomLand\Scoreboard\ScoreboardFactory;
use pocketmine\plugin\PluginBase;

use pocketmine\utils\SingletonTrait;

class Core extends PluginBase
{

    use SingletonTrait;

    /**
     * @var string
     */
    private string $prefix = ' > ';

    /**
     * @var ProviderInterface
     */
    private ProviderInterface $provider;

    protected function onLoad() : void
    {
        self::setInstance($this);
    }

    protected function onEnable() : void
    {
        new Permissions();

        $this->loadControllers();
        $this->loadProvider();

        ScoreboardFactory::init();
    }

    private function loadControllers() : void
    {
        $pluginManager = $this->getServer()->getPluginManager();

        $pluginManager->registerEvents(new Commands(), $this);
        $pluginManager->registerEvents(new Listeners(), $this);
        $pluginManager->registerEvents(new Tasks(), $this);
        $pluginManager->registerEvents(new Entities(), $this);
    }

    private function loadProvider() : void
    {
        $this->provider = new SQLite3Provider($this);
    }

    /**
     * @return string
     */
    public function getPrefix() : string
    {
        return $this->prefix;
    }

    /**
     * @return ProviderInterface
     */
    public function getProvider() : ProviderInterface
    {
        return $this->provider;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key) : mixed
    {
        return $this->getConfig()->get($key, 'Key ' . $key . ' not found.');
    }
}
