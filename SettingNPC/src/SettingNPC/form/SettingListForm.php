<?php

declare(strict_types=1);

namespace SettingNPC\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use SettingNPC\SettingNPC;
use function strtolower;

final class SettingListForm implements Form{

    public function jsonSerialize() : array{
        $api = SettingNPC::getInstance();
        $arr = [];
        foreach($api->getLists() as $list){
            array_push($arr, array('text' => '- ' . $list . " 클릭시 커스텀엔피시 수정"));
        }
         return [
            'type' => 'form',
            'title' => '§l§b[커스텀엔피시]§r§7',
            'content' => "수정할 커스텀엔티티 를 클릭하세요.",
            'buttons' => $arr
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        $api = SettingNPC::getInstance();
        if($data === null) return;
        if (!$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            $player->sendMessage(SettingNPC::TAG . "권한이 없습니다.");
            return;
        }
        if($data !== null){
            $arr = [];
            foreach($api->getLists() as $Name){
                array_push($arr, $Name);
            }
            if (! isset ( $this->chat [$name] )) {
                $this->api->SettingEvent ($sender,$arr[$data]);
                $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                return true;
            }
            if (date("YmdHis") - $this->chat [$name] < 3) {
                $sender->sendMessage (SettingNPC::TAG . "이용 쿨타임이 지나지 않아 불가능합니다.");
                return true;
            } else {
                $this->api->SettingEvent ($sender,$arr[$data]);
                $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                return true;
            }
            return;
        }
    }
}
