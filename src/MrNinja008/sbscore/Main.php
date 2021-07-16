<?php
declare(strict_types = 1);

namespace MrNinja008\sbscore;

use MrNinja008\sbscore\listeners\TagResolveListener;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
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
	private $owningPlugin;

	public function onEnable(){
	$this->saveDefaultConfig();
		$this->owningPlugin = $this->getServer()->getPluginManager()->getPlugin("SkyBlock");
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(int $_): void{
			foreach($this->getServer()->getOnlinePlayers() as $player){
				if(!$player->isOnline()){
					continue;
				}

				(new PlayerTagUpdateEvent($player, new ScoreTag("skyblock.state", strval($this->getIsleState($player)))))->call();
				(new PlayerTagUpdateEvent($player, new ScoreTag("skyblock.blocks", strval($this->getIsleBlocks($player)))))->call();
		 	  (new PlayerTagUpdateEvent($player, new ScoreTag("skyblock.size", strval($this->getIsleSize($player)))))->call();
  		  (new PlayerTagUpdateEvent($player, new ScoreTag("skyblock.rank", strval($this->getIsleRank($player)))))->call();
			}
		}), 20);
	}
	
	public function getIsleState(Player $player): string{
	  $session = $this->$owningPlugin->getSessionManager()->getSession($player);

		if((is_null($session)) || (!$session->hasIsland())){
				return "No Island";
		}

	  	$isle = $session->getIsland();

			return $isle->isLocked() ? "Locked" : "Unlocked";
  	}
  	
  	public function getIsleBlocks(Player $player){
			$session = $this->owningPlugin->getSessionManager()->getSession($player);

			if((is_null($session)) || (!$session->hasIsland())){
				return "No Island";
			}

			$isle = $session->getIsland();

			return $isle->getBlocksBuilt();
		}
   
   
		public function getIsleSize(Player $player){
			$session = $this->owningPlugin->getSessionManager()->getSession($player);

			if((is_null($session)) || (!$session->hasIsland())){
				return "No Island";
			}

			$isle = $session->getIsland();

			return $isle->getCategory();
		}
		
		public function getIsleRank(Player $player): string{
			$session = $this->owningPlugin->getSessionManager()->getSession($player);

			if((is_null($session)) || (!$session->hasIsland())){
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
