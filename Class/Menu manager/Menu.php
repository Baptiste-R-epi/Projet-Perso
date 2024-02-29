<?php

include_once "Class/Config.php";
include_once "Class/Menu manager/Selectionnable.php";

class Menu
{
	private Config $config;
	private array $content = [];
	private array $selectedCoord = [0, 0];

	private $callbackOnEsc = false;
	public function __construct(Config $config) {
		$this->config = $config;
	}
	public function add_selectionnable(int $menuX, int $menuY, int $screenX, int $screenY, string $label, ...$callback) {
		$this->content[$menuX][$menuY] = new Selectionnable($label, $screenX, $screenY, ...$callback);
	}
	public function get_selected_callback() {
		$x = $this->selectedCoord[0];
		$y = $this->selectedCoord[1];
		return $this->content[$x][$y]->get_callback();
	}

	public function overwrite_esc_behavior(...$callback) {
		$this->callbackOnEsc = $callback;
	}

	private function display_menu() {
		// Clear screen
		echo "\e[H\e[J";

		for ($i = 0; $i < sizeof($this->content); $i++) {
			for ($j = 0; $j < sizeof($this->content[$i]); $j++) {
				if ($this->selectedCoord == [$i, $j]) {
					$this->content[$i][$j]->selected_display();
				} else {
					$this->content[$i][$j]->basic_display();
				}
			}
		}
	}

	private function move_up() {
		$x = $this->selectedCoord[0];
		$y = $this->selectedCoord[1] - 1;
		if (@$this->content[$x][$y]) {
			$this->selectedCoord = [$x, $y];
		}
	}
	private function move_down() {
		$x = $this->selectedCoord[0];
		$y = $this->selectedCoord[1] + 1;
		if (@$this->content[$x][$y]) {
			$this->selectedCoord = [$x, $y];
		}
	}
	private function move_left() {
		$x = $this->selectedCoord[0] - 1;
		$y = $this->selectedCoord[1];
		if (@$this->content[$x][$y]) {
			$this->selectedCoord = [$x, $y];
		}
	}
	private function move_right() {
		$x = $this->selectedCoord[0] + 1;
		$y = $this->selectedCoord[1];
		if (@$this->content[$x][$y]) {
			$this->selectedCoord = [$x, $y];
		}
	}

	public function use_menu() {
		do {
			usleep(100000);
			$order = $this->config->get_order();
			switch ($order) {
				case "ESC":
					return $this->callbackOnEsc; // temporaire
				case "ENTER":
					return $this->get_selected_callback();
				case "UP":
					$this->move_up();
					break;
				case "DOWN":
					$this->move_down();
					break;
				case "LEFT":
					$this->move_left();
					break;
				case "RIGHT":
					$this->move_right();
					break;
			}
			$this->display_menu();
		} while ($order != "ENTER" && $order != "ESC");
	}
}