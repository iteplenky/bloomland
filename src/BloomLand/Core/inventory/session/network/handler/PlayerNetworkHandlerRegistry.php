<?php


namespace BloomLand\Core\inventory\session\network\handler;


use Closure;

use BloomLand\Core\inventory\session\network\NetworkStackLatencyEntry;

use pocketmine\network\mcpe\protocol\types\DeviceOS;

final class PlayerNetworkHandlerRegistry
{

    /**
     * @var PlayerNetworkHandler
     */
    private PlayerNetworkHandler $default;

    /**
     * @var array
     */
    private array $game_os_handlers = [];

    /**
     * PlayerNetworkHandlerRegistry constructor.
     */
    public function __construct()
    {
		$this->registerDefault(new ClosurePlayerNetworkHandler(static function(Closure $then) : NetworkStackLatencyEntry {
			return new NetworkStackLatencyEntry(mt_rand() * 1000, $then);
        }));
        $this->register(DeviceOS::PLAYSTATION, new ClosurePlayerNetworkHandler(static function(Closure $then) : NetworkStackLatencyEntry {
			$timestamp = mt_rand();
			return new NetworkStackLatencyEntry($timestamp, $then, $timestamp * 1000);
		}));
	}

    /**
     * @param PlayerNetworkHandler $handler
     */
    public function registerDefault(PlayerNetworkHandler $handler) : void
    {
		$this->default = $handler;
	}

    /**
     * @param int $os_id
     * @param PlayerNetworkHandler $handler
     */
    public function register(int $os_id, PlayerNetworkHandler $handler) : void
    {
		$this->game_os_handlers[$os_id] = $handler;
	}

    /**
     * @param int $os_id
     * @return PlayerNetworkHandler
     */
    public function get(int $os_id) : PlayerNetworkHandler
    {
		return $this->game_os_handlers[$os_id] ?? $this->default;
	}
}