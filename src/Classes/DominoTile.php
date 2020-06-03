<?php
namespace Domino\Classes;

class DominoTile
{

	/**
	 * Stores the left and right values of a domino tile
	 *
	 * @var array
	 */
	private $tileValues = [];

	/**
	 * Valid values of a domino tile half
	 * 
	 * @var array
	 */
	private $allowedValues = [0,1,2,3,4,5,6];

	/**
	 * Tell if the tile has the same value on both sides
	 * 
	 * @var boolean
	 */
	private $isDouble = false;
	
	/**
	 * Unicode for domino characters
	 * 
	 * @var array
	 */
	private $dominoChars = [
		'horizontal' => [
			0 => [
				0 => '1F031',
				1 => '1F032',
				2 => '1F033',
				3 => '1F034',
				4 => '1F035',
				5 => '1F036',
				6 => '1F037'
			],
			1 => [
				0 => '1F038',
				1 => '1F039',
				2 => '1F03A',
				3 => '1F03B',
				4 => '1F03C',
				5 => '1F03D',
				6 => '1F03E'
			],
			2 => [
				0 => '1F03F',
				1 => '1F040',
				2 => '1F041',
				3 => '1F042',
				4 => '1F043',
				5 => '1F044',
				6 => '1F045'
			],
			3 => [
				0 => '1F046',
				1 => '1F047',
				2 => '1F048',
				3 => '1F049',
				4 => '1F04A',
				5 => '1F04B',
				6 => '1F04C'
			],
			4 => [
				0 => '1F04D',
				1 => '1F04E',
				2 => '1F04F',
				3 => '1F050',
				4 => '1F051',
				5 => '1F052',
				6 => '1F053'
			],
			5 => [
				0 => '1F054',
				1 => '1F055',
				2 => '1F056',
				3 => '1F057',
				4 => '1F058',
				5 => '1F059',
				6 => '1F05A'
			],
			6 => [
				0 => '1F05B',
				1 => '1F05C',
				2 => '1F05D',
				3 => '1F05E',
				4 => '1F05F',
				5 => '1F060',
				6 => '1F061'
			]
		],
		'vertical' => [
			0 => [
				0 => '1F063',
				1 => '1F064',
				2 => '1F065',
				3 => '1F066',
				4 => '1F067',
				5 => '1F068',
				6 => '1F069'
			],
			1 => [
				0 => '1F06A',
				1 => '1F06B',
				2 => '1F06C',
				3 => '1F06D',
				4 => '1F06E',
				5 => '1F06F',
				6 => '1F070'
			],
			2 => [
				0 => '1F071',
				1 => '1F072',
				2 => '1F073',
				3 => '1F074',
				4 => '1F075',
				5 => '1F076',
				6 => '1F077'
			],
			3 => [
				0 => '1F078',
				1 => '1F079',
				2 => '1F07A',
				3 => '1F07B',
				4 => '1F07C',
				5 => '1F07D',
				6 => '1F07E'
			],
			4 => [
				0 => '1F07F',
				1 => '1F080',
				2 => '1F081',
				3 => '1F082',
				4 => '1F083',
				5 => '1F084',
				6 => '1F085'
			],
			5 => [
				0 => '1F086',
				1 => '1F087',
				2 => '1F088',
				3 => '1F089',
				4 => '1F08A',
				5 => '1F08B',
				6 => '1F08C'
			],
			6 => [
				0 => '1F08D',
				1 => '1F08E',
				2 => '1F08F',
				3 => '1F090',
				4 => '1F091',
				5 => '1F092',
				6 => '1F093'
			]
		]
	];
	

	/**
	 * Return the value of one side of the tile
	 *
	 * @param string $side
	 * @return int
	 */
	public function getValue($side)
	{
		return $this->tileValues[$side];
	}

	/**
	 * Setup the tile
	 *
	 * @param int $leftValue
	 * @param int $rightValue
	 * @throws \Exception
	 */
	public function __construct(int $leftValue, int $rightValue)
	{
		if (! in_array($leftValue, $this->allowedValues) || ! in_array($rightValue, $this->allowedValues)) {
			throw new \Exception('Tile value out of bound');
		}

		$this->tileValues = array(
			'L' => $leftValue,
			'R' => $rightValue
		);

		$this->isDouble = ($leftValue == $rightValue);
	}

	/**
	 * Check if the tile can be played in the current round
	 *
	 * @param int $playValue1
	 *        	Possible tile value 1 on the table
	 * @param int $playValue2
	 *        	Possible tile value 2 on the table
	 *        	
	 * @return boolean
	 */
	public function isPlayable(int $playValue1, int $playValue2)
	{
		return ($this->tileValues['L'] == $playValue1 || $this->tileValues['L'] == $playValue2 || $this->tileValues['R'] == $playValue1 || $this->tileValues['R'] == $playValue2) ? true : false;
	}

	/**
	 * Check if the tile is a double tile
	 *
	 * @return boolean
	 */
	public function isDouble()
	{
		return $this->isDouble;
	}

	/**
	 * Return the Unicode character for the tile
	 *
	 * @param string $orientation
	 * @return string
	 */
	public function getHexValue(string $orientation)
	{
		return "&#x" . $this->dominoChars[$orientation][$this->tileValues['L']][$this->tileValues['R']] . ";";
	}

	/**
	 * Flip the values into position to be able to add to the played tiles
	 */
	public function flipValues()
	{
		$temp = $this->tileValues['L'];
		$this->tileValues['L'] = $this->tileValues['R'];
		$this->tileValues['R'] = $temp;
	}
}