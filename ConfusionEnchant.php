<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyCustomEnchants\enchants\tools\axes;

use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchantIds;
use DaPigGuy\PiggyCustomEnchants\enchants\ReactiveEnchantment;
use DaPigGuy\PiggyCustomEnchants\PiggyCustomEnchants;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Event;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;
use ReflectionException;

class ConfusionEnchant extends ReactiveEnchantment
{
    /** @var int */
    public $maxLevel = 3;
    /** @var int */
    public $itemType = CustomEnchant::ITEM_TYPE_AXE;
    /** @var array */
    private $effectIds = [Effect::NAUSEA];
    /** @var array */
    private $baseDuration = [0];
    /** @var array */
    private $baseAphlifier = [0];
    /** @var int[] */
    private $durationMultiplier = [60];
    /** @var int[] */
    private $aphlifierMultiplier = [1];

    /**
     * @throws ReflectionException
     */
    public function __construct(PiggyCustomEnchants $plugin, int $id, string $name, int $rarity = self::RARITY_SIMPLE, array $effectIds = [Effect::NAUSEA], array $durationMultiplier = [60], array $aphlifierMultiplier = [1], array $baseDuration = [0], array $baseAphlifier = [0])
    {
        $this->name = $name;
        $this->id = $id;
        $this->rarity = $rarity;
        $this->effectIds = $effectIds;
        $this->durationMultiplier = $durationMultiplier;
        $this->aphlifierMultiplier = $aphlifierMultiplier;
        $this->baseDuration = $baseDuration;
        $this->baseAphlifier = $baseAphlifier;
        parent::__construct($plugin, $id);
    }

    public function getDefaultExtraData(): array
    {
        return ["durationMultiplier" => $this->durationMultiplier, "aphlifierMultiplier" => $this->aphlifierMultiplier, "baseDuration" => $this->baseDuration, "baseAphlifier" => $this->baseAphlifier];
    }

    public function react(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            $entity = $event->getEntity();
            if ($entity instanceof Living) {
                foreach ($this->effectIds as $key => $effectId) {
                    $entity->addEffect(new EffectInstance(Effect::getEffect($effectId), ($this->extraData["baseDuration"][$key] ?? 0) + ($this->extraData["durationMultiplier"][$key] ?? 60) * $level, ($this->extraData["baseAphlifier"][$key] ?? 0) + ($this->extraData["aphlifierMultiplier"][$key] ?? 1) * $level));
                }
                $player->sendMessage("§b*§d*§b* §7Inflicted Confusion! §8(§7Confusion Effect§8) §b*§d*§b*");
		        $event->getEntity()->sendMessage("§c*§6*§c* §cThe Enemy Has Inflicted Insomnia on You! §8(§7Confusion Effect§8) §c*§6*§c*");
			}
		}
	}
}