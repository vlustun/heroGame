<?php
include("Beast.php");
class Hero extends Beast
{
	CONST HEALTH = array('min' => 70, 'max' => 100);
	CONST STRENGTH = array('min' => 70, 'max' => 80);
	CONST DEFENCE = array('min' => 45, 'max' => 55);
	CONST SPEED = array('min' => 40, 'max' => 50);
	CONST LUCK = array('min' => 10, 'max' => 30);


	public function __construct()
	{

		$this->initStats();
		$this->name = 'Orderus';
		return $this;
	}

	public function initStats()
    {
		$this->health = rand(self::HEALTH['min'], self::HEALTH['max']);
		$this->strength = rand(self::STRENGTH['min'], self::STRENGTH['max']);
		$this->defence = rand(self::DEFENCE['min'], self::DEFENCE['max']);
		$this->speed = rand(self::SPEED['min'], self::SPEED['max']);
		$this->luck = rand(self::LUCK['min'], self::LUCK['max']);
    }


    public function magicShield()
    {
    	$isLuck = rand(0, 100);
    	if($isLuck <= 20){
    		return true;
    	}
    	return false;
    }

    public function rapidStrike()
    {
    	$isLuck = rand(0, 100);
    	if($isLuck <= 10){
    		return true;
    	}
    	return false;
    }
}