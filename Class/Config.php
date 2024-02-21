<?php
include_once("config.php");
include_once("Functions/Error_Manager.php");
include_once("Functions/Lcm.php");

class Config
{
	private $sprite_max_cycle;
	private $sprite_cycle = 0;
	private $Display;
	const MAP = [
		MAP_START => "START",
		MAP_END => "END",
		MAP_BLOCK => "BLOCK",
		MAP_END_COIN => "END_COIN",
	];
	const CONTROL = [
		"LEFT" => CONTROL_LEFT,
		"RIGHT" => CONTROL_RIGHT,
		"UP" => CONTROL_UP,
		"DOWN" => CONTROL_DOWN,
		"ALL" => CONTROL_LEFT . CONTROL_RIGHT . CONTROL_UP . CONTROL_DOWN,
	];

	public function __construct() {
		foreach ($this::MAP as $key => $value) {
			if (strlen($key) != 1) {
				error_function("MAP config ", $value, " has a length other than 1 : ", $key);
			}
			if (str_contains("0123456789", $key)) {
				error_function("MAP config ", $value, " must not be a number : ", $key, "\nThose are for spikes and holes.");
			}
		}

		if (sizeof($this::MAP) != sizeof(array_unique($this::MAP))) {
			error_function("MAP config has two identical values.");
		}

		$this->set_Display();

		foreach ($this->Display as $key => $value) {
			if (sizeof($value) == 0) {
				error_function("DISPLAY config ", (strlen($key) == 1 ? $this::MAP[$key] : $key), " must not be an empty string");
			}
		}
		stop_if_error();

		$this->set_sprite_length();
	}

	private function set_Display() {
		$this->Display = [
			"CURSOR" => mb_str_split(DISPLAY_CURSOR),
			MAP_END => mb_str_split(DISPLAY_END),
			MAP_BLOCK => mb_str_split(DISPLAY_BLOCK),
			MAP_END_COIN => mb_str_split(DISPLAY_END_COIN),
			"AIR" => mb_str_split(DISPLAY_AIR),
			"BORDER" => mb_str_split(DISPLAY_BORDER),
			"SPIKE" => mb_str_split(DISPLAY_SPIKE),
			"HOLE" => mb_str_split(DISPLAY_HOLE),
		];
	}

	private function set_sprite_length() {
		$this->sprite_max_cycle = math_lcm(
			...array_map(
				function ($s) {
					return sizeof($s);
				},
				$this->Display
			)
		);
		var_dump($this->Display);
		echo $this->sprite_max_cycle;
	}

	public function get_map(string $character) {
		return $this::MAP[$character] ?? "AIR";
	}

	public function get_display(string $character) {
		if (!$this->Display[$character]) {
			return $this->Display["AIR"][
				$this->sprite_cycle % strlen($this->Display["AIR"])
			];
		}
		return $this->Display[$character][
			$this->sprite_cycle % strlen($this->Display[$character])
		];
	}

	public function next_cycle() {
		$this->sprite_cycle = ($this->sprite_cycle + 1) % $this->sprite_max_cycle;
	}
}

const CONFIG = new Config;