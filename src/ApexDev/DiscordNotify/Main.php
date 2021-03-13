<?php

declare(strict_types=1);

namespace ApexDev\DiscordNotify;

use ApexDev\DiscordNotify\task\SendMessageTask;


use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    /** @var Main */
    private static $instance;

    public function onEnable()
    {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }


    public static function getInstance(): Main
    {
        return self::$instance;
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


    public static function parseMessage(string $message, array $data): string
    {
        $msg = str_replace("{Player}", $data["playerName"], $message);
        return $msg;
    }
}