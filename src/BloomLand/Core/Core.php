<?php


namespace BloomLand\Core;


use BloomLand\Core\controllers\{
    Commands,
    Entities,
    Listeners,
    Permissions,
    Tasks
};

use BloomLand\Core\provider\{
    SQLite3Provider,
    ProviderInterface
};

use BloomLand\Core\scoreboard\ScoreboardFactory;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\{
    Config,
    SingletonTrait
};

class Core extends PluginBase
{

    /**
     * @var string
     */
    private string $prefix = ' > ';

    /**
     * @return string
     */
    public function getPrefix() : string
    {
        return $this->prefix;
    }

    /**
     * @var ProviderInterface
     */
    private ProviderInterface $provider;

    /**
     * @return ProviderInterface
     */
    public function getProvider() : ProviderInterface
    {
        return $this->provider;
    }

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key) : mixed
    {
        $keys = explode('.', $key);
        if (count($keys) == 1 && $keys[0] == $key) {
            return $this->config->get($keys[0], 'Key ' . $key . ' not found.');
        } else {
            $data = [];
            foreach ($keys as $key) {
                if (empty($data)) {
                    $data = $this->config->get($key, 'Key ' . $key . ' not found.');
                } else {
                    $data = $data[$key];
                }
            }
        }
        return $data;
    }

    use SingletonTrait;

    protected function onLoad() : void
    {
        self::setInstance($this);
    }

    protected function onEnable() : void
    {
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML);

        $this->loadControllers();
        $this->loadConfig();
        $this->loadProvider();

        ScoreboardFactory::init();
    }

    private function loadControllers() : void
    {
        new Permissions();
        new Commands();
        new Listeners();
        new Tasks();
        new Entities();
        $this->getLogger()->info('Обработчики загружены.');
    }

    private function loadConfig() : void
    {
        if ($this->saveDefaultConfig()) {
            $this->getLogger()->info('Конфигурация по умолчанию загружен.');
        }
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML);
        $this->getLogger()->info('Конфиг сохранен.');
    }

    private function loadProvider() : void
    {
        $this->provider = new SQLite3Provider($this);
        $this->getLogger()->info('База данных: ' . $this->getProvider()->getName());
    }
}
