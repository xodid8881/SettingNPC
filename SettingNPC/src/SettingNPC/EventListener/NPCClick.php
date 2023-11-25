<?php
declare(strict_types=1);

namespace SettingNPC\EventListener;

use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\Server;
use SettingNPC\SettingNPC;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;


class NPCClick implements Listener
{

    private $chat;
    public function onEntityDamage(EntityDamageByEntityEvent $event) {
        $api = SettingNPC::getInstance();
        $entity = $event->getEntity ();
        $damager = $event->getDamager ();
        if (!$damager instanceof Player) {
            if ($entity->getNameTag() != null){
                if (isset($this->npcdb [$entity->getNameTag()])){
                    $event->cancel();
                    $command = $this->npcdb [$npcname] ["Command"];
                }
            }
        }
        if ($damager instanceof Player) {
            if ($entity->getNameTag() != null){
                if (isset($this->npcdb [$entity->getNameTag()])){
                    $event->cancel();
                    $command = $this->npcdb [$npcname] ["Command"];
                    if (! isset ( $this->chat [$name] )) {
                        $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                        return true;
                    }
                    if (date("YmdHis") - $this->chat [$name] < 3) {
                        $player->sendMessage ( Market::TAG . "이용 쿨타임이 지나지 않아 불가능합니다." );
                        return true;
                    } else {
                        Server::getInstance()->getCommandMap ()->dispatch ( $damager, $command );
                        $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                        return true;
                    }
                }
            }
        }
    }
}
