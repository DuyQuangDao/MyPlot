<?php
namespace MyPlot\provider;

use MyPlot\MyPlot;
use MyPlot\Plot;
use pocketmine\utils\Config;

class JSONDataProvider extends DataProvider
{
	/** @var MyPlot */
	protected $plugin;
	/** @var Config */
	private $json;

	public function __construct(MyPlot $plugin, int $cacheSize = 0) {
		parent::__construct($plugin, $cacheSize);
		$this->json = new Config($this->plugin->getDataFolder()."Data".DIRECTORY_SEPARATOR."plots.yml", Config::JSON, [
			"count" => 0,
			"plots" => []
		]);
	}

	/**
	 * @param Plot $plot
	 * @return bool
	 */
	public function savePlot(Plot $plot) : bool {
		$plots = $this->json->get("plots", []);
		$plots[$plot->id] = [
			"level" => $plot->levelName,
			"x" => $plot->X,
			"z" => $plot->Z,
			"name" => $plot->name,
			"owner" => $plot->owner,
			"helpers" => $plot->helpers,
			"denied" => $plot->denied,
			"biome" => $plot->biome
		];
		$this->json->set("plots", $plots);
		$this->cachePlot($plot);
		return $this->json->save();
	}

	/**
	 * @param Plot $plot
	 * @return bool
	 */
	public function deletePlot(Plot $plot) : bool {
		$plots = $this->json->get("plots", []);
		unset($plots[$plot->id]);
		$this->json->set("plots", $plots);
		$this->cachePlot($plot);
		return $this->json->save();
	}

	/**
	 * @param string $levelName
	 * @param int $X
	 * @param int $Z
	 * @return Plot
	 */
	public function getPlot(string $levelName, int $X, int $Z) : Plot {
		if (($plot = $this->getPlotFromCache($levelName, $X, $Z)) != null) {
			return $plot;
		}
		$plots = $this->json->get("plots");
		$levelKeys = array_keys($plots, $levelName);
		$xKeys = array_keys($plots, $X);
		$zKeys = array_keys($plots, $Z);
		$key = null;
		foreach($levelKeys as $levelKey) {
			foreach($xKeys as $xKey) {
				foreach($zKeys as $zKey) {
					if($zKey == $xKey and $xKey == $levelKey and $zKey == $levelKey) {
						$key = $levelKey;
						break 3;
					}
				}
			}
		}
		if($key != null) {
			$plotName = $plots[$key]["name"] == "" ? "" : $plots[$key]["name"];
			$owner = $plots[$key]["owner"] == "" ? "" : $plots[$key]["owner"];
			$helpers = $plots[$key]["helpers"] == [] ? [] : $plots[$key]["helpers"];
			$denied = $plots[$key]["denied"] == [] ? [] : $plots[$key]["denied"];
			$biome = strtoupper($plots[$key]["biome"]) == "PLAINS" ? "PLAINS" : strtoupper($plots[$key]["biome"]);

			return new Plot($levelName, $X, $Z, $plotName, $owner, $helpers, $denied, $biome, $key);
		}
		$count = $this->json->get("count", 0);
		$this->json->set("count", (int)$count++);
		$this->json->save(true);
		return new Plot($levelName, $X, $Z, "", "", [], [], "PLAINS", (int)$count);
	}

	/**
	 * @param string $owner
	 * @param string $levelName
	 * @return Plot[]
	 */
	public function getPlotsByOwner(string $owner, string $levelName = "") : array {

		$plots = $this->json->get("plots");
		$ownerPlots = [];
		if($levelName != "") {
			$levelKeys = array_keys($plots, $levelName);
			$ownerKeys = array_keys($plots, $owner);
			foreach($levelKeys as $levelKey) {
				foreach($ownerKeys as $ownerKey) {
					if($levelKey == $ownerKey) {
						$X = $plots[$levelKey]["x"];
						$Z = $plots[$levelKey]["x"];
						$plotName = $plots[$levelKey]["name"] == "" ? "" : $plots[$levelKey]["name"];
						$owner = $plots[$levelKey]["owner"] == "" ? "" : $plots[$levelKey]["owner"];
						$helpers = $plots[$levelKey]["helpers"] == [] ? [] : $plots[$levelKey]["helpers"];
						$denied = $plots[$levelKey]["denied"] == [] ? [] : $plots[$levelKey]["denied"];
						$biome = strtoupper($plots[$levelKey]["biome"]) == "PLAINS" ? "PLAINS" : strtoupper($plots[$levelKey]["biome"]);

						$ownerPlots[] = new Plot($levelName, $X, $Z, $plotName, $owner, $helpers, $denied, $biome, $levelKey);
					}
				}
			}
		}else{
			$ownerKeys = array_keys($plots, $owner);
			foreach($ownerKeys as $key) {
				$levelName = $plots[$key]["level"];
				$X = $plots[$key]["x"];
				$Z = $plots[$key]["x"];
				$plotName = $plots[$key]["name"] == "" ? "" : $plots[$key]["name"];
				$owner = $plots[$key]["owner"] == "" ? "" : $plots[$key]["owner"];
				$helpers = $plots[$key]["helpers"] == [] ? [] : $plots[$key]["helpers"];
				$denied = $plots[$key]["denied"] == [] ? [] : $plots[$key]["denied"];
				$biome = strtoupper($plots[$key]["biome"]) == "PLAINS" ? "PLAINS" : strtoupper($plots[$key]["biome"]);

				$ownerPlots[] = new Plot($levelName, $X, $Z, $plotName, $owner, $helpers, $denied, $biome, $key);
			}
		}
		return $ownerPlots;
	}

	/**
	 * @param string $levelName
	 * @param int $limitXZ
	 * @return Plot|null
	 */
	public function getNextFreePlot(string $levelName, int $limitXZ = 0){
		$plots = $this->json->get("plots", []);
		//TODO
	}
	public function close(){
		unset($this->json);
	}
}