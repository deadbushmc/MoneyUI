<?php

namespace DeadBush\MoneyUI;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use onebone\economyapi\EconomyAPI;
use jojoe77777\FormAPI\Form;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\FormAPI;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\utils\Config;

class moneyui extends PluginBase implements Listener{
    public function onEnable(): void{
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args): bool{
        switch($cmd->getName()){
            case "money":
                if($sender instanceof Player){
                    $this->moneyui($sender);
                }
        }
        return true;
    }

    public function moneyui($sender){
        $form = new SimpleForm(function (Player $sender, int $data = null){
            if($data === null) {
                return true;
            }
            switch($data) {
                case 0:
                    $this->playermoney($sender);
                break;
    
                case 1:
                    $this->transfer($sender);
                break;

                case 2:
                    $this->getServer()->dispatchCommand($sender,"topmoney"); 
                break;

                case 3:
                    if($sender->hasPermission("money.admin")){
                        $this->admin($sender);
                    }else{
                        $sender->sendMessage("§4You dont have permmission");
                    }
                break;

                case 4:
                break;
            }
        });
            
        $form->setTitle($this->getConfig()->get("title"));
        $form->addButton($this->getConfig()->get("buttona"));
        $form->addButton($this->getConfig()->get("buttonb"));
        $form->addButton($this->getConfig()->get("buttonc"));
        $form->addButton($this->getConfig()->get("buttonAdmin"));
        $form->addButton($this->getConfig()->get("exit"));
        $sender->sendForm($form);
        return $form;
    }

    public function playermoney($sender){
        $mymoney = EconomyAPI::getInstance()->myMoney($sender);
        $form = new SimpleForm(function (Player $sender, int $data = null){
            if($data === null) {
                return true;
            }
            switch($data){
                case 0:
                    $this->moneyui($sender);
                break;
            }
        });
            
        $form->setTitle($this->getConfig()->get("title"));
        $form->setContent("§l§2You have §4" . $mymoney . "$ §2in your account");
        $form->addButton($this->getConfig()->get("back"));
        $sender->sendForm($form);
        return $form;
    }

    public function transfer($sender){
        $form = new CustomForm(function (Player $sender, array $data = null){
            if($data === null) {
                return true;
            }
            $this->getServer()->dispatchCommand($sender, "pay " . $data[0] . " " . $data[1]);
        });
            
        $form->setTitle($this->getConfig()->get("title"));
        $form->addInput("§eEnter the player name");
        $form->addInput("§eEnter the money amount");
        $sender->sendForm($form);
        return $form;
    }

    public function admin($sender){
        $form = new SimpleForm(function (Player $sender, int $data = null){
            if($data === null){
                return true;
            }
            switch($data){
                case 0:
                    $this->addmoney($sender);
                break;

                case 1:
                    $this->takemoney($sender);
                break;

                case 2:
                    $this->moneyui($sender);
                break;
            }
        });
        $form->setTitle($this->getConfig()->get("title"));
        $form->addButton($this->getConfig()->get("add"));
        $form->addButton($this->getConfig()->get("take"));
        $form->addButton($this->getConfig()->get("back"));
        $sender->sendForm($form);
        return $form;
    }

    public function addmoney($sender){
        $form = new CustomForm(function (Player $player, array $data = null){
            if($data === null){
                return true;
            }
            EconomyAPI::getinstance()->addMoney($data[0], $data[1]);
        });
        $form->setTitle($this->getConfig()->get("title"));
        $form->addInput("§eEnter the player name");
        $form->addInput("§eEnter the money amount");
        $sender->sendForm($form);
        return $form;
    }

    public function takemoney($sender){
        $form = new CustomForm(function (Player $player, array $data = null){
            if($data === null){
                return true;
            }
            EconomyAPI::getinstance()->reduceMoney($data[0], $data[1]);
        });
        $form->setTitle($this->getConfig()->get("title"));
        $form->addInput("§eEnter the player name");
        $form->addInput("§eEnter the money amount");
        $sender->sendForm($form);
        return $form;
    }
}