<?php
namespace Domino\Classes;

class DominoGame
{

	/**
	 * Players playing the game
	 *
	 * @var DominoPlayer[]
	 */
	private $dominoPlayers = [];

	/**
	 * Tiles on the table
	 *
	 * @var DominoTile[]
	 */
	private $playedTiles = [];

	/**
	 * The key of the player next to play from $dominoPlayers
	 *
	 * @var integer
	 */
	private $activePlayerKey = null;

	/**
	 * Values from the extremities of the played tiles on the table
	 *
	 * @var array
	 */
	private $playableValues = [];

	/**
	 * The remaining stack after dealing
	 *
	 * @var DominoStack
	 */
	private $boneYard = null;

	/**
	 * Game progress logging
	 *
	 * @var array
	 * 
	 * @TODO Move progress logging to its own class for code clarity as it is cluttered for now
	 */
	private $progress = [];

	/**
	 * Entry point to start the game
	 *
	 * @param integer $numberOfPlayers
	 *        	Number of players
	 */
	public function startGame(int $numberOfPlayers)
	{
		if ($numberOfPlayers < 2 || $numberOfPlayers > 4) {
			throw new \Exception('Number of players must be between 2 and 4 inclusive');
		}
		// Init the stack
		$this->boneYard = new DominoStack();

		// Add players to the game
		for ($count = 1; $count <= $numberOfPlayers; $count ++) {
			$this->dominoPlayers[] = new DominoPlayer();
		}

		// Deal players
		/* @var DominoTile $startingTile */
		$startingTile = null;
		foreach ($this->dominoPlayers as $playerKey => $player) {
			for ($count = 0; $count < 7; $count ++) {
				$tile = $this->boneYard->getFromBoneYard();
				$player->addToHand($tile);
				$this->progress['start']['hands'][$playerKey][] = $tile->getHexValue('vertical');

				// IF the tile is
				// - a double
				// AND
				// - no starting tile selected yet
				// OR
				// - the numbers are higher than any previous tile
				// SET the starting tile
				if ($tile->isDouble() && (! isset($startingTile) || (isset($startingTile) && ($startingTile->getValue('L') < $tile->getValue('L'))))) {
					$startingTile = $tile;
					$this->activePlayerKey = $playerKey;
				}
			}
		}

		// if no starting double tile can be found, pick a random player's random tile to start
		if (! isset($startingTile)) {
			$this->activePlayerKey = array_rand($this->dominoPlayers);
			$startingTile = $this->dominoPlayers[$this->activePlayerKey]->playStarterTile();
		}
		$this->playableValues['L'] = $startingTile->getValue('L');
		$this->playableValues['R'] = $startingTile->getValue('R');
		$this->playedTiles[] = $startingTile;
		$this->dominoPlayers[$this->activePlayerKey]->removeFromHand($startingTile);
		$this->progress['start']['table'] = $startingTile->getHexValue('horizontal');
	}

	/**
	 * Return the played tiles on the table
	 *
	 * @return DominoTile[]
	 */
	public function showTiles()
	{
		return $this->playedTiles;
	}

	/**
	 * Get the end result of the game
	 *
	 * @return array
	 */
	public function evaluateEndgame()
	{
		$result = [];
		$winners = [];

		// Get the points in each player's hand
		foreach ($this->dominoPlayers as $playerKey => $player) {
			$result[$playerKey] = $player->getHandPoints();
		}

		// Check if there's an absolute winner by empty hand
		$emptyHandWinner = array_search(- 1, $result);

		if ($emptyHandWinner !== false) {
			// If there's a player with empty hand, declare it as winner
			$emptyHandWinner = $emptyHandWinner + 1;
			$result['winner'] = "Player #$emptyHandWinner"; // . $emptyHandWinner+1;
		} else {
			// If no empty hand player, then look for the player with the least points in hand
			asort($result, SORT_NUMERIC);
			$prevValue = null;
			foreach ($result as $key => $value) {
				if (count($winners) && ($prevValue != $value)) {
					break;
				}
				if ($prevValue === null || ($prevValue == $value)) {
					$winners[] = $key;
				}
				$prevValue = $value;
			}

			if (count($winners) > 1) {
				// If there are multiple players with the lowest point in hand, it's a tie
				$increment = function ($value) {
					$value = $value + 1;
					return "Player #$value";
				};
				$result['tie'] = implode(', ', array_map($increment, $winners));
			} else {
				// If there's only one player with the lowest point, declare it as winner
				$player = array_shift($winners) + 1;
				$result['winner'] = "Player #$player";
			}
		}

		$response['winner'] = (isset($result['winner'])) ? $result['winner'] . " won the game" : $result['tie'] . " had the same lowest numbers in their hands";

		$tiles = [];
		foreach ($this->playedTiles as $tile) {
			$tiles[] = $tile->getHexValue('horizontal');
		}
		$response['playedTiles'] = $tiles;
		$response['progress'] = $this->progress;
		return $response;
	}

	/**
	 * Main logic of the game
	 */
	public function playTheGame()
	{
		// if every player passed and boneyard empty, return
		if ($this->boneYard->isEmpty() && $this->checkPlayersStatus()) {
			$this->progress['game'][] = "Everyone passed and there are no more tiles in the boneyard";
			return;
		}

		$this->activePlayerKey = $this->getNextPlayer($this->activePlayerKey);

		// If player does not have any playable tile, pick from boneyard
		while (! $this->dominoPlayers[$this->activePlayerKey]->checkHand($this->playableValues['L'], $this->playableValues['R'])) {
			$newTile = $this->boneYard->getFromBoneYard();
			// If there's no more tile left in the boneyard, player passes
			if (! $newTile) {
				$this->progress['game'][] = "Boneyard is empty";
				break;
			}
			$this->progress['game'][] = "Player #" . ($this->activePlayerKey + 1) . " picked up <span style='font-size: 3rem'>" . $newTile->getHexValue('vertical') . "</span>";
			$this->dominoPlayers[$this->activePlayerKey]->addToHand($newTile);
		}

		if ($this->dominoPlayers[$this->activePlayerKey]->canPlay()) {
			$nextTile = $this->dominoPlayers[$this->activePlayerKey]->playHand();

			// Position the tile correctly to match the tile on the table
			if ($nextTile->getValue('L') == $this->playableValues['L'] || $nextTile->getValue('R') == $this->playableValues['R']) {
				$nextTile->flipValues();
			}

			// Prepend or append the new tile to the played tiles to match values
			if ($nextTile->getValue('R') == $this->playableValues['L']) {
				array_unshift($this->playedTiles, $nextTile);
				$this->playableValues['L'] = $nextTile->getValue('L');
			} else {
				array_push($this->playedTiles, $nextTile);
				$this->playableValues['R'] = $nextTile->getValue('R');
			}
			$this->progress['game'][] = "Player #" . ($this->activePlayerKey + 1) . " turn. Played <span style='font-size: 3rem'>" . $nextTile->getHexValue('vertical') . "</span>";
			if ($this->dominoPlayers[$this->activePlayerKey]->isEmptyHand()) {
				$this->dominoPlayers[$this->activePlayerKey]->setAsWinner();
				$this->progress['game'][] = "Player #" . ($this->activePlayerKey + 1) . " wins with empty hand";
				return;
			}
			$this->dominoPlayers[$this->activePlayerKey]->setInactive(false);
		} else {
			$this->progress['game'][] = "Player #" . ($this->activePlayerKey + 1) . " passes";
			$this->dominoPlayers[$this->activePlayerKey]->setInactive(true);
		}

		// Next
		$this->playTheGame();
	}

	/**
	 * Return the next player to play
	 *
	 * @param integer $activePlayerKey
	 * @return integer
	 */
	private function getNextPlayer($activePlayerKey)
	{
		$numberOfPlayers = count($this->dominoPlayers);
		if ($activePlayerKey < $numberOfPlayers - 1) {
			return ($activePlayerKey + 1);
		} else {
			return 0;
		}
	}

	/**
	 * Returns if there are any player left to be able to play
	 * (no tiles in hand to place on table, boneyard is empty...)
	 *
	 * @return boolean
	 */
	private function checkPlayersStatus()
	{
		$allPassed = true;
		foreach ($this->dominoPlayers as $player) {
			$allPassed &= $player->isInactive(); // && $allPassed
		}
		return $allPassed;
	}
}