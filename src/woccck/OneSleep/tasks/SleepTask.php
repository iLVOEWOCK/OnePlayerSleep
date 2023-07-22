<?php

declare(strict_types=1);

namespace woccck\OneSleep\tasks;

use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use woccck\OneSleep\OnePlayerSleep;

class SleepTask extends Task {

    private OnePlayerSleep $plugin;

    private Player $player;

    public function __construct(OnePlayerSleep $plugin, Player $player) {
        $this->plugin = $plugin;
        $this->player = $player;
    }

    public function onRun() : void {
        if ($this->player->isSleeping()) {
            $onlinePlayers = count($this->plugin->getServer()->getOnlinePlayers());
            if ($onlinePlayers === 1) {
                $level = $this->player->getWorld();
                $level->setTime(0);
                $messages = $this->plugin->getConfig()->get("messages.morning", "§6Good morning! The sun rises on a new day. §r");
                $this->plugin->getServer()->broadcastMessage(TextFormat::colorize($messages));
                $this->player->stopSleep();
            } else {
                $this->player->setSpawn($this->player->getLocation());
                $sleepingmessage = $this->plugin->getConfig()->get("messages.sleeping", "&r&b{display_name} is sleeping...§r");
                $sleepingmessage = str_replace("{display_name}", $this->player->getDisplayName(), $sleepingmessage);
                $playersleepingmessage = $this->plugin->getConfig()->get("messages.player-slept", "&r&bSleeping...&r");
                $this->player->sendMessage(TextFormat::colorize($playersleepingmessage));
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
                    $p->sendTip(TextFormat::colorize($sleepingmessage));
                }

                $task = new SleepCheckTask($this->plugin, $this->player);
                $this->plugin->getScheduler()->scheduleDelayedTask($task, 100);

                $this->plugin->sleepTasks[$this->player->getName()] = $task;
            }
        } else {
            $this->plugin->getLogger()->warning("Player is detected as not sleeping. Server possibly lagging.");
        }
    }
}
