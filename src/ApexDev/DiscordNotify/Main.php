<?php

declare(strict_types=1);

namespace ApexDev\DiscordNotify;

use ApexDev\DiscordNotify\task\SendMessageTask;
use ApexDev\DiscordNotify\utils\ConfigManager;
use ApexDev\DiscordNotify\utils\ProfileUrlManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase
{
    /** @var Main */
    private static $instance;

    /** @var Config */
    private $config;

    /** @var Config */
    private $pfp;

    public function onEnable()
    {
        self::$instance = $this;
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->pfp = new Config($this->getDataFolder() . "profileURL.yml", Config::YAML, array());
    }


    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function getConfig() : Config
    {
        return $this->config;
    }
    
    public function getProfiles() : Config
    {
        return $this->pfp;
    }

    public function sendDiscordMsg(string $playerName, string $type)
    {
        if($type === "join"){
            $msg = ConfigManager::getMessage("player-join-message");
        }else{
            $msg = ConfigManager::getMessage("player-leave-message");
        }

        $messageData = [
            "playerName" => $playerName
        ];
        $msg  = self::parseMessage($msg, $messageData);

        $whUsername = ConfigManager::getMessage("webhookUsername");
        $whURL = ConfigManager::getMessage("webhookURL");
        $whData = array('content' => $msg, 'username' => $whUsername);

        $this->getServer()->getAsyncPool()->submitTask(new SendMessageTask($whURL, $msg, $whData));
    }

    public function sendDiscordChat(string $name, string $chatMsg)
    {
        $msg = $chatMsg;
        $whUsername = $name;
        $whAvatar = ProfileUrlManager::getProfileURL(strtoupper($name));

        if(empty($whAvatar) || ctype_space($whAvatar)){
            // An exclamation mark pfp URl
            $whAvatar = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTl10-O-B5WQzfT-AMca0EcKiboofwetosvKhYTxuoUot4h-Cf_Z12i2r73IfCfMV9QlNk&usqp=CAU";
        }
        $whURL = ConfigManager::getMessage("chat-webhookURL");
        $whData = array('content' => $msg, 'username' => $whUsername, 'avatar_url' => $whAvatar);

        $this->getServer()->getAsyncPool()->submitTask(new SendMessageTask($whURL, $msg, $whData));
        
    }

    public static function parseMessage(string $message, array $data): string
    {
        $msg = str_replace("{Player}", $data["playerName"], $message);
        return $msg;
    }


}