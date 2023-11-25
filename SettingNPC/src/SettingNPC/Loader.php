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

        $server->getPluginManager()->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event) : void {
            $player = $event->getPlayer();
            $name = $player->getName();
            if(!isset($this->api->pldb ["목록"] [strtolower($name)])){
                $this->api->pldb [strtolower($name)] ["상점"] = "없음";
                $this->api->pldb ["목록"] [strtolower($name)] ["구매정보"] = "없음";
                $this->api->pldb ["목록"] [strtolower($name)] ["페이지"] = 1;
                $this->api->pldb ["목록"] [strtolower($name)] ["이용이벤트"] = "없음";
                $this->api->save();
            }
        }, EventPriority::MONITOR, $this);
    }

    /**
     * @throws JsonException
     */
    protected function onDisable() : void{
        $this->api->save();
    }
}
