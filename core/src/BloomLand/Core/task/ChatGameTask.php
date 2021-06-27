<?php


namespace BloomLand\Core\task;


    use BloomLand\Core\Core;
    use BloomLand\Core\utils\API;

    use pocketmine\scheduler\Task;

    class ChatGameTask extends Task
    {
        public function onRun(): void
        {
        $random = mt_rand(0, 1);

            if ($random == 0) {

                Core::getAPI()->chatGame = mt_rand(1, 10);

                foreach (Core::getAPI()->getServer()->getOnlinePlayers() as $player) {

                    $player->sendMessage(Core::getAPI()->getPrefix() . '§b§lЧАТ ИГРА!§r Угадайте случайное число от §e1 §rдо §e10§r и получите за это монеты.');

                }
                
            } else {

                Core::getAPI()->chatGame = mt_rand(1, 2);
                
                foreach (Core::getAPI()->getServer()->getOnlinePlayers() as $player) {

                    $player->sendMessage(Core::getAPI()->getPrefix() . '§a§lЧАТ ИГРА!§r Выберите между §e1 §r- §e2§r и за правильный выбор получите монеты.');

                }
                
            }
            foreach (Core::getAPI()->getServer()->getOnlinePlayers() as $player) {

                if (Core::getAPI()->getServer()->isOp($player->getName())) {

                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Ответ в этой игре: §b' . Core::getAPI()->chatGame . '§r.');
                    
                }

            }

        }

    }

?>
