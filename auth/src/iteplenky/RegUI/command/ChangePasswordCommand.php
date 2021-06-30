<?php


namespace iteplenky\RegUI\command;


    use iteplenky\RegUI\Main;
    use iteplenky\RegUI\form\ChangePasswordForm;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\player\Player;

    class ChangePasswordCommand extends Command
    {
        /** @var Main */
        private $main;
        
        public function __construct(Main $main, string $name, string $description, string $permission, array $aliases)
        {
            $this->main = $main;
            
            parent::__construct($name, $description);
            $this->setPermission($permission);
            $this->setAliases($aliases);
        }
        
        /**
         * @param CommandSender $sender
         * @param string $label
         * @param array $args
         * @return void
         */
        public function execute(CommandSender $sender, string $label, array $args) : void
        {
            if (!$this->testPermission($sender)) return;

            if ($sender instanceof Player)
                $sender->sendForm(new ChangePasswordForm($this->main));

            else $sender->sendMessage('Только в игре');
        }
    }

?>
