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

final class OpWareTaskForm implements Form{

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
        $count = $api->marketdb ["신고물품"] [$Market] ["갯수"];
        $livemoney = $api->marketdb ["신고물품"] [$Market] ["즉시구매가"];
        $timemoney = $api->marketdb ["신고물품"] [$Market] ["최소경매가"];
        $livetimemoney = $api->marketdb ["신고물품"] [$Market] ["현재경매가"];
        $playername = $api->marketdb ["신고물품"] [$Market] ["등록자이름"];
        $wareplayer = $api->marketdb ["신고물품"] [$Market] ["신고자"];
        $xyz = $api->marketdb ["신고물품"] [$Market] ["신고위치"];
        $message = $api->marketdb ["신고물품"] [$Market] ["신고글"];

        return [
            'type' => 'custom_form',
            'title' => '[ MarketManager ] | 신고관리',
            'content' => [
                [
                    'type' => 'dropdown',
                    'text' => '이용 방법',
                    "options" => ["신고체결","신고무효화"]
                ],
                [
                    'type' => 'input',
                    'text' => "신고체결을 하실 경우 지급할 경고의 정도를 적어주세요.\n\n신고무효화를 하실 경우 YES 를 적어주세요.\n\n아래 정보의 물품의 신고를 관리합니다.\n\n\n§6-----------------------------\n\n§6{$wareplayer} §f님이 §6{$xyz} §6의 문제로 신고했습니다.\n\n§6신고자의 메세지 §f=> §6{$message}\n\n§6등록자 §f=> §6{$playername}\n\n§6수량 §f=> §6{$count} §f개\n§6판매가 §f=> §6{$livemoney} §f원\n§6경매가 §f=> §6{$livetimemoney} §f원\n\n§6-----------------------------\n"
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        if($data === null) return;
        $name = $player->getName();
        $api = Market::getInstance();
        if (!isset($data[1])) {
            $player->sendMessage( $api->tag() . '빈칸을 채워주세요.');
            return;
        }
        switch($data[0]){
            case 0 :
            if (! is_numeric ($data[0])) {
                $player->sendMessage ( $api->tag() . "숫자를 이용 해야됩니다. " );
                return;
            }
            $Market = $api->pldb [strtolower($name)] ["이용이벤트"];
            $playername = $api->marketdb ["신고물품"] [$Market] ["등록자이름"];
            WarningManager::getInstance ()->addWarningPoint ($playername,$data[0],"시장 물품경고");
            $api->pldb [strtolower($playername)] ["차단기록"] = date("YmdHis",strtotime ("+20 minutes"));
            $api->pldb [strtolower($playername)] ["차단종료기간"] = date("Y년 m월 d일 H시 i분 s초",strtotime ("+20 minutes"));
            unset ($api->marketdb ["신고물품"] [$Market] ["물품이름"]);
            $player->sendMessage ( $api->tag() . "신고를 체결하고 등록자에게 {$data[0]} 개의 경고를 부여했습니다." );
            if (isset($api->marketdb ["물품리스트"] [$Market])) {
                $item = Item::nbtDeserialize($this->serializer->read($api->marketdb['신고물품'][$Market]['nbt'])->mustGetCompoundTag());
                $api->BackGiveItem ($playername,$Market,$item);
                unset ($api->marketdb ["물품리스트"] [$Market]);
            }
            if (isset($api->pldb [strtolower($playername)] ["등록리스트"] [$Market])){
                unset($api->pldb [strtolower($playername)] ["등록리스트"] [$Market]);
            }
            break;
            case 1 :
            if ($data[0] != "YES") {
                $player->sendMessage ( Market::TAG . "YES 를 정확하게 입력해주세요." );
                return;
            }
            $Market = $api->pldb [strtolower($name)] ["이용이벤트"];
            unset ($api->marketdb ["신고물품"] [$Market] ["물품이름"]);
            $player->sendMessage ( Market::TAG . "해당 신고를 무효화 시켰습니다." );
            break;
        }
    }
}