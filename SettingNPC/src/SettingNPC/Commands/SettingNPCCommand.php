<?php
declare(strict_types=1);

namespace SettingNPC\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use SettingNPC\SettingNPC;

final class SettingNPCCommand extends Command{

    private SettingNPC $api;
    /**
     * @var string[]
     * @phpstan-var  array<string, string>
     */
    private array $chat = [];

    public function __construct(){
        parent::__construct('커스텀엔피시', '커스텀엔피시 명령어.', '/커스텀엔피시');
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
        $this->api = SettingNPC::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!$sender instanceof Player) return true;
        $name = $sender->getName();
        if (!$sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            $player->sendMessage(SettingNPC::TAG . "권한이 없습니다.");
            return true;
        }
        if( ! isset($args[0] )){
            $sender->sendMessage (SettingNPC::TAG);
            $sender->sendMessage (SettingNPC::TAG."/커스텀엔피시 생성 < 커스텀 엔피시를 생성합니다. >");
            $sender->sendMessage (SettingNPC::TAG."/커스텀엔피시 제거 < 커스텀 엔피시를 제거합니다. >");
            $sender->sendMessage (SettingNPC::TAG."/커스텀엔피시 수정 < 커스텀 엔피시를 수정합니다. >");
            $sender->sendMessage (SettingNPC::TAG."/커스텀엔피시 목록 < 커스텀 엔피시를 생성합니다. >");
            return true;
        }
        switch ($args [0]) {
            case "생성" :
            if (! isset ( $this->chat [$name] )) {
                $this->api->CreateEvent ($sender);
                $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                return true;
            }
            if (date("YmdHis") - $this->chat [$name] < 3) {
                $sender->sendMessage (SettingNPC::TAG . "이용 쿨타임이 지나지 않아 불가능합니다.");
                return true;
            } else {
                $this->api->CreateEvent ($sender);
                $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                return true;
            }
            break;
            case "제거" :
            if (! isset ( $this->chat [$name] )) {
                $this->api->DeleteEvent ($sender);
                $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                return true;
            }
            if (date("YmdHis") - $this->chat [$name] < 3) {
                $sender->sendMessage (SettingNPC::TAG . "이용 쿨타임이 지나지 않아 불가능합니다.");
                return true;
            } else {
                $this->api->DeleteEvent ($sender);
                $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                return true;
            }
            break;
            case "수정" :
            if (! isset ( $this->chat [$name] )) {
                $this->api->SettingListEvent ($sender);
                $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                return true;
            }
            if (date("YmdHis") - $this->chat [$name] < 3) {
                $sender->sendMessage (SettingNPC::TAG . "이용 쿨타임이 지나지 않아 불가능합니다.");
                return true;
            } else {
                $this->api->SettingListEvent ($sender);
                $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
                return true;
            }
            break;
            case "목록" :
            foreach($this->api->npcdb as $npcname => $v){
                if (isset($this->api->npcdb [$npcname])){
                    $sender->sendMessage(SettingNPC::TAG . $npcname);
                }
            }
            $sender->sendMessage(SettingNPC::TAG . "상점들이 존재합니다.");
            break;
        }
    }
}
