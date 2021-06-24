<?php

declare(strict_types=1);

namespace iteplenky\RegUI\command;

use iteplenky\RegUI\Main;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class VkCommand extends Command
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
		if(!$this->testPermission($sender))
		{
			return;
		}
	}
}