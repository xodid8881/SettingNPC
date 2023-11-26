<?php

declare(strict_types=1);

namespace SettingNPC\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use SettingNPC\SettingNPC;
use function strtolower;

final class SettingForm implements Form{

    protected $text;
    public function __construct(String $text)
    {
        $this->text = $text;
    }

    public function jsonSerialize() : array{
        return [
            'type' => 'custom_form',
            'title' => '§l§b[커스텀엔피시]§r§7',
            "content" => [
                [
                    "type" => "toggle",
                    "text" => "명령어 권한 존재 여부",
                    "default" => false
                ],
                [
                    "type" => "toggle",
                    "text" => "쿨타임 존재 여부",
                    "default" => false
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        $api = SettingNPC::getInstance();
        if($data === null){
            return;
        }
        if ($data[0] == true){
            $this->npcdb [$this->text] ["Permissions"] = "ROOT_OPERATOR";
        } else {
            $this->npcdb [$this->text] ["Permissions"] = "ROOT_USER";
        }
        if ($data[1] == true){
            $this->npcdb [$this->text] ["CoolTime"] = "true";
        } else {
            $this->npcdb [$this->text] ["CoolTime"] = "false";
        }
        $player->sendMessage(SettingNPC::TAG . "엔피시 설정을 완료했습니다.");
    }
}
