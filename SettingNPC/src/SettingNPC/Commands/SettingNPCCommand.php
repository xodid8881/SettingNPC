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
            $player->sendMessage( SettingNPC::TAG . "권한이 없습니다.");
            return true;
        } else {
            sleep(1);
            $this->api->CreateEvent ($sender);
            return true;
        }
        return true;
    }
}
