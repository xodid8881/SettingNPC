<?php
declare(strict_types=1);

namespace Market\EventListener;

use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\Server;
use Market\Market;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;


class NPCClick implements Listener
{

    private $chat;
    public function onEntityDamage(EntityDamageByEntityEvent $event) {
        $api = Market::getInstance();
        $entity = $event->getEntity ();
        $damager = $event->getDamager ();
        if (! $damager instanceof Player) {
            if ($entity->getNameTag() != null){
                if ($entity->getNameTag() == "시장도우미"){
                    $event->cancel();
                }
            }
        }
        if ($damager instanceof Player) {
            if ($entity->getNameTag() != null){
                if ($entity->getNameTag() == "시장도우미"){
                    $event->cancel();
                    if (! isset ( $this->chat [$name] )) {
                        $api->pldb [strtolower($name)] ["이용이벤트"] = "구매";
                        $api->pldb [strtolower($name)] ["페이지"] = 1;
                        $api->MarketEvent ($player);
                        $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                        return true;
                    }
                    if (date("YmdHis") - $this->chat [$name] < 3) {
                        $player->sendMessage ( Market::TAG . "이용 쿨타임이 지나지 않아 불가능합니다." );
                        return true;
                    } else {
                        $api->pldb [strtolower($name)] ["이용이벤트"] = "구매";
                        $api->pldb [strtolower($name)] ["페이지"] = 1;
                        $api->MarketEvent ($player);
                        $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                        return true;
                    }
                }
            }
        }
    }
}