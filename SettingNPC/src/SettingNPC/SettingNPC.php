<?php

declare(strict_types=1);

namespace Market;

use JsonException;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
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
