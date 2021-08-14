<?php


namespace BloomLand\Core\inventory\session;

use Closure;

use BloomLand\Core\inventory\session\network\PlayerNetwork;

use pocketmine\player\Player;

final class PlayerSession
{

    /**
     * @var Player
     */
    protected Player $player;

    /**
     * @var PlayerNetwork
     */
    protected PlayerNetwork $network;

    /**
     * @var InvMenuInfo|null
     */
    protected ?InvMenuInfo $current = null;

    /**
     * PlayerSession constructor.
     * @param Player $player
     * @param PlayerNetwork $network
     */
    public function __construct(Player $player, PlayerNetwork $network)
    {
		$this->player = $player;
		$this->network = $network;
	}

	/**
	 * @internal
	 */
	public function finalize() : void
    {
		if($this->current !== null){
			$this->current->graphic->remove($this->player);
			$this->player->removeCurrentWindow();
		}
		$this->network->dropPending();
	}

    /**
     * @return InvMenuInfo|null
     */
    public function getCurrent() : ?InvMenuInfo
    {
		return $this->current;
	}

    /**
     * @param InvMenuInfo|null $current
     * @param Closure|null $callback
     */
    public function setCurrentMenu(?InvMenuInfo $current, ?Closure $callback = null) : void
    {
		$this->current = $current;

		if($this->current !== null){
			$this->network->waitUntil($this->network->getGraphicWaitDuration(), function(bool $success) use($callback) : void{
				if($this->current !== null){
					if($success && $this->current->graphic->sendInventory($this->player, $this->current->menu->getInventory())){
						if($callback !== null){
							$callback(true);
						}
						return;
					}

					$this->removeCurrentMenu();
					if($callback !== null){
						$callback(false);
					}
				}
			});
		}else{
			$this->network->wait($callback ?? static function(bool $success) : void{});
		}
	}

    /**
     * @return PlayerNetwork
     */
    public function getNetwork() : PlayerNetwork
    {
		return $this->network;
	}

    /**
     * @return bool
     */
    public function removeCurrentMenu() : bool
    {
		if($this->current !== null){
			$this->current->graphic->remove($this->player);
			$this->setCurrentMenu(null);
			return true;
		}
		return false;
	}
}
