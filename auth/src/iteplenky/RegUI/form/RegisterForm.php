<?php

declare(strict_types=1);

namespace iteplenky\RegUI\form;

use iteplenky\RegUI\Main;

use jojoe77777\FormAPI\CustomForm;

use pocketmine\player\Player;

class RegisterForm extends CustomForm
{
    
    public function __construct(Main $main, $feedback)
    {
        parent::__construct(function(Player $player, array $data = null) use($main)
        {
            if($data === null)
            {
                $player->sendForm(new RegisterForm($main, 'Пройдите регистрацию'));
                return;
            }
            
            if(empty($data[0]))
            {
                $player->sendForm(new RegisterForm($main, $main->config["error"]["emptyPassword"]));
                return;
            }
            
            if(strlen($data[0]) < $main->config["minLength"])
            {
                $player->sendForm(new RegisterForm($main, $main->config["error"]["minLength"]));
                return;
            }
            
            if(!ctype_alnum($data[0]))
            {
                $player->sendForm(new RegisterForm($main, $main->config["error"]["ctype"]));
                return;
            }
            
            if(empty($data[1]))
            {
                $player->sendForm(new RegisterForm($main, $main->config["error"]["emptyEmail"]));
                return;
            }
            
            $password = $data[0];
            
            if($main->config["hashPassword"] === true)
            {
                $password = crypt($password, '$5$rounds=5000$usesomesillystringforsalt$');
            }
            
            $main->register($player, $password, "{$data[1]}");
            $main->setLogined($player->getName());
            $player->setImmobile(false);
            $player->sendMessage($main->config["success"]["register"]);
            $player->sendMessage(str_replace(["{player}", "{count}"], [$player->getName(), $main->getPlayersCount()], $main->config["success"]["broadcast"]));
            $main->getServer()->getLogger()->notice('§7Новый игрок §b' . $player->getName() . ' §7зарегистрировался. §8(§6' . $password . '§8), §7#' . $main->getPlayersCount());
        });
        
        $this->setTitle($feedback);
        $this->addInput($main->config["form"]["register"]["password"], $main->config["form"]["register"]["passwordSub"]);
        $this->addInput($main->config["form"]["register"]["vkId"], $main->config["form"]["register"]["vkIdSub"]);
    }
}
