<?php
include("Hero.php");

// include("Round.php");
class Game
{
	public $orderus = null;
	public $beast = null;
	public $maxRounds = 20;

    public function __construct()
    {
        return $this;
    }

	public function start()
    {
    	$gameState['round'] = 0;
    	$gameState['attacker'] = null;
    	$gameState['winner'] = null;
    	$gameState['gameOver'] = false;

		$players = $this->initPlayers();

		$attacker = $this->getAttacker();

		$response = array('attacker' => $attacker->name, 'players' => $players);

		session_start();
		$_SESSION['players'] = $players;
		$_SESSION['gameState'] = $gameState;

    	header('Content-Type: application/json');
		echo json_encode($response);
    }

    public function initPlayers()
    {
    	$this->orderus = new Hero(); 
    	$this->beast = new Beast(); 

		$response['hero'] = $this->orderus;
		$response['beast'] = $this->beast;
		return $response;
    }

    public function newRound()
    {
    	$roundData = array();
		$roundData['rapidStrike'] = false;
		$roundData['magicShield'] = false;

    	session_start();
    	$players = $_SESSION['players'];
    	$this->orderus = $players['hero']; 
    	$this->beast = $players['beast']; 
    	$gameState = $_SESSION['gameState'];

    	if($gameState['gameOver']){
    		header('Content-Type: application/json');
			echo json_encode($gameState);

			return false;
    	}

    	//get attacker and defender
    	if($gameState['attacker']){
    		$attacker = $gameState['attacker'];
    	}else{
    		$attacker = $this->getAttacker();
    	}

    	if($attacker == $this->orderus){
    		$defender = $this->beast;

    	}else{
    		$defender = $this->orderus;
    	}
    	
    	$damage = $attacker->strength - $defender->defence;
    	// is defenderLucky, damage  = 0
    	$miss = $defender->isLucky();
    	if($miss['success']){
    		$damage = 0;
    		$roundData['miss'] = true;
    		$roundData['luck_randomizer'] = $miss['luck_randomizer'];
    	}

    	$crit = $attacker->isCrit();
    	if($crit['success']){
    		$damage = ceil($damage + $damage/2);
    		$roundData['crit'] = true;	
    		$roundData['crit_randomizer'] = $crit['crit_randomizer'];	
    	}

    	// add atack modifiers
		if($defender == $this->orderus && $this->orderus->magicShield()){
			$damage = ceil($damage / 2);
			$roundData['magicShield'] = true;
		}

		// do the damage
		$defender->health -= $damage;
		if($defender->health <= 0){
			$roundData['winner'] = $attacker->name;
			$gameState['winner'] = $attacker->name;
			$gameState['gameOver'] = true;
			$roundData['gameOver'] = true;
		}

		$roundData['attacker'] = $attacker->name;
		$roundData['defender'] = $defender->name;

    	//switchAttacker for next round
    	if($attacker == $this->orderus){
    		if($this->orderus->rapidStrike()){
    			$attacker = $this->orderus;
    			$roundData['rapidStrike'] = true;
    		}else{
    			$attacker = $this->beast;
    		}
    	}else{
    		$attacker = $this->orderus;
    	}

    	$gameState['round'] += 1;
    	if($gameState['round'] == $this->maxRounds){
    		$gameState['gameOver'] = true;
    		$roundData['gameOver'] = true;
    	}

    	
    	$roundData['damage'] = $damage;
    	$roundData['round'] = $gameState['round'];
    	$gameState['attacker'] = $attacker;

		$players = array('hero' => $this->orderus, 'beast' => $this->beast);

    	$_SESSION['players'] = $players;
    	$_SESSION['gameState'] = $gameState;

    	$roundData['players'] = $players;
    	header('Content-Type: application/json');
		echo json_encode($roundData);
    }

    public function getAttacker()
    {
    	if($this->orderus->speed > $this->beast->speed){
    		return $this->orderus;
    	}else if($this->orderus->speed < $this->beast->speed){
			return $this->beast;
    	}else{
			if($this->orderus->luck > $this->beast->luck){
				return $this->orderus;
			}else if($this->orderus->luck < $this->beast->luck){
				return $this->beast;
			}else{
				return $this->orderus;
			}
    	}
    }
}

$game = new Game();
switch($_POST['func']){
	case "start" :
		$game->start();
		break;

	case "newRound":
		$game->newRound();
		break;

	default:
		throw new Exception('invalid command');
		break;
}
