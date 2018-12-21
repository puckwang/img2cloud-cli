<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem;

class ImgurService
{
    /**
     * @var object
     */
    private $client;

    /**
     * @var array
     */
    private $accessToken;

    private $clientData;

    public function __construct()
    {
        $this->initClient();
        $this->fetchAccessTokenFromFile();
    }

    private function initClient(): void
    {
        $this->client = new \Imgur\Client();
    }

    public function setOption($client_id, $client_secret)
    {
        $this->client->setOption('client_id', $client_id);
        $this->client->setOption('client_secret', $client_secret);
        $this->clientData['clientId'] = $client_id;
        $this->clientData['clientSecret'] = $client_secret;
    }

    public function hasClientId()
    {
        return isset($this->clientData);
    }

    public function getAuthenticationUrl(): string
    {
        return $this->client->getAuthenticationUrl("token");
    }

    public function parseAccessToken(string $url): void
    {
        $query = parse_url($url)['fragment'];
        parse_str($query, $this->accessToken);
        $this->client->setAccessToken($this->accessToken);
        $this->saveAccessTokenToFile();
    }

    private function checkAccessToken(): void
    {
        if (!$this->hasLogin()) {
            return;
        }
        $this->client->setAccessToken($this->accessToken);
        $this->setOption($this->clientData['clientId'], $this->clientData['clientSecret']);

        if ($this->client->checkAccessTokenExpired()) {
            $this->client->refreshToken();
        }
    }

    public function hasLogin(): bool
    {
        return isset($this->accessToken);
    }

    private function fetchAccessTokenFromFile()
    {
        try {
            $fileSystem = new Filesystem();
            $username = exec("whoami");
            $filename = "/Users/{$username}/.img2imgur.json";
            if ($fileSystem->exists($filename)) {
                $config = json_decode($fileSystem->get($filename), true);
                $this->accessToken = $config['accessToken'];
                $this->setOption($config['clientData']['clientId'], $config['clientData']['clientSecret']);
                $this->checkAccessToken();
            }
        } catch (\Exception $e) {
            $this->accessToken = null;
            $this->saveAccessTokenToFile();
            error_log($e->getMessage() . $e->getTraceAsString());
            throw new \Exception("驗證失敗，請重新登入。");
        }
    }

    protected function saveAccessTokenToFile()
    {
        $fileSystem = new Filesystem();
        $username = exec("whoami");
        $filename = "/Users/{$username}/.img2imgur.json";
        $fileSystem->put($filename, json_encode([
            'accessToken' => $this->accessToken,
            'clientData'  => $this->clientData,
        ]));
    }

    public function saveImageLog($res)
    {
        try {
            $fileSystem = new Filesystem();
            $username = exec("whoami");
            $filename = "/Users/{$username}/.img2imgur.history.json";
            $history = [];
            if ($fileSystem->exists($filename)) {
                $history = json_decode($fileSystem->get($filename), true);
            }

            $history[] = $res;

            $fileSystem->put($filename, json_encode($history));
        } catch (\Exception $e) {

        }
    }

    public function upload($img)
    {
        $image = file_get_contents($img);
        $base64 = base64_encode($image);

        $imageData = [
            'image' => $base64,
            'type'  => 'base64',
        ];

        return $this->client->api('image')->upload($imageData);
    }
}