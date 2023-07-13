<?php

declare(strict_types=1);

namespace woccck\OneSleep;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class OnePlayerSleep extends PluginBase implements Listener {

    private array $sleepTasks = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param PlayerBedEnterEvent $event
     * @priority HIGHEST
     */
    public function onPlayerBedEnter(PlayerBedEnterEvent $event): void {
        $player = $event->getPlayer();

        $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $event): void {
            if ($player->isSleeping()) {
                $onlinePlayers = count($this->getServer()->getOnlinePlayers());
                if ($onlinePlayers === 1) {
                    $player->getWorld()->setTime(0);

                    $day = (int)($this->getServer()->getTick() / 24000);
                    $this->getServer()->broadcastMessage("§6Good morning! The sun rises on a new day. Day: $day §r");
                    $player->stopSleep();
                } else {
                    // If more than one player is online, start a sleep task for the player
                    $event->cancel();
                    $player->setSpawn($player->getLocation());
                    $player->sendMessage("§bSleeping...§r");
                    // Send action bar to all players
                    foreach ($this->getServer()->getOnlinePlayers() as $p) {
                        $p->sendTip("§b" . $player->getDisplayName() . " is sleeping...§r");
                    }
                    $task = $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $event): void {
                        if ($player->isSleeping()) {
                            $player->getWorld()->setTime(0);
                            $day = (int)($this->getServer()->getTick() / 24000);
                            $player->stopSleep();
                            $this->getServer()->broadcastMessage("§6" . $player->getDisplayName() . " has skipped the night. Good morning! Day: $day §r");
                        } else {
                            // Check if day
                            $level = $player->getWorld();
                            $time = $level->getTime();
                            $isDaytime = ($time >= 0 && $time < 12000) || ($time >= 24000 && $time < 36000);
                            $day = (int)($this->getServer()->getTick() / 24000);
                            $this->getLogger()->info((string)$isDaytime);
                            if ($isDaytime) {
                                $this->getServer()->broadcastMessage("§6" . $player->getDisplayName() . " has skipped the night. Good morning! Day: $day §r");
                            } else {
                                $player->sendMessage("§cYou have left your bed before morning.");
                                // Send action bar to all players
                                foreach ($this->getServer()->getOnlinePlayers() as $p) {
                                    $p->sendTip("§c" . $player->getDisplayName() . " has left their bed before morning.§r");
                                }
                            }
                        }
                        unset($this->sleepTasks[$player->getName()]);
                    }), 100); 
                    $this->sleepTasks[$player->getName()] = $task;
                }
            } else {
                $this->getLogger()->warning("Player is detected as not sleeping. Server possibly lagging.");
            }
        }), 5);
    }

    /**
     * @param PlayerQuitEvent $event
     * @priority HIGHEST
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        if (isset($this->sleepTasks[$player->getName()])) {
            $this->sleepTasks[$player->getName()]->cancel();
            unset($this->sleepTasks[$player->getName()]);
        }
    }
}
