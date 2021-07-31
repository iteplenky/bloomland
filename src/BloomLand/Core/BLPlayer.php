<?php


namespace BloomLand\Core;


    use JetBrains\PhpStorm\Pure;
    use pocketmine\player\Player;

    class BLPlayer extends Player
    {

        /**
         * @return string
         */
        #[Pure] public function getLowerCaseName() : string
        {
            return strtolower($this->getName());
        }

        /**
         * @return bool
         */
        public function isOp() : bool
        {
            return $this->getServer()->isOp($this->getName());
        }
    }