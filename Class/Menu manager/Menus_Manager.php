<?php

include_once "Class/Config.php";
include_once "Class/Menu manager/Menu.php";
include_once "Class/Game.php";
class Menus_Manager
{

	private $main_menu;
	private $level_menu;
	private $credit_menu;
	private Config $config;
	private Game $game;

	public function __construct(Config $config) {
		$this->config = $config;
		$this->game = new Game($config);
		$this->set_main_menu();
		$this->set_level_menu();
		$this->set_credit_menu();
	}

	public function get_started() {
		$use_function = [
			function () {
				return $this->main_menu->use_menu();
			}
		];
		while ($use_function = call_user_func(...$use_function))
			;
	}
	private function set_main_menu() {
		$menu = new Menu($this->config);

		$menu->set_background(
			"  ╔════════════════════════╗\n" .
			"  ║ WELCOME TO THE AWESOME ║\n" .
			"  ║       SUPER GAME !     ║\n" .
			"  ║                        ║\n" .
			"  ║                        ║\n" .
			"  ║    Start new game      ║\n" .
			"  ║                        ║\n" .
			"  ║     Choose level       ║\n" .
			"  ║                        ║\n" .
			"  ║        Credits         ║\n" .
			"  ╚════════════════════════╝"
		);

		$menu->add_selectionnable(0, 0, 8, 6, "Start new game", function () {
			$this->game->start_game();
		});
		$menu->add_selectionnable(0, 1, 9, 8, "Choose level", function () {
			return $this->level_menu->use_menu();
		});
		$menu->add_selectionnable(0, 2, 12, 10, "Credits", function () {
			return $this->credit_menu->use_menu();
		});
		$this->main_menu = $menu;
	}

	private function set_level_menu() {
		$menu = new Menu($this->config);

		// get number of existing levels
		$level_count = 0;
		do {
			$level_count++;
			$path = "Levels/Level_" . str_pad($level_count, 2, "0", STR_PAD_LEFT) . ".txt";
		} while (file_exists($path));
		$level_count--;

		// set up background
		$intra_width = 1 + min($level_count, 10) * 5;
		$title_offset = max(0, floor(($intra_width - 21) / 2));
		$menu_height = ceil($level_count / 10);
		$menu->set_background(
			str_repeat(" ", 2 + $title_offset) . "╔═════════════════════╗\n" .
			str_repeat(" ", 2 + $title_offset) . "║ CHOOSE YOUR LEVEL ! ║\n" .
			str_repeat(" ", 2 + $title_offset) . "╚═════════════════════╝\n\n" .
			"  ╔" . str_repeat("═", $intra_width) . "╗\n" .
			str_repeat("  ║" . str_repeat(" ", $intra_width) . "║\n", $menu_height) .
			"  ╚" . str_repeat("═", $intra_width) . "╝\n"
		);

		$actual_level = 1;
		for ($y = 0; true; $y++) {
			for ($x = 0; $x < 10; $x++) {
				$menu->add_selectionnable(
					$x,
					$y,
					6 + $x * 5,
					6 + $y,
					str_pad($actual_level, 2, " ", STR_PAD_LEFT),
					function ($level) {
						$this->game->start_game($level);
					},
					$actual_level
				);

				$actual_level++;
				if ($actual_level > $level_count) {
					break 2;
				}
			}
		}

		$this->level_menu = $menu;
	}

	private function set_credit_menu() {
		$menu = new Menu($this->config);

		$menu->set_background(
			"  ╔══════╗\n" .
			"  ║      ║\n" .
			"  ╚══════╝\n" .
			"\n" .
			"Jeux développer par Baptiste ROYER.\n" .
			"Encouragement de son fidèle ami, Yoann JOB.\n" .
			"Encouragement des jeunes Tech 1 :\n" .
			"Corentin, Nathan, Axel, Mathéo, Thomas, Alan." .
			"Suivi par Stanislas Hegron, dans le cadre de la formation Web@cademie.\n" .
			"Vous êtes tous des beau gosses !!!\n"
		);

		$menu->add_selectionnable(0, 0, 5, 2, "BACK", function () {
			return $this->main_menu->use_menu();
		});

		$this->credit_menu = $menu;
	}
}