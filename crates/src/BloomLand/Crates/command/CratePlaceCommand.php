<?php


namespace BloomLand\Crates\command;


    use BloomLand\Crates\Main;

    use Frago9876543210\EasyForms\elements\Button;
    use Frago9876543210\EasyForms\elements\Image;
    use Frago9876543210\EasyForms\forms\MenuForm;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\player\Player;

    class CratePlaceCommand extends Command 
    {
        private $plugin;

        public function __construct(Main $plugin) 
        {
            $this->plugin = $plugin;

            parent::__construct('models', 'пустое описание', '/models');
        }

        public function getPlugin() : Main
        {
            return $this->plugin;
        }
      
        public function execute(CommandSender $player, ?string $commandLabel, ?array $args) : void 
        {
            if ($player instanceof Player) {
                
                $player->sendForm(new MenuForm(
                    "Модели", 
                    "§e>> §7Выбери действие", 
                    [
                        new Button('Монетный сундук', new Image('textures/ui/buttons/unlocked_button', Image::TYPE_PATH)), 
                        new Button('Воздушный шар', new Image('textures/ui/buttons/unlocked_button', Image::TYPE_PATH)), 
                        new Button('Стол зачарований', new Image('textures/ui/buttons/unlocked_button', Image::TYPE_PATH)), 
                        new Button('Скупщик', new Image('textures/ui/buttons/unlocked_button', Image::TYPE_PATH)), 
                    ],
                    function(Player $player, Button $selected) : void {

                        $this->getPlugin()->modelsControl($player, $selected->getValue());

                    }

                ));

            }

        }

    }

?>
