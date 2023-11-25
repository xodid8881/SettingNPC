<?php

declare(strict_types=1);

namespace SettingNPC;

use JsonException;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use ReflectionException;

use SettingNPC\Commands\SettingNPCCommand;

use function strtolower;

final class Loader extends PluginBase{
    use SingletonTrait;

    private SettingNPC $api;

    protected function onLoad() : void{
        self::setInstance($this);
    }

    /**
     * @throws ReflectionException
     */
    protected function onEnable() : void{
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }

        $this->api = new SettingNPC(
            npc: new Config ($this->getDataFolder() . "npcs.yml", Config::YAML),
        );

        $server = $this->getServer();
        $cmdMap = $server->getCommandMap();

        $cmdMap->register('SettingNPC', new SettingNPCCommand());
    }

    /**
     * @throws JsonException
     */
    protected function onDisable() : void{
        $this->api->save();
    }
}
