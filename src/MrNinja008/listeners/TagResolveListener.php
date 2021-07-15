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
		$tags = explode('.', $tag->getName(), 5);
		$value = "";

		if($tags[0] !== 'is' || count($tags) < 5){
			return;
		}

		switch($tags[1]){
			case "state":
				$value = $this->getIsleBlock($event->getPlayer());
			break;

			case "blocks":
				$value = $this->getIsleBlocks($event->getPlayer());
			break;
			
			case "size":
			  $value = $this->getIsleSize($event->getPlayer());
			break;
			
			case "members":
			  $value = $this->getIsleMembers($event->getPlayer());
			break;
			
			case "rank":
			  $value = $this->getIsleRank($event->getPlayer());
			break;
		}

		$tag->setValue(strval($value));
	}
}
