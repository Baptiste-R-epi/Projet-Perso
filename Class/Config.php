<?php
include_once("config.php");
include_once("Functions/Error_Manager.php");
include_once("Functions/Lcm.php");

class Config
{
	private int $spriteMaxCycle;
	private int $spriteCycle = 0;
	private array $allDisplay;
	private array $currentDisplay;
	const MAP = [
		MAP_START => "START",
		MAP_END => "END",
		MAP_BLOCK => "BLOCK",
		MAP_END_COIN => "END_COIN",
		MAP_SCORE_COIN => "SCORE_COIN",
		MAP_SPIKE => "SPIKE",
		MAP_BUMPER => "BUMPER",
	];
	const CONTROL = [
		"LEFT" => CONTROL_LEFT,
		"RIGHT" => CONTROL_RIGHT,
		"UP" => CONTROL_UP,
		"DOWN" => CONTROL_DOWN,
		"ALL" => CONTROL_LEFT . CONTROL_RIGHT . CONTROL_UP . CONTROL_DOWN,
	];

	public function __construct() {
		$this->set_display();
		$this->error_manager();
		$this->set_sprite_max_length();
	}

	private function error_manager() {
		foreach ($this::MAP as $key => $value) {
			if (strlen($key) != 1) {
				error_function("MAP config ", $value, " has a length other than 1 : ", $key);
			}
		}

		if (sizeof($this::MAP) != sizeof(array_unique($this::MAP))) {
			error_function("MAP config has two identical values.");
		}

		foreach ($this->allDisplay as $key => $value) {
			if (sizeof($value) == 0) {
				error_function("DISPLAY config ", (strlen($key) == 1 ? $this::MAP[$key] : $key), " must NOT be an empty string");
			}
		}
		stop_if_error();
	}
	private function set_display() {
		$this->allDisplay = [
			"CURSOR" => mb_str_split(DISPLAY_CURSOR),
			"END" => mb_str_split(DISPLAY_END),
			"BLOCK" => mb_str_split(DISPLAY_BLOCK),
			"END_COIN" => mb_str_split(DISPLAY_END_COIN),
			"SCORE_COIN" => mb_str_split(DISPLAY_SCORE_COIN),
			"AIR" => mb_str_split(DISPLAY_AIR),
			"BORDER" => mb_str_split(DISPLAY_BORDER),
			"SPIKE" => mb_str_split(DISPLAY_SPIKE),
			"BUMPER" => mb_str_split(DISPLAY_BUMPER),
		];
		$this->update_display();
	}
	private function update_one_display($string) {
		return $this->allDisplay[$string][
			$this->spriteCycle % sizeof($this->allDisplay[$string])
		];
	}
	private function update_display() {
		$this->currentDisplay = [
			"CURSOR" => $this->update_one_display("CURSOR"),
			"END" => $this->update_one_display("END"),
			"BLOCK" => $this->update_one_display("BLOCK"),
			"END_COIN" => $this->update_one_display("END_COIN"),
			"SCORE_COIN" => $this->update_one_display("SCORE_COIN"),
			"AIR" => $this->update_one_display("AIR"),
			"BORDER" => $this->update_one_display("BORDER"),
			"SPIKE" => $this->update_one_display("SPIKE"),
			"BUMPER" => $this->update_one_display("BUMPER"),
		];
	}

	private function set_sprite_max_length() {
		$this->spriteMaxCycle = math_lcm(
			...array_map("sizeof", $this->allDisplay)
		);
	}

	public function get_type_from_map($character) {
		return $this::MAP[$character] ?? "AIR";
	}

	public function get_display_char_from_type(string $type) {
		return
			$this->currentDisplay[$type] ??
			$this->currentDisplay["AIR"];
	}

	public function display_data_next_cycle() {
		$this->spriteCycle = ($this->spriteCycle + 1) % $this->spriteMaxCycle;
		$this->update_display();
	}

	public function get_order() {
		$key = fgets(STDIN);
		$result = [];
		preg_match("/\e(\[[ABCD])?|[" . $this::CONTROL["ALL"] . "\n]|/", $key, $result);

		switch (true) {
			default:
			case $result[0] == "":
				return "NONE";
			case $result[0] == "\n":
				return "ENTER";
			case $result[0] == "\e":
				return "ESC";
			case $result[0] == "\e[A" || str_contains($this::CONTROL["UP"], $result[0]):
				return "UP";
			case $result[0] == "\e[B" || str_contains($this::CONTROL["DOWN"], $result[0]):
				return "DOWN";
			case $result[0] == "\e[C" || str_contains($this::CONTROL["RIGHT"], $result[0]):
				return "RIGHT";
			case $result[0] == "\e[D" || str_contains($this::CONTROL["LEFT"], $result[0]):
				return "LEFT";

		}
	}
}