<?php


namespace BloomLand\Core\task;


use BloomLand\Core\Core;

use pocketmine\scheduler\Task;

class ReloadingTask extends Task
{

    /**
     * @var Core
     */
    private Core $plugin;

    /**
     * @var int
     */
    private int $minutesLeft = 10;

    /**
     * ReloadingTask constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getScheduler()->scheduleDelayedRepeatingTask($this, 20 * 60, 20 * 60 * $this->minutesLeft);
    }

    public function onRun() : void
    {
        $this->minutesLeft--;
        if ($this->minutesLeft == 0) {
            $this->getHandler()->cancel();
            $this->getPlugin()->getScheduler()->scheduleRepeatingTask(new class ($this->getPlugin()) extends Task {

                /**
                 * @var Core
                 */
                private Core $plugin;

                /**
                 * @var int
                 */
                private int $secondsLeft = 60;

                /**
                 *  constructor.
                 * @param Core $plugin
                 */
                public function __construct(Core $plugin)
                {
                    $this->plugin = $plugin;
                }

                public function onRun() : void
                {
                    $this->secondsLeft--;

                    $server = $this->getPlugin()->getServer();

                    if ($this->secondsLeft == 5) {

                        foreach ($server->getOnlinePlayers() as $player) {
                            $player->save();
                        }

                        foreach ($server->getWorldManager()->getWorlds() as $world) {
                            $world->save(true);
                        }
                    }

                    $server->broadcastPopup('§fДо перезагрузки §b' . $this->secondsLeft . '§f секунд.');

                    if ($this->secondsLeft <= 0) {

                        $this->getHandler()->cancel();

                        foreach ($server->getOnlinePlayers() as $player) {
                            $player->setFighting(false);
                            $player->kick(
                                '§l| §rСервер перезагрузился, можете заходить!' . PHP_EOL .
                                '§l| §rСообщество: §bvk.com/bl_pe'
                            );
                        }
                        $server->shutdown();
                    }
                }

                /**
                 * @return Core
                 */
                public function getPlugin() : Core
                {
                    return $this->plugin;
                }
            }, 20);
        }
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return $this->plugin;
    }

    /**
     * @param int $minutesLeft
     */
    public function setMinutesLeft(int $minutesLeft) : void
    {
        $this->minutesLeft = $minutesLeft;
    }
}