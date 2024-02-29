<?php
include_once "Level.php";
include_once "Config.php";
include_once "Player.php";

class Game
{
	private Config $config;
	private int $level;
	private Level $Level_board;
	private Player $player;
	public function __construct($config, $level = 1) {
		$this->player = new Player;
		$this->Level_board = new Level($config);
		$this->player->set_level($this->Level_board);
		$this->Level_board->setPlayer($this->player);
		$this->config = $config;
		$this->level = $level;
	}

	public function load_level($level) {
		$path = "Levels/Level_" . str_pad($level, 2, "0", STR_PAD_LEFT) . ".txt";
		if (!file_exists($path)) {
			return false;
		}

		$this->Level_board->setBoard(file_get_contents($path));
		$this->Level_board->displayBoard();
		return true;
	}

	function play_game() {
		while ($this->load_level($this->level)) {
			while (!$this->Level_board->isWin()) {
				usleep(100000);
				$order = $this->config->get_order();

				if ($this->Level_board->isLost()) {
					$this->player->setPosition($this->Level_board->starting_position);
					$this->Level_board->Score -= 2;
				}

				if ($order == "ESC") {
					return false;
				}
				
				$this->Level_board->play($order);
				$this->Level_board->displayBoard();
				$this->config->display_data_next_cycle();

			}
			$this->level++;
		}
		return true;
	}

	function end_congrate() {
		// Clear screen
		echo "\e[H\e[J";

		// Draw Congrate
		echo "\e[3;10H";
		echo "CONGRATULATION !";

		echo "\e[4;6H";
		echo "YOU BEAT ALL THE LEVELS";

		// Draw score
		echo "\e[6;10H";
		echo "YOUR SCORE : " . $this->Level_board->Score;

		// Draw press key
		echo "\e[14;1H";
		echo "PRESS ENTER OR ESC TO END";

		do {
			usleep(100000);
			$order = $this->config->get_order();
			$this->config->display_data_next_cycle();

			// Store variables
			$border = $this->config->get_display_char_from_type("BORDER");
			$block = $this->config->get_display_char_from_type("BLOCK");
			$player = $this->config->get_display_char_from_type("CURSOR");
			$coin = $this->config->get_display_char_from_type("SCORE_COIN");
			$spike = $this->config->get_display_char_from_type("SPIKE");

			// Draw upper border
			echo "\e[1;1H";
			echo str_repeat($border, 33);

			// Draw left & right border
			for ($i = 2; $i <= 11; $i++) {
				echo "\e[" . $i . ";1H";
				echo $border;
				echo "\e[" . $i . ";33H";
				echo $border;
			}

			// Draw lower border
			echo "\e[12;1H";
			echo str_repeat($border, 33);

			// Draw stage
			echo "\e[8;15H";
			echo $coin . $coin . $player . $coin . $coin;

			echo "\e[9;14H";
			echo str_repeat($block, 7);

			echo "\e[10;13H";
			echo $block;
			echo "\e[10;21H";
			echo $block;

			echo "\e[11;13H";
			echo $block . str_repeat($spike, 7) . $block;

			echo "\e[16;13H";
		} while ($order != "ENTER" && $order != "ESC");
	}

	public function start_game() {
		stream_set_blocking(STDIN, 0);
		system("stty cbreak -echo");
		echo "\e[?25l";

		if ($this->play_game()) {
			$this->end_congrate();
		}

		system("stty cbreak echo");
		echo "\e[?25h\n\e[A";
	}
}