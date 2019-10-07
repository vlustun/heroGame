$j = $;
var gameOver = false;
var remember = false;
var roundInfo = 'start next round';
$j(function() {
	// $j('#console').hide();
	gameOver = false;
	startGame();
	$j('#start').on('click', function(){
		startGame();
		return false;
	});

	$j('#newRound').on('click', function(){
		newRound();
		return false;
	});

	$j(document).on('keyup', function(e) {
		console.log(e.keyCode);
        if (e.keyCode == 67) { $j('#console').toggle(); }   // esc
    });

});
function startGame()
{
	roundInfo = 'start next round';
	$j('#roundInfo').empty().html(roundInfo);
	$j('#newRound').show();
	gameOver = false;
	$j.post('app/Game.php', { func: "start" }, function(data){
		$j('#gameState1').empty().text(JSON.stringify(data));

		$j('#heroHp').css("width", parseInt(data.players.hero.health) + '%');
		$j('#heroHp').next('.value').empty().text(data.players.hero.health);
		$j('#heroHealth').empty().text(data.players.hero.health);
		$j('#heroStrength').empty().text(data.players.hero.strength);
		$j('#heroDefence').empty().text(data.players.hero.defence);
		$j('#heroSpeed').empty().text(data.players.hero.speed);
		$j('#heroLuck').empty().text(data.players.hero.luck);


		$j('#beastHp').css("width", parseInt(data.players.beast.health) + '%');
		$j('#beastHp').next('.value').empty().text(data.players.beast.health);
		$j('#beastHealth').empty().text(data.players.beast.health);
		$j('#beastStrength').empty().text(data.players.beast.strength);
		$j('#beastDefence').empty().text(data.players.beast.defence);
		$j('#beastSpeed').empty().text(data.players.beast.speed);
		$j('#beastLuck').empty().text(data.players.beast.luck);

		$j('#gameState2').empty().text(JSON.stringify(data.beast));
		$j('#gameState3').empty();

		$j('#attacker').text(data.attacker);

	}, 'json');

}

function newRound()
{
	if(gameOver){
		return false;
	}
	if(! remember){
		$j('#roundInfo').empty();
	}
	$j.post('app/Game.php', { func: "newRound" }, function(data){
		
		if(data.attacker == 'Orderus'){
			$j('#beastHp').css("width", parseInt(data.players.beast.health) + '%');
			$j('#beastHp').next('.value').empty().text(Math.max(data.players.beast.health, 0));	
		}else{
			$j('#heroHp').css("width", parseInt(data.players.hero.health) + '%');
			$j('#heroHp').next('.value').empty().text(Math.max(data.players.hero.health, 0));	
		}

		if(data.gameOver){
			gameOver = true;
		}

		if(data.attacker == 'Beast'){
			roundInfo = '';
		}
		

		if(data.miss){
			if(remember){
				roundInfo += data.attacker + ' missed ' +  data.defender + ', lucky ' + data.defender;
			}else{
				roundInfo = data.attacker + ' missed ' +  data.defender + ', lucky ' + data.defender;	
			}
		}else{
			if(remember){
				roundInfo += data.attacker + ' hits ' +  data.defender + ' for ' + data.damage
			}else{
				roundInfo = data.attacker + ' hits ' +  data.defender + ' for ' + data.damage;
			}
			
			if(data.crit){
				roundInfo +=  ' CRITICAL damage. <br><br>';
			}else{
				roundInfo += ' damage. <br><br>';
			}

			if(data.magicShield){
				roundInfo += data.defender + ' used MAGIC SHIELD <br><br>'; 
			}

			if(data.winner){
				roundInfo += data.defender + ' dies <br><br>  ' +  data.attacker + ' is victorious!';
			}else{
				if(data.rapidStrike){
					remember = true;
					roundInfo += data.attacker + " used RAPID STRIKE and attacks again <br><br>";
				}else{
					remember = false;
				}	
			}
		}

		if(data.gameOver && !data.winner){
			roundInfo += "<br><br> Game Over <br> no more rounds";
		}

		if(data.gameOver){
			$j('#newRound').hide();
		}

		if(remember){
			$j('#roundInfo').html(roundInfo);	
		}else{
			$j('#roundInfo').empty().html(roundInfo);
		}

		if(data.rapidStrike){
			newRound();
		}

		

		$j('#roundNumber').text(data.round);
		$j('#attacker').text(data.defender);
		
		$j('#gameState3').append(JSON.stringify(data) + '<br>');

	}, 'json');
}