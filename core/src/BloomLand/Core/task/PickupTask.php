<?php


namespace BloomLand\Core\task;

        
    use pocketmine\item\Item;
    use pocketmine\math\Vector3;
    use pocketmine\entity\Entity;
    use pocketmine\world\Position;

    use pocketmine\network\mcpe\convert\TypeConverter;
    use pocketmine\network\mcpe\protocol\AddItemActorPacket;
    use pocketmine\network\mcpe\protocol\RemoveActorPacket;
    use pocketmine\network\mcpe\protocol\TakeItemActorPacket;
    use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
    
    use pocketmine\Server;
    use pocketmine\player\Player;
    
    use pocketmine\scheduler\Task;

    class PickupTask extends Task
    {
        private Player $player;
        private Item $item;
        private Position $pos;
        private int $entityRuntimeId;

        /** @var Player[] */
        private array $hasSpawned = [];

        public function __construct(Player $player, Item $item, Position $pos)
        {
            $this->player = $player;
            $this->item = $item;
            $this->pos = $pos;
            $this->entityRuntimeId = Entity::nextRuntimeId();

            $pk = new AddItemActorPacket();
            
            $pk->entityRuntimeId = $this->entityRuntimeId;
            
            $pk->item = ItemStackWrapper::legacy(TypeConverter::getInstance()->coreItemStackToNet($item));;
            
            $pk->position = $pos;
            
            $pk->motion = new Vector3(lcg_value() * 0.2 - 0.1, 0.2, lcg_value() * 0.2 - 0.1);
            
            $chunkX = $pos->getFloorX() >> 4;
            $chunkZ = $pos->getFloorZ() >> 4;
            
            foreach ($pos->getWorld()->getChunkPlayers($chunkX, $chunkZ) as $viewer) {
                
                if (!$viewer->hasReceivedChunk($chunkX, $chunkZ)) continue;

                $this->hasSpawned[spl_object_hash($viewer)] = $viewer;
                $viewer->getNetworkSession()->sendDataPacket($pk);
            
            }
        }

        public function onRun() : void
        {
            if(
                $this->player->isClosed() ||
                !$this->player->isConnected() ||
                !empty($this->player->getInventory()->addItem($this->item))
            ){
                $this->pos->getWorld()->dropItem($this->pos, $this->item);
            }
    
            Server::getInstance()->broadcastPackets($this->hasSpawned, [
                TakeItemActorPacket::create($this->player->getId(), $this->entityRuntimeId),
                RemoveActorPacket::create($this->entityRuntimeId)
            ]);
        }

    }

?>
