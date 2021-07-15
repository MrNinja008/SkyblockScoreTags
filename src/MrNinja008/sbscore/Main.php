<?php
declare(strict_types = 1);

namespace MrNinja008\sbscore;

use MrNinja008\sbscore\listeners\EventListener;
use MrNinja008\sbscore\listeners\TagResolveListener;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\ScoreHudSettings;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Process;
use pocketmine\Player;
use room17\SkyBlock\session\BaseSession as SkyBlockSession;
use room17\SkyBlock\island\RankIds;
use room17\SkyBlock\SkyBlock;
use function strval;

   class Main extends PluginBase{
     
     /** @var SkyBlock */
	private $SkyBlock;

	public function onEnable(){
	$this->saveDefaultConfig();
		$this->SkyBlock = $this->getServer()->getPluginManager()->getPlugin("SkyBlock");
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(int $_): void{
			foreach($this->getServer()->getOnlinePlayers() as $player){
				if(!$player->isOnline()){
					continue;
				}

				(new PlayerTagUpdateEvent($player, new ScoreTag("is.state", strval($this->getIsleState($player)))))->call();
				(new PlayerTagUpdateEvent($player, new ScoreTag("is.blocks", strval($this->getIsleBlocks($player)))))->call();
		 	  (new PlayerTagUpdateEvent($player, new ScoreTag("is.size", strval($this->getIsleSize($player)))))->call();
  	    (new PlayerTagUpdateEvent($player, new ScoreTag("is.members", strval($this->getIsleMembers($player)))))->call();
  		  (new PlayerTagUpdateEvent($player, new ScoreTag("is.rank", strval($this->getIsleRank($player)))))->call();
			}
		}), 20);
	}
	
	public function getIsleState(Player $player): string{
	  $session = $this->skyBlock->getSessionManager()->getSession($player);

		if($session === null){
			return "No Island";
		}

	  	$isle = $session->getIsland();

			return $isle->isLocked() ? "Locked" : "Unlocked";
  	}
  	
  	public function getIsleBlocks(Player $player){
			$session = $this->skyBlock->getSessionManager()->getSession($player);

			if($session === null){
				return "No Island";
			}

			$isle = $session->getIsland();

			return $isle->getBlocksBuilt();
		}
   
   public function getIsleMembers(Player $player): string{
			$session = $this->skyBlock->getSessionManager()->getSession($player);

			if($session === null){
				return "No Island";
			}

			$isle = $session->getIsland();

			return count($isle->getMembers());
		}

		public function getIsleSize(Player $player): string{
			$session = $this->skyBlock->getSessionManager()->getSession($player);

			if($session === null){
				return "No Island";
			}

			$isle = $session->getIsland();

			return $isle->getCategory();
		}
		
		public function getIsleRank(Player $player): string{
			$session = $this->skyBlock->getSessionManager()->getSession($player);

			if($session === null){
				return "No Island";
			}

			switch($session->getRank()){
				case RankIds::MEMBER:
					return "Member";
				case RankIds::OFFICER:
					return "Officer";
				case RankIds::LEADER:
					return "Leader";
				case RankIds::FOUNDER:
					return "Founder";
			}

			return "No Rank";
		}
	}