<?php

declare(strict_types=1);

namespace iteplenky\RegUI\form;

use iteplenky\RegUI\Main;

use jojoe77777\FormAPI\CustomForm;

use pocketmine\player\Player;

class ChangePasswordForm extends CustomForm
{
	
	public function __construct(Main $main)
	{
		parent::__construct(function(Player $player, array $data = null) use($main)
		{
			if($data === null)
			{
				$player->sendMessage(' §r> Вы §bничего не ввели§r, поэтому форма закрылась.');
				return;
			}
			
			if(empty($data[0]))
			{
				$player->sendMessage($main->config["error"]["emptyOldPassword"]);
				return;
			}
			
			if(empty($data[1]))
			{
				$player->sendMessage($main->config["error"]["emptyNewPassword"]);
				return;
			}
			
			$oldPassword = $data[0];
			
			if($main->config["hashPassword"])
			{
				$p = $main->getData($player->getName())['password'];
				$oldPassword = hash_equals($p, crypt($oldPassword, $p));
				unset($p);
			} else {
				$p = $main->getData($player->getName())['password'];
				if ($data[0] == $p) {
					$oldPassword = true;
				} else {
					$oldPassword = false;
				}
			}
			
			if(!$oldPassword)
			{
				$player->sendMessage($main->config["error"]["incorrectPassword"]);
				return;
			}
			
			$password = $data[1];
			
			if($main->config["hashPassword"])
			{
				$password = crypt($password, '$5$rounds=5000$usesomesillystringforsalt$');
			}
			
			$main->changepassword($player->getName(), $password);
			$player->sendMessage($main->config["success"]["cp"]);
        });
        
        $this->setTitle($main->config["form"]["cp"]["title"]);
        $this->addInput($main->config["form"]["cp"]["oldPassword"], $main->config["form"]["cp"]["oldPasswordSub"]);
        $this->addInput($main->config["form"]["cp"]["newPassword"], $main->config["form"]["cp"]["newPasswordSub"]);
	}
}