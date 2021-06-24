<?php


namespace iteplenky\RegUI\command;


	use iteplenky\RegUI\Main;
	use iteplenky\RegUI\form\RegisterForm;

	use pocketmine\command\Command;
	use pocketmine\command\CommandSender;

	class RegisterCommand extends Command
	{
		
		/** @var Main */
		private $main;
		
		public function __construct(Main $main, string $name, string $description, string $permission)
		{
			$this->main = $main;
			
			parent::__construct($name, $description);
			$this->setPermission($permission);
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
			
			if ($this->main->isRegistered($sender->getName())) {

				$sender->sendMessage($this->main->config["error"]["registered"]);
				return;
			
			}
			
			$sender->sendForm(new RegisterForm($this->main, 'Регистрация'));
		}
	
	}

?>
