<?php

declare(strict_types=1);

namespace Market\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use Market\Market;
use MoneyManager\MoneyManager;
use function strtolower;

use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

use pocketmine\permission\DefaultPermissions;

final class MarketSettingTaskForm implements Form{

    public function jsonSerialize() : array{
        $api = Market::getInstance();

        return [
            'type' => 'form',
            'title' => '[ MarketManager ]',
            'content' => '버튼을 눌러주세요.',
            'buttons' => [
                [
                    'text' => '시장 관리'
                ],
                [
                    'text' => '엔피시 소환'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        if($data === null) return;
        $name = $player->getName();
        $api = Market::getInstance();
        if ($data === 0) {
            if (!$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
                $player->sendMessage( Market::TAG . "권한이 없습니다.");
                return;
            }
            sleep(1);
            $api->onOpOpen ($player);
            return;
        }
        if ($data === 1) {
            if (!$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
                $player->sendMessage( Market::TAG . "권한이 없습니다.");
                return;
            }
            sleep(1);
            $api->ShopEntitySpawn ($player);
            $player->sendMessage( Market::TAG . '시장 엔피시를 소환했습니다.');
            return;
        }
    }
}