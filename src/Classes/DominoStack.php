<?php
namespace Domino\Classes;

class DominoStack
{

	/**
	 * Stack of available tiles after dealing the initial hands
	 *
	 * @var DominoTile[]
	 */
	private $boneYard = [];

	/**
	 * Initialize the stack
	 */
	public function __construct()
	{
		$this->initStack();
		shuffle($this->boneYard);
	}

	/**
	 * Gets one tile from the already shuffled boneyard
	 *
	 * @return DominoTile
	 */
	public function getFromBoneYard()
	{
		return array_shift($this->boneYard);
	}

	/**
	 * Tells if there's any tile left in the boneyard
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		return (count($this->boneYard) == 0);
	}

	/**
	 * Fills the boneyard with the initial 28 tiles
	 */
	private function initStack()
	{
		for ($leftValue = 0; $leftValue <= 6; $leftValue ++) {
			for ($rightValue = 0; $rightValue <= $leftValue; $rightValue ++) {
				$this->boneYard[] = new DominoTile($leftValue, $rightValue);
			}
		}
	}
}