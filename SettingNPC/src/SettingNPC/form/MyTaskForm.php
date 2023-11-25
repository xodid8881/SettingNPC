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

final class MyTaskForm implements Form{

    private static BigEndianNbtSerializer $serializer;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->serializer = new BigEndianNbtSerializer();
    }

    public function jsonSerialize() : array{
        $name = $this->player->getName ();
        $api = Market::getInstance();
        $Market = $this->pldb [strtolower($name)] ["이용이벤트"];
        $count = $this->marketdb ["물품리스트"] [$Market] ["갯수"];
        $livemoney = $this->marketdb ["물품리스트"] [$Market] ["즉시구매가"];
        $timemoney = $this->marketdb ["물품리스트"] [$Market] ["최소경매가"];
        $livetimemoney = $this->marketdb ["물품리스트"] [$Market] ["현재경매가"];
        $playername = $this->marketdb ["물품리스트"] [$Market] ["등록자이름"];
        $time = $this->marketdb ["물품리스트"] [$Market] ["종료시간"];


        return [
            'type' => 'form',
            'title' => '[ MarketManager ]',
            'content' => "아래 정보의 물품을 관리합니다.\n\n§6-----------------------------\n\n§6물품이름 §f=> §6{$Market}\n\n§6등록자 §f=> §6{$playername}\n\n§6수량 §f=> §6{$count} §f개\n§6판매가 §f=> §6{$livemoney} §f원\n§6경매가 §f=> §6{$livetimemoney} §f원\n§6등록만료 §f=> §6{$time}\n\n§6-----------------------------\n",
            'buttons' => [
                [
                    'text' => '등록 제거'
                ],
                [
                    'text' => '등록기간 연장'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        if($data === null) return;
        $name = $player->getName();
        $api = Market::getInstance();
        if ($data === 0) {
            $Market = $api->pldb [strtolower($name)] ["이용이벤트"];
            $marketname = $api->marketdb ["물품리스트"] [$Market] ["물품이름"];
            $item = Item::nbtDeserialize($this->serializer->read($api->marketdb['물품리스트'][$Market]['nbt'])->mustGetCompoundTag());

            $upname = $api->marketdb ["물품리스트"] [$Market] ["등록자이름"];

            $api->GiveItem ($upname,$marketname,$item);

            unset($api->pldb [strtolower($upname)] ["등록리스트"] [$Market]);
            unset($api->marketdb ["물품리스트"] [$Market]);
            $player->sendMessage ( Market::TAG . "{$Market} 이름인 물품의 등록을 취소했습니다.");
            return;
        }
        if ($data === 1) {
            $Market = $api->pldb [strtolower($name)] ["이용이벤트"];
            $marketname = $api->marketdb ["물품리스트"] [$Market] ["물품이름"];

            $api->marketdb ["물품리스트"] [$Market] ["등록종료시간"] = date("YmdHis",strtotime ("+10 minutes"));
            $api->marketdb ["물품리스트"] [$Market] ["종료시간"] = date("Y년 m월 d일 H시 i분 s초",strtotime ("+10 minutes"));
            $api->pldb [strtolower($name)] ["등록리스트"] [$Market] ["등록종료시간"] = date("YmdHis",strtotime ("+10 minutes"));
            $api->pldb [strtolower($name)] ["등록리스트"] [$Market] ["종료시간"] = date("Y년 m월 d일 H시 i분 s초",strtotime ("+10 minutes"));

            $player->sendMessage ( Market::TAG . "{$Market} 이름의 물품의 등록기간을 연장했습니다.");
            return;
        }
    }
}