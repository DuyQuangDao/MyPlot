<?php
namespace MyPlot;

use pocketmine\block\Block;

class PlotLevelSettings
{
	/** @var string $name */
	public $name;
	/** @var Block  */
	public $roadBlock, $wallBlock, $plotFloorBlock, $plotFillBlock, $bottomBlock;
	/** @var int  */
	public $roadWidth, $plotSize, $groundHeight, $claimPrice, $clearPrice, $disposePrice, $resetPrice;
	/** @var bool  */
	public $restrictEntityMovement, $updatePlotLiquids;

	public function __construct(string $name, array $settings = []) {
		$this->name = $name;
		if (!empty($settings)) {
			$this->roadBlock = self::parseBlock($settings, "RoadBlock", Block::get(Block::PLANK));
			$this->wallBlock = self::parseBlock($settings, "WallBlock", Block::get(Block::SLABS));
			$this->plotFloorBlock = self::parseBlock($settings, "PlotFloorBlock", Block::get(Block::GRASS));
			$this->plotFillBlock = self::parseBlock($settings, "PlotFillBlock", Block::get(Block::DIRT));
			$this->bottomBlock = self::parseBlock($settings, "BottomBlock", Block::get(Block::BEDROCK));
			$this->roadWidth = self::parseNumber($settings, "RoadWidth", 7);
			$this->plotSize = self::parseNumber($settings, "PlotSize", 22);
			$this->groundHeight = self::parseNumber($settings, "GroundHeight", 64);
			$this->claimPrice = self::parseNumber($settings, "ClaimPrice", 0);
			$this->clearPrice = self::parseNumber($settings, "ClearPrice", 0);
			$this->disposePrice = self::parseNumber($settings, "DisposePrice", 0);
			$this->resetPrice = self::parseNumber($settings, "ResetPrice", 0);
			$this->restrictEntityMovement = self::parseBool($settings, "RestrictEntityMovement", true);
			$this->updatePlotLiquids = self::parseBool($settings, "UpdatePlotLiquids", false);
		}
	}

	/**
	 * @param array $array
	 * @param string|int $key
	 * @param Block $default
	 * @return Block
	 */
	private static function parseBlock(array &$array, $key, Block $default) : Block {
		if (isset($array[$key])) {
			$id = $array[$key];
			if (is_numeric($id)) {
				$block = Block::get($id);
			} else {
				$split = explode(":", $id);
				if (count($split) === 2 and is_numeric($split[0]) and is_numeric($split[1])) {
					$block = Block::get($split[0], $split[1]);
				} else {
					$block = $default;
				}
			}
		} else {
			$block = $default;
		}
		return $block;
	}

	/**
	 * @param array $array
	 * @param string|int $key
	 * @param int $default
	 * @return int
	 */
	private static function parseNumber(array &$array, $key, int $default) : int {
		if (isset($array[$key]) and is_numeric($array[$key])) {
			return $array[$key];
		} else {
			return $default;
		}
	}

	/**
	 * @param array $array
	 * @param string|int $key
	 * @param bool $default
	 * @return bool
	 */
	private static function parseBool(array &$array, $key, bool $default) : bool {
		if (isset($array[$key]) and is_bool($array[$key])) {
			return $array[$key];
		} else {
			return $default;
		}
	}
}