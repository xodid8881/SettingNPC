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

final class SayTaskForm implements Form{

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
            'title' => '[ MarketManager ] | 구매',
            'content' => [
                [
                    'type' => 'dropdown',
                    'text' => '이용 방법',
                    "options" => ["즉시구매","경매참여"]
                ],
                [
                    'type' => 'input',
                    'text' => "§6-----------------------------\n\n§6등록자 §f=> §6{$playername}\n\n§6수량 §f=> §6{$count} §f개\n§6판매가 §f=> §6{$livemoney} §f원\n§6경매가 §f=> §6{$livetimemoney} §f원\n§6만료일 §f=> §6{$outtime}\n\n§6-----------------------------\n§6즉시구매§f를 이용하신다면 대문자 §6YES §f를 적어주세요.\n§6경매§f를 이용하신다면 경매가 보다 높게 금액을 적어주세요.\n§6-----------------------------"
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
            if ($data[1] != "YES"){
                $player->sendMessage( Market::TAG . '즉시 구매를 원하시면 이용이벤트 선택 후 YES 를 적어주세요.');
                return;
            }
            $Market = $api->pldb [strtolower($name)] ["이용이벤트"];
            $marketname = $api->marketdb ["물품리스트"] [$Market] ["물품이름"];
            $count = $api->marketdb ["물품리스트"] [$Market] ["갯수"];
            $livemoney = $api->marketdb ["물품리스트"] [$Market] ["즉시구매가"];
            $playername = $api->marketdb ["물품리스트"] [$Market] ["등록자이름"];

            $item = Item::jsonDeserialize($this->plugin->marketdb['물품리스트'][$Market]['nbt']);

            if (MoneyManager::getInstance ()->getMoney ($name) >= $livemoney){
                MoneyManager::getInstance ()->sellMoney ($name,$livemoney);
                MoneyManager::getInstance ()->addMoney ($playername,$livemoney);
                $this->plugin->GiveItem ($name,$marketname,$item->jsonSerialize ());
                unset($api->pldb [strtolower($playername)] ["등록리스트"] [$Market]);
                unset($api->marketdb ["물품리스트"] [$Market]);
                $player->sendMessage ( Market::TAG . "{$Market} 이름의 물품을 {$livemoney} 원을 주고 구매했습니다.");
                return;
            } else {
                $player->sendMessage ( Market::TAG . "구매하기에 보유한 돈이 부족합니다.");
                return;
            }
            break;
            case 1 :
            if (! is_numeric ($data[1])) {
                $player->sendMessage ( Market::TAG . "숫자를 이용 해야됩니다. " );
                return;
            }
            $Market = $api->pldb [strtolower($name)] ["이용이벤트"];
            $marketname = $api->marketdb ["물품리스트"] [$Market] ["물품이름"];
            $count = $api->marketdb ["물품리스트"] [$Market] ["갯수"];
            $livemoney = $api->marketdb ["물품리스트"] [$Market] ["즉시구매가"];
            $timemoney = $api->marketdb ["물품리스트"] [$Market] ["최소경매가"];
            $livetimemoney = $api->marketdb ["물품리스트"] [$Market] ["현재경매가"];
            $playername = $api->marketdb ["물품리스트"] [$Market] ["등록자이름"];


            if ($livemoney <= $data[1]){
                MoneyManager::getInstance ()->sellMoney ($name,$livemoney);
                MoneyManager::getInstance ()->addMoney ($playername,$livemoney);
                $item = Item::nbtDeserialize($this->serializer->read($api->marketdb['물품리스트'][$Market]['nbt'])->mustGetCompoundTag());
                $this->plugin->GiveItem ($name,$marketname,$item->jsonSerialize ());
                unset($api->pldb [strtolower($playername)] ["등록리스트"] [$Market]);
                unset($api->marketdb ["물품리스트"] [$Market]);
                $player->sendMessage ( Market::TAG . "적으신 경매가가 즉시 구매가와 같아 거래가 채결됬습니다.");
                $player->sendMessage ( Market::TAG . "당신은 {$Market} 이름의 물품을 즉시 구매가로 {$livemoney} 원을 주고 구매했습니다.");
                return;
            }
            if ($livetimemoney >= $data[1]){
                $player->sendMessage ( Market::TAG . "현재 경매가보다 낮습니다. 현재 경매가 => {$livetimemoney} 원");
                return;
            }
            if (MoneyManager::getInstance ()->getMoney ($name) >= $data[1]){
                $api->marketdb ["물품리스트"] [$Market] ["경매자"] = $name;
                $api->marketdb ["물품리스트"] [$Market] ["현재경매가"] = $data[1];
                return
            } else {
                $player->sendMessage ( Market::TAG . "경매 신청한 금액보다 보유한 돈이 부족합니다.");
                return;
            }
            break;
        }
    }
}