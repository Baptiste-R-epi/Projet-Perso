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
					$this->Level_board->Score--;
				}

				if ($order == "ESC") {
					break 2;
				}
				$this->Level_board->play($order);
				$this->Level_board->displayBoard();
				$this->config->display_data_next_cycle();

			}
			$this->level++;
		}
	}

	public function start_game() {
		stream_set_blocking(STDIN, 0);
		system("stty cbreak -echo");
		echo "\e[?25l";

		$this->play_game();
		system("stty cbreak echo");
		echo "\e[?25h\n\e[A";
	}
}