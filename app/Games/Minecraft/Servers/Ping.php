<?php

namespace Azuriom\Games\Minecraft\Servers;

use Azuriom\Games\Minecraft\Servers\Protocol\MinecraftPing;
use Azuriom\Games\ServerBridge;
use Azuriom\Models\User;
use Exception;
use Illuminate\Support\Arr;
use RuntimeException;

class Ping extends ServerBridge
{
    protected const DEFAULT_PORT = 25565;

    public function getServerData()
    {
        try {
            return $this->ping($this->server->address, $this->server->port);
        } catch (Exception $e) {
            return null;
        }
    }

    public function verifyLink()
    {
        return $this->ping($this->server->address, $this->server->port);
    }

    public function sendCommands(array $commands, User $user = null, bool $needConnected = false)
    {
        report(new RuntimeException('Command cannot be executed with ping link.'));
    }

    public function canExecuteCommand()
    {
        return false;
    }

    public function getDefaultPort()
    {
        return self::DEFAULT_PORT;
    }

    protected function ping(string $address, int $port = null, bool $resolveSrv = true)
    {
        $pinger = new MinecraftPing($address, $port ?? self::DEFAULT_PORT, $resolveSrv);

        try {
            $response = $pinger->ping(self::TIMEOUT);

            return [
                'players' => Arr::get($response, 'players.online', 0),
                'max_players' => Arr::get($response, 'players.max', 0),
            ];
        } finally {
            $pinger->close();
        }
    }
}