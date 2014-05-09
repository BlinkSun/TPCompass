<?php

/*
__PocketMine Plugin__
name=TPCompass
description=Tap there and there you are !
version=0.6
author=BlinkSun
class=TPCompass
apiversion=11,12
*/

class TPCompass implements Plugin {
	private $api;

	public function __construct(ServerAPI $api, $server = false) {
		$this->api = $api;
	}
	
	public function init() {
		AchievementAPI::addAchievement("tpcompass","Tap there and there you are !");
		$this->api->addHandler("player.action", array($this, "eventHandle"), 50);
	}
	
	public function eventHandle($data, $event) {
		switch ($event) {
			case "player.action":
				$player = $data["player"];
				$item = $data["item"];
				if($item == 345) {
					console("compass");
					//*****************************************************************
					//**** This snippet come from src\Player.php line 1701 to 1739 ****
					//*****************************************************************
					$rotation = ($player->entity->yaw - 90) % 360;
					if($rotation < 0){
						$rotation = (360 + $rotation);
					}
					$rotation = ($rotation + 180);
					if($rotation >= 360){
						$rotation = ($rotation - 360);
					}
					$X = 1;
					$Z = 1;
					$overturn = false;
					if(0 <= $rotation and $rotation < 90){
						
					}elseif(90 <= $rotation and $rotation < 180){
						$rotation -= 90;
						$X = (-1);
						$overturn = true;
					}elseif(180 <= $rotation and $rotation < 270){
						$rotation -= 180;
						$X = (-1);
						$Z = (-1);
					}elseif(270 <= $rotation and $rotation < 360){
						$rotation -= 270;
						$Z = (-1);
						$overturn = true;
					}
					$rad = deg2rad($rotation);
					$pitch = (-($player->entity->pitch));
					$matY = (sin(deg2rad($pitch))); // * $speed);
					$matXZ = (cos(deg2rad($pitch))); // * $speed);
					if($overturn){
						$matX = (sin($rad) * $matXZ * $X);
						$matZ = (cos($rad) * $matXZ * $Z);
					}
					else{
						$matX = (cos($rad) * $matXZ * $X);
						$matZ = (sin($rad) * $matXZ * $Z);
					}
					//*****************************************************************
					//*****************************************************************
					//*****************************************************************
					$checkX = $player->entity->x;
					$checkY = $player->entity->y + 1;
					$checkZ = $player->entity->z;
					console("playerxyz: " .$player->entity->x." ".$player->entity->y." ".$player->entity->z);
					console("matrixxyz: " .$matX." ".$matY." ".$matZ);
					while($player->level->getBlock(new Vector3((int) $checkX, (int) $checkY, (int) $checkZ))->isSolid !== true) {
						$checkX = $checkX + $matX;
						$checkY = $checkY + $matY;
						$checkZ = $checkZ + $matZ;
						console("matrixxyz: " .$checkX." ".$checkY." ".$checkZ);
						if (($checkX <= 0) or ($checkX >= 256) or ($checkY <= 0) or ($checkY >= 128) or ($checkZ <= 0) or ($checkZ >= 256)) {
							$player->sendChat("[ERROR] You can't teleport into the void!");
							break 2;
						}
					}
					console("sBlockxyz: " .$checkX." ".$checkY." ".$checkZ);
					$player->sendChat("[TPCompass] Teleporting ...");
					AchievementAPI::grantAchievement($player, "tpcompass");
					$player->teleport(new Vector3((int) $checkX, (int) $checkY + 1, (int) $checkZ));
				}
				break;
		}
	}

	public function __destruct() {
	}
}
