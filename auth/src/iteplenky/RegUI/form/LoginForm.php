<?php

declare(strict_types=1);

namespace iteplenky\RegUI\form;

use iteplenky\RegUI\Main;

use jojoe77777\FormAPI\CustomForm;

use pocketmine\player\Player;

class LoginForm extends CustomForm
{
    
    public function __construct(Main $main)
    {
        parent::__construct(function(Player $player, array $data = null) use($main)
        {
            if($data === null)
            {
                $player->sendForm(new LoginForm($main));
                return;
            }
            
            if(empty($data[0]))
            {
                $player->sendForm(new LoginForm($main));
                $player->sendMessage($main->config["error"]["emptyPassword"]);
                return;
            }
            
            $password = $main->getData($player->getName())['password'];
            
            if($main->config["hashPassword"] === true)
            {
                $password = hash_equals($password, crypt($data[0], $password));
            } else
            {
                $password = $data[0] == $password ? true : false;
            }
            
            if(!$password)
            {
                $player->sendForm(new LoginForm($main));
                $player->sendMessage($main->config["error"]["incorrectPassword"]);
                
                if(!isset($main->logCount[$player->getName()]))
                {
                    $main->logCount[$player->getName()] = 1;
                } else
                {
                    $main->logCount[$player->getName()]++;
                    if($main->logCount[$player->getName()] >= 3)
                    {
                        $player->close("", $main->config["error"]["logCount"]);
                        return;
                    }
                }
                return;
            }
            
            if(isset($main->logCount[$player->getName()]))
            {
                unset($main->logCount[$player->getName()]);
            }
            $main->setLogined($player->getName());
            $player->setImmobile(false);
            $main->updateIP($player->getName(), $player->getNetworkSession()->getIp());
            $player->sendMessage($main->config["success"]["login"]);
        });
        
        $this->setTitle($main->config["form"]["login"]["title"]);
        $this->addInput($main->config["form"]["login"]["password"], $main->config["form"]["register"]["passwordSub"]);
    }
}
