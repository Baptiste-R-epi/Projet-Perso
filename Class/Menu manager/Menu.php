<?php

include_once "Class/Config.php";
include_once "Class/Menu manager/Selectionnable.php";

class Menu
{
	private Config $config;
	private array $content = [];
	private array $selectedCoord = [0, 0];
	private string $background = "";
	private int $last_line = 1;
	private $callbackOnEsc = false;

	/**
	 * Take the config files (controls etc) to determine what are the controls to move around the menu.
	 *
	 * @param Config $config the config which contain the controls.
	 */
	public function __construct(Config $config) {
		$this->config = $config;
	}

	/**
	 * set_background() set the background image for the menu. As a default, there is no background.
	 *
	 * @param string $background the background to be set
	 * @return void
	 */
	public function set_background(string $background) {
		$this->background = $background;
		$this->last_line = substr_count($background, "\n") + 2;
	}

	/**
	 * add_selectionnable() is used to create new menu element the user will be able to select from. They are ordained in an internal table to determined how you are meant to move between them.
	 *
	 * @param integer $menuX the X position in the internal table. Start at 0.
	 * @param integer $menuY the Y position in the internal table. Start at 0.
	 * @param integer $screenX the X position on screen. Start at 1, from the upper left corner.
	 * @param integer $screenY the Y position on screen. Start at 1, from the upper left corner.
	 * @param string $label the displayed label on screen.
	 * @param callable ...$callback what function (and its paramater(s)) should be called when you "click" on the selectionnable.
	 * @return void
	 */
	public function add_selectionnable(int $menuX, int $menuY, int $screenX, int $screenY, string $label, ...$callback) {
		$this->content[$menuX][$menuY] = new Selectionnable($label, $screenX, $screenY, ...$callback);
	}

	/**
	 * Get the callable (and its parameter(s) if any) of the actually selected selectionnable of the menu.
	 *
	 * @return array[callable, ...any] the callable of the selectionnable.
	 */
	private function get_selected_callback() {
		$x = $this->selectedCoord[0];
		$y = $this->selectedCoord[1];
		return $this->content[$x][$y]->get_callback();
	}

	/**
	 * As a default, escaping a menu return false, not a function. overwrite_esc_behavior() allows you to choose a function to be called instead of the default behavior.
	 *
	 * @param array ...$callback the function (and its parameter(s) if any) to be called when you escape.
	 * @return void
	 */
	public function overwrite_esc_behavior(...$callback) {
		$this->callbackOnEsc = $callback;
	}

	/**
	 * Considering all information given to the menu, prepare the basic display of the menu : its background, and all its selectionnables. 
	 *
	 * @return void
	 */
	private function display_menu() {
		// Clear screen and draw background
		echo "\e[H\e[J";
		echo $this->background;

		// Draw all selectionnable on screen at start
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
		echo "\e[" . $this->last_line . ";0H";
	}
	private function unselect_current() {
		$x = $this->selectedCoord[0];
		$y = $this->selectedCoord[1];
		$this->content[$x][$y]->basic_display();
		echo "\e[" . $this->last_line . ";0H";
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