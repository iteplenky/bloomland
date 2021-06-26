<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\sqlite3\SQLite3;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    class PayCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('pay', 'Передать сумму от баланса', '/pay');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    if (isset($args[1])) {

                        $target = array_shift($args);
		                $amount = array_shift($args);

                        if (is_numeric($amount) and $amount > 0 and $amount % 1 == 0 and !is_float($amount)) {
                            
                            if (($target = Core::getAPI()->getServer()->getPlayerByPrefix($target)) instanceof BLPlayer) {

                                if ($target->getLowerCaseName() == $player->getLowerCaseName()) {

                                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы пытаетесь передать монеты §bсамому себе§r.');
                                    return true;
                                
                                }

                                $balance = $player->getMoney();

                                if ($balance >= $amount) {

                                    $target->addMoney($amount);
                                    $player->removeMoney($amount); 

                                    $player->sendMessage(Core::getPrefix() . $player->translate('coins.pay.success', [$target->getName(), $amount]));

                                    $player->sendTitle($player->translate('coins.pay.player.title'), $player->translate('coins.pay.player.subtitle'), 10, 15, 5);

                                    if ($target instanceof BLPlayer) {

                                        $target->sendMessage(Core::getPrefix() . $player->translate('coins.pay.success.target', [$player->getName(), $amount]));
                                        $target->sendTitle($player->translate('coins.pay.target.title'), $player->translate('coins.pay.target.subtitle'), 10, 15, 5);

                                    }

                                } else $player->sendMessage(Core::getPrefix() . $player->translate('coins.nosuch.amount'));
                            
                            } else $player->sendMessage(Core::getPrefix() . $player->translate('coins.pay.failed.target'));

                        } else $player->sendMessage(Core::getPrefix() . $player->translate('coins.pay.failed'));

                    } else $player->sendMessage(Core::getPrefix() . $player->translate('coins.usage'));

                }

            }
            return true;
        }
    }

?>