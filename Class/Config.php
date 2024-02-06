<?php
include_once("config.php");
include_once("Functions/Error_Manager");

class Config
{
	private $sprite_max_cycle;
	private $sprite_cycle;
	const MAP = [
		MAP_START => "START",
		MAP_END => "END",
		MAP_BLOCK => "BLOCK",
		MAP_END_COIN => "END_COIN",
	];
	const DISPLAY = [
		"CURSOR" => DISPLAY_CURSOR,
		MAP_END => DISPLAY_END,
		MAP_BLOCK => DISPLAY_BLOCK,
		MAP_END_COIN => DISPLAY_END_COIN,
		"AIR" => DISPLAY_AIR,
		"BORDER" => DISPLAY_BORDER,
		"SPIKE" => DISPLAY_SPIKE,
		"HOLE" => DISPLAY_HOLE,
	];
	const CONTROL = [
		"LEFT" => CONTROL_LEFT,
		"RIGHT" => CONTROL_RIGHT,
		"UP" => CONTROL_UP,
		"DOWN" => CONTROL_DOWN,
	];

	function __construct() {
		foreach ($this::MAP as $key => $value) {
			if (strlen($key) != 1) {
				error_function("MAP config ", $value, " has a length other than 1 : ", $key);
			}
		}

		if (sizeof($this::MAP) != sizeof(array_unique($this::MAP))) {
			error_function("MAP config has two identical values.");
		}

		foreach ($this::DISPLAY as $key => $value) {
			if (strlen($value) == 0) {
				error_function("DISPLAY config ", (strlen($key) == 1 ? $this::MAP[$key] : $key), " must not be an empty string");
			}
		}

		stop_if_error();
	}
	private function set_sprite_length() {

	}

	function check_map(string $character) {
		if (strlen($character) == 0) {
			return "AIR";
		}
		return $this::MAP[$character] ?? "AIR";
	}
}

const CONFIG = new Config;