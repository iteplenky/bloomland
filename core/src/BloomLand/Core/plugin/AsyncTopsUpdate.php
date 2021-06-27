<?php


namespace BloomLand\Core\plugin;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\scheduler\AsyncTask;

    use pocketmine\Server;

    class AsyncTopsUpdate extends AsyncTask 
    {
        private const MAX = 5;

        private $folder;
        private $id;
        private $needle;

        public function __construct(string $folder, int $id, int $needle = 5) 
        {
            $this->folder = $folder;
            $this->id = $id;
            $this->needle = $needle;
        }

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        /**
         * @param string $folder
         *
         * @return \SQLite3
         */
        private static function defineDatabase(string $folder) : \SQLite3 
        {
            return new \SQLite3($folder. 'users.db');
        }

        public function onRun() : void 
        {
            $db = self::defineDatabase($this->folder);
            $prepare = $db->query('SELECT * FROM data ORDER BY coins DESC limit ' . $this->needle);

            $keys = [];
            $i = 0;

            while ($item = $prepare->fetchArray(SQLITE3_ASSOC)) {
                $keys[$i++] = $item;
            }

            $this->setResult($keys);
        }

        public function onCompletion() : void 
        {
            $player = $this->getPlugin()->getServer()->getWorldManager()->findEntity($this->id);

            if ($player instanceof BLPlayer) {

                $result = $this->getResult();
                $msg = $this->getPlugin()->getPrefix() . $player->translate('rich.players') . PHP_EOL; 

                for ($i = ($this->needle - self::MAX); $i < $this->needle; $i++) {

                    if (isset($result[$i])) {

                        if (!is_numeric($result[$i]['coins'])) {

                            $result[$i]['coins'] = 0;

                        }

                        $msg .=  ' §r#§a' . $a = $i + 1 . ' §r'. $result[$i]['username'] .' §7> §b'. number_format($result[$i]['coins'], 0, '', ' ') . ' ' . $player->translate('monetary.unit');

                    }

                    else 
                        $msg .= ' §r#§a' . $a = $i + 1 . ' ' . $player->translate('rich.players.failed');

                    $msg .= PHP_EOL;
                }
                $player->sendMessage($msg);
            }
            
        }
        
    }

?>
