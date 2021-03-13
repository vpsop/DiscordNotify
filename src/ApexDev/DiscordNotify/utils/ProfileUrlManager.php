<?php

namespace ApexDev\DiscordNotify\utils;

use ApexDev\DiscordNotify\Main;

class ProfileUrlManager
{
    /**
     * @param string $name
     * @return string
     */
    public static function getProfileURL(string $name): string
    {
        $pfpURL = (string) Main::getInstance()->getProfiles()->get($name);
        return $pfpURL;
    }
}