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

final class WareTaskForm implements Form{

    private static BigEndianNbtSerializer $serializer;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->serializer = new BigEndianNbtSerializer();
    }

    public function jsonSerialize() : array{
        $name = $this->player->getName ();
        $api = Market::getInstance();
        $Market = $api->pldb [strtolower($name)] ["이용이벤트"];
        $marketname = $api->marketdb ["물품리스트"] [$Market] ["물품이름"];
        $count = $api->marketdb ["물품리스트"] [$Market] ["갯수"];
        $livemoney = $api->marketdb ["물품리스트"] [$Market] ["즉시구매가"];
        $timemoney = $api->marketdb ["물품리스트"] [$Market] ["최소경매가"];
        $livetimemoney = $api->marketdb ["물품리스트"] [$Market] ["현재경매가"];
        $outtime = $api->marketdb ["물품리스트"] [$Market] ["종료시간"];
        $playername = $api->marketdb ["물품리스트"] [$Market] ["등록자이름"];

        return [
            'type' => 'custom_form',
            'title' => '[ MarketManager ] | 신고도우미',
            'content' => [
                [
                    'type' => 'dropdown',
                    'text' => '이용 방법',
                    "options" => ["금액신고","아이템신고"]
                ],
                [
                    'type' => 'input',
                    'text' => "빈칸에는 신고내용을 적어주세요.\n\n아래 정보의 물품을 신고합니다.\n\n\n§6-----------------------------\n\n§6등록자 §f=> §6{$playername}\n\n§6수량 §f=> §6{$count} §f개\n§6판매가 §f=> §6{$livemoney} §f원\n§6경매가 §f=> §6{$livetimemoney} §f원\n§6만료일 §f=> §6{$outtime}\n\n§6-----------------------------\n"
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        if($data === null) return;
        $name = $player->getName();
        $api = Market::getInstance();
        if (!isset($data[1])) {
            $player->sendMessage( Market::TAG . '빈칸을 채워주세요.');
            return;
        }
        switch($data[0]){
            case 0 :

            $Market = $api->pldb [strtolower($name)] ["이용이벤트"];
            $marketname = $api->marketdb ["물품리스트"] [$Market] ["물품이름"];
            $count = $api->marketdb ["물품리스트"] [$Market] ["갯수"];
            $livemoney = $api->marketdb ["물품리스트"] [$Market] ["즉시구매가"];
            $timemoney = $api->marketdb ["물품리스트"] [$Market] ["최소경매가"];
            $livetimemoney = $api->marketdb ["물품리스트"] [$Market] ["현재경매가"];
            $playername = $api->marketdb ["물품리스트"] [$Market] ["등록자이름"];

            $api->marketdb ["신고물품"] [$Market] ["물품이름"] = $marketname;
            $api->marketdb ["신고물품"] [$Market] ["등록자이름"] = $playername;
            $api->marketdb ["신고물품"] [$Market] ["신고자"] = $name;
            $api->marketdb ["신고물품"] [$Market] ['id'] = $api->marketdb ["물품리스트"] [$Market] ['id'];
            $api->marketdb ["신고물품"] [$Market] ['dmg'] = $api->marketdb ["물품리스트"] [$Market] ['id'];
            $api->marketdb ["신고물품"] [$Market] ['nbt'] = $api->marketdb ["물품리스트"] [$Market] ['id'];
            $api->marketdb ["신고물품"] [$Market] ["갯수"] = $count;
            $api->marketdb ["신고물품"] [$Market] ["즉시구매가"] = $livemoney;
            $api->marketdb ["신고물품"] [$Market] ["최소경매가"] = $timemoney;
            $api->marketdb ["신고물품"] [$Market] ["현재경매가"] = $livetimemoney;
            $api->marketdb ["신고물품"] [$Market] ["신고위치"] = "금액";
            $api->marketdb ["신고물품"] [$Market] ["신고글"] = $data[1];
            $api->save ();
            break;
            case 1 :
            $Market = $api->pldb [strtolower($name)] ["이용이벤트"];
            $marketname = $api->marketdb ["물품리스트"] [$Market] ["물품이름"];
            $count = $api->marketdb ["물품리스트"] [$Market] ["갯수"];
            $livemoney = $api->marketdb ["물품리스트"] [$Market] ["즉시구매가"];
            $timemoney = $api->marketdb ["물품리스트"] [$Market] ["최소경매가"];
            $livetimemoney = $api->marketdb ["물품리스트"] [$Market] ["현재경매가"];
            $playername = $api->marketdb ["물품리스트"] [$Market] ["등록자이름"];

            $api->marketdb ["신고물품"] [$Market] ["물품이름"] = $marketname;
            $api->marketdb ["신고물품"] [$Market] ["등록자이름"] = $playername;
            $api->marketdb ["신고물품"] [$Market] ["신고자"] = $name;
            $api->marketdb ["신고물품"] [$Market] ['id'] = $api->marketdb ["물품리스트"] [$Market] ['id'];
            $api->marketdb ["신고물품"] [$Market] ['dmg'] = $api->marketdb ["물품리스트"] [$Market] ['id'];
            $api->marketdb ["신고물품"] [$Market] ['nbt'] = $api->marketdb ["물품리스트"] [$Market] ['id'];
            $api->marketdb ["신고물품"] [$Market] ["갯수"] = $count;
            $api->marketdb ["신고물품"] [$Market] ["즉시구매가"] = $livemoney;
            $api->marketdb ["신고물품"] [$Market] ["최소경매가"] = $timemoney;
            $api->marketdb ["신고물품"] [$Market] ["현재경매가"] = $livetimemoney;
            $api->marketdb ["신고물품"] [$Market] ["신고위치"] = "아이템";
            $api->marketdb ["신고물품"] [$Market] ["신고글"] = $data[1];
            $api->save ();
            break;
        }
    }
}