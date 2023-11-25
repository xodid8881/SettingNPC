<?php
declare(strict_types=1);

namespace Market\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use Market\Market;

final class OPCommand extends Command{

    private Market $api;
    /**
     * @var string[]
     * @phpstan-var  array<string, string>
     */
    private array $chat = [];

    public function __construct(){
        parent::__construct('시장관리', '시장관리 명령어.', '/시장관리');
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
        $this->api = Market::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!$sender instanceof Player) return true;
        $name = $sender->getName();
        if (!$sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            $player->sendMessage( Market::TAG . "권한이 없습니다.");
            return true;
        } else {
            sleep(1);
            $this->api->MarketSettingTaskEvent($sender);
            return true;
        }
        return true;
    }
}