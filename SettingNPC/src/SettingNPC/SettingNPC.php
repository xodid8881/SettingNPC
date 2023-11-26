<?php

declare(strict_types=1);

namespace SettingNPC;

use JsonException;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;

use pocketmine\permission\DefaultPermissions;

use pocketmine\entity\Human;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Entity;

use SettingNPC\form\CreateForm;

use function explode;

final class SettingNPC{
    use SingletonTrait;

    public array $npcdb;


    private BigEndianNbtSerializer $serializer;

    public function __construct(
        private readonly Config $npc,
    ){
        self::setInstance($this);
        $this->npcdb = $this->npc->getAll();
        $this->serializer = new BigEndianNbtSerializer();
    }

    /**
     * @throws JsonException
     */
    public function save() : void{
        $this->npc->setAll($this->npcdb);
        $this->npc->save();
    }
    
    private $chat;
    public const TAG = "§c【 §fSettingNPC §c】 §7: ";

    public function EntitySpawn($player,$npcname, $command){
        $pos = $player->getPosition();
        $loc = $player->getLocation();
        $loc = new Location($pos->getFloorX() + 0.5, $pos->getFloorY() + 0.05, $pos->getFloorZ() + 0.5,
        $pos->getWorld(), $loc->getYaw(), $loc->getPitch());
        $npc = new Human($loc, $player->getSkin());
        $npc->setNameTag($npcname);
        $npc->setNameTagAlwaysVisible();
        $npc->spawnToAll();
        $this->npcdb [$npcname] ["Command"] = $command;
        $this->npcdb [$npcname] ["Permissions"] = "ROOT_OPERATOR";
        /*
        ROOT_OPERATOR OP
        ROOT_USER USER
        */
        $this->npcdb [$npcname] ["CoolTime"] = "false";
        $this->npcdb [$npcname] ["Cool"] = 0;
        return true;
    }

    public function CreateEvent (Player $player) : void{
        Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use($player) : void {
            if($player->isOnline()) {
                $this->CreateUI($player);
            }
        }), 20);
    }

    public function CreateUI(Player $player) : void{
        $player->sendForm(new CreateForm());
    }
}
