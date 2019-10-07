<?php
class Beast
{
	CONST HEALTH = array('min' => 60, 'max' => 90);
	CONST STRENGTH = array('min' => 60, 'max' => 90);
	CONST DEFENCE = array('min' => 40, 'max' => 60);
	CONST SPEED = array('min' => 40, 'max' => 60);
	CONST LUCK = array('min' => 25, 'max' => 40);

	public function __construct()
	{

		$this->initStats();
		$this->name = 'Beast';
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

    public function isLucky()
    {
    	$response = array();
    	$response['success'] = false;
    	$rsponse = array();
    	$isRand = rand(0, 100);
    	$response['luck_randomizer'] = $isRand;
    	if($isRand <= $this->luck){
    		$response['success'] = true;
    	}

    	return $response;
    }

    public function isCrit()
    {
    	$response = array();
    	$response['success'] = false;
    	$isRand = rand(0, 100);
    	$response['crit_randomizer'] = $isRand;
    	if($isRand <= ceil($this->luck/5)){
    		$response['success'] = true;
    	}
    	
    	return $response;
    }
}