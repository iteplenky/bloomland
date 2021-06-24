<?php


namespace iteplenky\RegUI\command;


	use iteplenky\RegUI\Main;

	use pocketmine\command\Command;
	use pocketmine\command\CommandSender;
	
	use pocketmine\player\Player;

	class LogoutCommand extends Command
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
			
			if (empty($args[0])) {

				if (!$this->main->isLogined($sender->getName())) {
				
					$sender->sendMessage($this->main->config["error"]["notLogined"]);
					return;
				
				}
			
				$this->main->logout($sender->getName());
			
			} else {
			
				if (!$sender->hasPermission("logout.player.cmd")) {
			
					$sender->sendMessage($this->main->config["error"]["noPermission"]);
					return;

				} else {
			
					$target = $args[0];
				
					if ($this->main->getServer()->getPlayerByPrefix($target) !== null) {
			
						$target = $this->main->getServer()->getPlayerByPrefix($target);
					
						if (!$this->main->isRegistered($target->getName())) {
			
							$sender->sendMessage('Игрок не зарегистрирован');
							return;
			
						}
						
						$this->main->logout($target->getName());
						$sender->sendMessage('Пароль игрока §c' . $target->getName() . '§r сброшен.');
					
					} else {
					
						if (!$this->main->isRegistered($target)) {
					
							$sender->sendMessage('Игрок не зарегистрирован');
							return;
					
						}
						
						$this->main->logout($target);
						$sender->sendMessage('Пароль игрока §c' . $target . '§r сброшен.');
					
					}
				
				}
			
			}
		
		}
	
	}

?>
