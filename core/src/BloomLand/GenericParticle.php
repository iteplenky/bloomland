<?php

namespace BloomLand;

    use pocketmine\math\Vector3;
    use pocketmine\network\mcpe\protocol\LevelEventPacket;
    use pocketmine\world\particle\Particle;

    class GenericParticle implements Particle
    {

        /** @var int */
        protected $id;
        /** @var int */
        protected $data;

        /**
         * GenericParticle constructor.
         * @param int $id
         * @param int $data
         */

        public function __construct(int $id, int $data = 0) 
        {
            $this->id = $id & 0xFFF;
            $this->data = $data;
        }

        public function encode(Vector3 $pos) : array
        {
            $pk = new LevelEventPacket;
            $pk->evid = LevelEventPacket::EVENT_ADD_PARTICLE_MASK | $this->id;
            $pk->position = $pos;
            $pk->data = $this->data;
            return [$pk];
        }
    }

?>