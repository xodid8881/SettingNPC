<?php
declare(strict_types=1);

namespace Market\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use Market\Market;

final class PlayerCommand extends Command{

    private Market $api;

    /**
     * @var string[]
     * @phpstan-var  array<string, string>
     */
    private array $chat = [];

    public function __construct(){
        parent::__construct('시장등록', '시장등록 명령어.', '/시장등록');
        $this->setPermission(DefaultPermissions::ROOT_USER);
        $this->api = Market::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!$sender instanceof Player) return true;
        $name = $sender->getName();
        sleep(1);
        $this->api->MarketPlayerUploadTaskEvent($sender);
        return true;
    }
}