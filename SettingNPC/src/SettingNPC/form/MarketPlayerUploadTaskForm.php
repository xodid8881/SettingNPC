<?php

declare(strict_types=1);

namespace Market\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use Market\Market;
use MoneyManager\MoneyManager;
use function strtolower;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

final class MarketPlayerUploadTaskForm implements Form{

    private static BigEndianNbtSerializer $serializer;

    public function jsonSerialize() : array{
        return [
            'type' => 'custom_form',
            'title' => '[ MarketManager ]',
            'content' => [
                [
                    'type' => 'input',
                    'text' => '물품 이름을 적어주세요.'
                ],
                [
                    'type' => 'input',
                    'text' => '물품 갯수를 적어주세요.'
                ],
                [
                    'type' => 'input',
                    'text' => "생각한 즉시 구매가를 적어주세요.\n터무니 없는 가격이면 신고 당할 수 있습니다."
                ],
                [
                    'type' => 'input',
                    'text' => "생각한 최소 경매가를 적어주세요.\n터무니 없는 가격이면 신고 당할 수 있습니다."
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        if($data === null) return;
        $name = $player->getName();
        $api = Market::getInstance();
        if (!isset($data[0])) {
            $player->sendMessage( Market::TAG . '빈칸을 채워주세요.');
            return;
        }
        if (!isset($data[1])) {
            $player->sendMessage( Market::TAG . '빈칸을 채워주세요.');
            return;
        }
        if (!isset($data[2])) {
            $player->sendMessage( Market::TAG . '빈칸을 채워주세요.');
            return;
        }
        if (!isset($data[3])) {
            $player->sendMessage( Market::TAG . '빈칸을 채워주세요.');
            return;
        }
        if (! is_numeric ($data[1])) {
            $player->sendMessage ( Market::TAG . "숫자를 이용 해야됩니다. " );
            return;
        }
        if (! is_numeric ($data[2])) {
            $player->sendMessage ( Market::TAG . "숫자를 이용 해야됩니다. " );
            return;
        }
        if (! is_numeric ($data[3])) {
            $player->sendMessage ( Market::TAG . "숫자를 이용 해야됩니다. " );
            return;
        }
        if (isset($api->marketdb ["물품리스트"] [$data[0]])) {
            $player->sendMessage ( Market::TAG . "해당 이름으로 이미 시장에 등록된 물품이 있습니다." );
            return;
        }
        if ($data[2] <= $data[3]) {
            $player->sendMessage ( Market::TAG . "경매가는 즉시 구매가보다 높거나 같으면 안됩니다." );
            return;
        }
        $HandItem = $player->getInventory()->getItemInHand();
        if ($HandItem == VanillaItems::AIR()) {
            $player->sendMessage ( Market::TAG . "시장에 등록할 아이템을 들고 이용해주세요. " );
            return;
        }
        self::$serializer = new BigEndianNbtSerializer();
        $item = self::$serializer->write(new TreeRoot($HandItem->nbtSerialize()));

        if ($player->getInventory ()->contains ( $item->setCount ((int)$data[1]) )) {
            $player->getInventory ()->removeItem ( $item->setCount ((int)$data[1]) );
        } else {
            $player->sendMessage ( Market::TAG . "보유한 아이템 갯수보다 많이 등록할 수 없습니다." );
            return;
        }
        $api->marketdb ["물품리스트"] [$data[0]] ["물품이름"] = $data[0];
        $api->marketdb ["물품리스트"] [$data[0]] ["등록자이름"] = $name;
        $api->marketdb ["물품리스트"] [$data[0]] ['nbt'] = $item;
        $api->marketdb ["물품리스트"] [$data[0]] ["갯수"] = $data[1];
        $api->marketdb ["물품리스트"] [$data[0]] ["즉시구매가"] = $data[2];
        $api->marketdb ["물품리스트"] [$data[0]] ["최소경매가"] = $data[3];
        $api->marketdb ["물품리스트"] [$data[0]] ["현재경매가"] = $data[3];
        $api->marketdb ["물품리스트"] [$data[0]] ["경매자"] = "없음";
        $api->marketdb ["물품리스트"] [$data[0]] ["등록당시시간"] = date("YmdHis");
        $api->marketdb ["물품리스트"] [$data[0]] ["등록종료시간"] = date("YmdHis",strtotime ("+10 minutes"));
        $api->marketdb ["물품리스트"] [$data[0]] ["종료시간"] = date("Y년 m월 d일 H시 i분 s초",strtotime ("+10 minutes"));

        $api->pldb [strtolower($name)] ["등록리스트"] [$data[0]] ["물품이름"] = $data[0];
        $api->pldb [strtolower($name)] ["등록리스트"] [$data[0]] ["등록자이름"] = $name;
        $api->pldb [strtolower($name)] ["물품리스트"] [$data[0]] ['nbt'] = $item;
        $api->pldb [strtolower($name)] ["물품리스트"] [$data[0]] ["갯수"] = $data[1];
        $api->pldb [strtolower($name)] ["등록리스트"] [$data[0]] ["즉시구매가"] = $data[2];
        $api->pldb [strtolower($name)] ["등록리스트"] [$data[0]] ["최소경매가"] = $data[3];
        $api->pldb [strtolower($name)] ["등록리스트"] [$data[0]] ["현재경매가"] = $data[3];
        $api->pldb [strtolower($name)] ["등록리스트"] [$data[0]] ["경매자"] = "없음";
        $api->pldb [strtolower($name)] ["등록리스트"] [$data[0]] ["등록당시시간"] = date("YmdHis");
        $api->pldb [strtolower($name)] ["등록리스트"] [$data[0]] ["등록종료시간"] = date("YmdHis",strtotime ("+10 minutes"));
        $api->pldb [strtolower($name)] ["등록리스트"] [$data[0]] ["종료시간"] = date("Y년 m월 d일 H시 i분 s초",strtotime ("+10 minutes"));
        $api->getServer()->broadcastMessage( Market::TAG . "{$name} 님이 {$data[0]} 이름으로 시장에 아이템을 올렸습니다.");
        $player->sendMessage ( Market::TAG . "손에든 아이템을 시장에 등록했습니다." );
        return;
    }
}