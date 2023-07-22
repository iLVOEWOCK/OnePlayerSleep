<?php

declare(strict_types=1);

namespace woccck\OneSleep\tasks;

use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use woccck\OneSleep\OnePlayerSleep;

class SleepCheckTask extends Task {

    private OnePlayerSleep $plugin;
    private Player $player;

    public function __construct(OnePlayerSleep $plugin, Player $player) {
        $this->plugin = $plugin;
        $this->player = $player;
    }

    public function onRun() : void {
        $config = $this->plugin->getConfig();
        if ($this->player->isSleeping()) {
            $this->player->getWorld()->setTime(0);
            $day = (int)($this->plugin->getServer()->getTick() / 24000);
            $playerskippednight = $config->get("messages.player-skipped-night", "&r&7{display_name} has skipped the night. Good morning!");
            $playerskippednight = str_replace("{display_name}", $this->player->getDisplayName(), $playerskippednight);
            $this->plugin->getServer()->broadcastMessage(TextFormat::colorize($playerskippednight));
            $this->player->stopSleep();
        } else {
            // Check if day
            $level = $this->player->getWorld();
            $time = $level->getTime();
            $isDaytime = ($time >= 0 && $time < 12000) || ($time >= 24000 && $time < 36000);
            $day = (int)($this->plugin->getServer()->getTick() / 24000);
            $this->plugin->getLogger()->info((string) $isDaytime);
            if ($isDaytime) {
                $playerskippednight = $config->get("messages.player-skipped-night", "&r&7{display_name} has skipped the night. Good morning!");
                $playerskippednight = str_replace("{display_name}", $this->player->getDisplayName(), $playerskippednight);
                $this->plugin->getServer()->broadcastMessage(TextFormat::colorize($playerskippednight));
            } else {
                $playerleftbed = $config->get("messages.player-left-before-morning", "&r&cYou have left your bed before morning.");
                $this->player->sendMessage(TextFormat::colorize($playerleftbed));
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    $tipplayerleft = $config->get("messages.player-left-bed", "&r&c{display_name} has left their bed before morning.&r");
                    $tipplayerleft = str_replace("{display_name}", $this->player->getDisplayName(), $tipplayerleft);
                    $p->sendTip(TextFormat::colorize($tipplayerleft));
                }
            }
        }
        unset($this->plugin->sleepTasks[$this->player->getName()]);
    }
}
