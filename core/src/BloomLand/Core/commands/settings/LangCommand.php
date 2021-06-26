<?php


namespace BloomLand\Core\commands\settings;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class LangCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('lang', 'Сменить игровой язык', '/lang <ru|en>');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            $args = array_map('strtolower', $args);

            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    if (!empty($args[0])) {

                        if (in_array($args[0], array('en', 'english'))) {

                            $player->setLanguage('en_US', true);
                            $player->sendMessage(Core::getPrefix() . $player->translate('language.change.success'));
                            return true;
                        }

                        elseif (in_array($args[0], array('ru', 'russian'))) {

                            $player->setLanguage('ru_RU', true);
                            $player->sendMessage(Core::getPrefix() . $player->translate('language.change.success'));
                            return true;
                        } else {
                            $player->sendMessage(Core::getPrefix() . $player->translate('language.change.failed'));
                        }
                    }
                    $player->sendMessage(Core::getPrefix() . $player->translate('language.list'));
                    return false;
                }
            }
            return true;
        }
    }

?>