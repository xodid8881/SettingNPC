<?php
declare(strict_types=1);

namespace SettingNPC\EventListener;

use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\Server;
use SettingNPC\SettingNPC;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use pocketmine\permission\DefaultPermissions;

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
                    $command = $this->npcdb [$entity->getNameTag()] ["Command"];
                    $CoolTime = $this->npcdb [$entity->getNameTag()] ["CoolTime"];
                    $Cool = (int)$this->npcdb [$entity->getNameTag()] ["Cool"];
                    if ($CoolTime == "false"){
                        $permissions = $this->npcdb [$entity->getNameTag()] ["Permissions"];
                        if ($permissions == "ROOT_OPERATOR"){
                            if (!$damager->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
                                $damager->sendMessage(SettingNPC::TAG . "권한이 없습니다.");
                                return true;
                            }
                            Server::getInstance()->getCommandMap ()->dispatch ( $damager, $command );
                            return true;
                        } else if ($permissions == "ROOT_USER"){
                            Server::getInstance()->getCommandMap ()->dispatch ( $damager, $command );
                            return true;
                        }
                    } else if ($CoolTime == "true"){
                        if (! isset ( $this->chat [$damager->getName ()] )) {
                            $this->chat [$damager->getName ()] = date("YmdHis",strtotime ("+{$Cool} seconds"));
                            return true;
                        }
                        if (date("YmdHis") - $this->chat [$damager->getName ()] < $Cool) {
                            $damager->sendMessage ( SettingNPC::TAG . "이용 쿨타임이 지나지 않아 불가능합니다." );
                            return true;
                        } else {
                            $permissions = $this->npcdb [$entity->getNameTag()] ["Permissions"];
                            if ($permissions == "ROOT_OPERATOR"){
                                if (!$damager->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
                                    $damager->sendMessage(SettingNPC::TAG . "권한이 없습니다.");
                                    return true;
                                }
                                Server::getInstance()->getCommandMap ()->dispatch ( $damager, $command );
                                $this->chat [$damager->getName ()] = date("YmdHis",strtotime ("+{$Cool} seconds"));
                                return true;
                            } else if ($permissions == "ROOT_USER"){
                                Server::getInstance()->getCommandMap ()->dispatch ( $damager, $command );
                                $this->chat [$damager->getName ()] = date("YmdHis",strtotime ("+{$Cool} seconds"));
                                return true;
                            }
                        }
                    }
                }
            }
        }
    }
}
