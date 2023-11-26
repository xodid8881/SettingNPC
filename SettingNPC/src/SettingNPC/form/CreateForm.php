<?php

declare(strict_types=1);

namespace SettingNPC\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use SettingNPC\SettingNPC;
use function strtolower;

final class CreateForm implements Form{

    public function jsonSerialize() : array{
        return [
            'type' => 'custom_form',
            'title' => '§l§b[커스텀엔피시]§r§7',
            'content' => [
                [
                    'type' => 'input',
                    'text' => "이름을 적어주세요."
                ],
                [
                    'type' => 'input',
                    'text' => "이용될 명령어를 적어주세요."
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        $api = SettingNPC::getInstance();
        if($data === null) return;
        if(!isset($data[0])){
            $player->sendMessage(SettingNPC::TAG . '빈칸을 채워주세요.');
            return;
        }
        if(!isset($data[1])){
            $player->sendMessage(SettingNPC::TAG . '빈칸을 채워주세요.');
            return;
        }
        $api->EntitySpawn($player,$data[0],$data[1]);
        return;
    }
}
