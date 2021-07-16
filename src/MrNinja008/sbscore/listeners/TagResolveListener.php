<?php

declare(strict_types = 1);

namespace MrNinja008\sbscore\listeners;

use Ifera\ScoreHud\event\TagsResolveEvent;
use MrNinja008\sbcore\Main;
use pocketmine\event\Listener;
use function count;
use function explode;
use function strval;

class TagResolveListener implements Listener{

	/** @var Main */	
        private $plugin;

	public function __construct(Main $plugin){

		$this->plugin = $plugin;

	}

	public function onTagResolve(TagsResolveEvent $event){
                $tag = $event->getTag();
		$tags = explode('.', $tag->getName(), 4);
		$value = "";

		if($tags[0] !== 'skyblock' || count($tags) < 4){

			return;

		}

		switch($tags[1]){

			case "state":
                        $value = $this->plugin->getIsleState($event->getPlayer());
                        break;

			case "blocks":
                        $value = $this->plugin->getIsleBlocks($event->getPlayer());
                        break;

			case "size":
                        value = $this->plugin->getIsleSize($event->getPlayer());
                        break;

			case "rank":
                        $value = $this->plugin->getIsleRank($event->getPlayer());
                        break;

		}

		$tag->setValue(strval($value));

	}

}
			
