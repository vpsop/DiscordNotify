<?php

declare(strict_types=1);

namespace ApexDev\DiscordNotify;


use pocketmine\event\Listener;
use pocketmine\event\player\cheat\PlayerCheatEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventListener implements Listener
{
    private $plugin;
    
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onPlayerJoin(PlayerJoinEvent $e){
        $eventType = "join";
        $name = $e->getPlayer()->getName();
        $this->plugin->sendDiscordMsg($name, $eventType);
    }
    
    
    public function onPlayerQuit(PlayerQuitEvent $e){
        $eventType = "quit";
        $name = $e->getPlayer()->getName();
        $this->plugin->sendDiscordMsg($name, $eventType);
    }

    public function onPlayerChat(PlayerChatEvent $e)
    {
        $name = $e->getPlayer()->getName();
        $chatMsg = $e->getMessage();
        $this->plugin->sendDiscordChat($name, $chatMsg);
    }
}