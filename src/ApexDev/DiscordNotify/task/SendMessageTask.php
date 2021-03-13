<?php

declare(strict_types=1);

namespace ApexDev\DiscordNotify\task;

use ApexDev\DiscordNotify\Main;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class SendMessageTask extends AsyncTask
{
    private $whURL;
    private $msg;
    private $whData;

    public function __construct(string $whURL, string $msg, array $whData)
    {
        $this->whURL = $whURL;
        $this->msg = $msg;
        $this->whData = $whData;
    }

    public function onRun()
    {
        $curl = curl_init($this->whURL);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->whData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $curlerror = curl_error($curl);


        $responsejson = json_decode($response, true);
        $success = false;
        $error = 'Some Error Occured';
        if ($curlerror != '') {
            $error = $curlerror;
        } elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 204) {
            $error = $responsejson['message'];
        } elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 204 or $response === '') {
            $success = true;
        }
        $result = ['Response' => $response, 'Error' => $error, 'success' => $success];
        $this->setResult($result, true);
    }

    public function onCompletion(Server $server)
    {
        $plugin = $server->getPluginManager()->getPlugin('DiscordNotify');
        if (!$plugin instanceof Main) {
            return;
        }
        if (!$plugin->isEnabled()) {
            return;
        }
        // Main::getInstance()->getServer()->broadcastMessage("Message Sent to Discord");
    }
}