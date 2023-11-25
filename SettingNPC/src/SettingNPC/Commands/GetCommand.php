<?php
declare(strict_types=1);

namespace Market\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use Market\Market;

final class GetCommand extends Command{

    private Market $api;

    /**
     * @var string[]
     * @phpstan-var  array<string, string>
     */
    private array $chat = [];

    public function __construct(){
        parent::__construct('시장', '시장 명령어.', '/시장');
        $this->setPermission(DefaultPermissions::ROOT_USER);
        $this->api = Market::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!$sender instanceof Player) return true;
        $name = $sender->getName();
        if (! isset ( $this->chat [$name] )) {
            $name = $sender->getName ();
            $this->api->pldb [strtolower($name)] ["페이지"] = 1;
            sleep(1);
            $this->api->MarketEvent($sender);
            $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
            return true;
        }
        if (date("YmdHis") - $this->chat [$name] < 3) {
            $sender->sendMessage ( Farm::TAG . "이용 쿨타임이 지나지 않아 불가능합니다." );
            return true;
        } else {
            $name = $sender->getName ();
            $this->api->pldb [strtolower($name)] ["페이지"] = 1;
            sleep(1);
            $this->api->MarketEvent($sender);
            $this->chat [$name] = date("YmdHis",strtotime ("+3 seconds"));
            return true;
        }
    }
}