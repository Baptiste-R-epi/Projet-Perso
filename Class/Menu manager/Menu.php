<?php

include_once "Class/Config.php";
include_once "Class/Menu manager/Selectionnable.php";

class Menu
{
	private Config $config;
	private array $content = [];
	private array $selectedCoord = [0, 0];

	private string $background = "";

	private $callbackOnEsc = false;
	public function __construct(Config $config) {
		$this->config = $config;
	}

	public function set_background($background) {
		$this->background = $background;
	}

	public function add_selectionnable(int $menuX, int $menuY, int $screenX, int $screenY, string $label, ...$callback) {
		$this->content[$menuX][$menuY] = new Selectionnable($label, $screenX, $screenY, ...$callback);
	}
	private function get_selected_callback() {
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
		echo $this->background;

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

	private function select_current() {
		$x = $this->selectedCoord[0];
		$y = $this->selectedCoord[1];
		$this->content[$x][$y]->selected_display();
	}
	private function unselect_current() {
		$x = $this->selectedCoord[0];
		$y = $this->selectedCoord[1];
		$this->content[$x][$y]->basic_display();
	}

	private function move_up() {
		$this->unselect_current();

		$x = $this->selectedCoord[0];
		$y = $this->selectedCoord[1] - 1;

		if ($y == -1) {
			$y = sizeof($this->content[$x]) - 1;
		}
		if (@$this->content[$x][$y]) {
			$this->selectedCoord = [$x, $y];
		}

		$this->select_current();
	}
	private function move_down() {
		$this->unselect_current();

		$x = $this->selectedCoord[0];
		$y = $this->selectedCoord[1] + 1;

		if (@$this->content[$x][$y]) {
			$this->selectedCoord = [$x, $y];
		} elseif (@$this->content[$x][0]) {
			$this->selectedCoord = [$x, 0];
		}

		$this->select_current();

	}
	private function move_left() {
		$this->unselect_current();

		$x = $this->selectedCoord[0] - 1;
		$y = $this->selectedCoord[1];

		if ($x == -1) {
			$x = sizeof($this->content) - 1;
		}
		if (@$this->content[$x][$y]) {
			$this->selectedCoord = [$x, $y];
		}

		$this->select_current();
	}
	private function move_right() {
		$this->unselect_current();

		$x = $this->selectedCoord[0] + 1;
		$y = $this->selectedCoord[1];

		if (@$this->content[$x][$y]) {
			$this->selectedCoord = [$x, $y];
		} elseif (@$this->content[0][$y]) {
			$this->selectedCoord = [0, $y];
		}

		$this->select_current();
	}

	public function use_menu() {
		$this->display_menu();

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
		} while ($order != "ENTER" && $order != "ESC");
	}
}