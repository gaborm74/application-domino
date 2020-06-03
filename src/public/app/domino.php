<?php
namespace Domino;

use Domino\Classes\DominoGame;
require '../../autoload.php';

try {
	$game = new DominoGame();
	$game->startGame($_POST['numPlayers']);
	
	$game->playTheGame();
	$result = $game->evaluateEndgame();
	if ($result !== null) {
		echo json_encode($result);
	}
} catch (\Exception $e) {
	echo json_encode($e->getMessage() . PHP_EOL);
}


