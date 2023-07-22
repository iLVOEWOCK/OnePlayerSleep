<?php

declare(strict_types=1);

namespace woccck\OneSleep;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use woccck\OneSleep\tasks\SleepTask;

class OnePlayerSleep extends PluginBase implements Listener {

    public array $sleepTasks = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param PlayerBedEnterEvent $event
     * @priority HIGHEST
     */
    public function onPlayerBedEnter(PlayerBedEnterEvent $event): void {
        $player = $event->getPlayer();

        $this->getScheduler()->scheduleDelayedTask(new SleepTask($this, $player), 5);
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
