<?php
include_once("Level.php");

class Game
{
	private $level;
	private $Level_board;
	public function __construct() {
		$this->level = 1;
		$this->Level_board = new Level();
		$this->startGame();
	}

	public function loadLevel($level) {
		$path = "Levels/Level_" . str_pad($level, 2, "0", STR_PAD_LEFT) . ".txt";
		if (!file_exists($path)) {
			return false;
		}

		$this->Level_board->setBoard(file_get_contents($path));
		$this->Level_board->displayStartingBoard();
		return true;
	}

	public function getOrder() {
		$key = fgets(STDIN);
		$result = [];
		if(preg_match("/\e(\[[ABCD])?/", $key, $result) != 1) {
			return "NONE";
		}
		
		switch ($result[0]) {
			case "\e":
				return "ESC";
			case "\e[A":
				return "UP";
			case "\e[C":
				return "RIGHT";
			case "\e[D":
				return "LEFT";
		}
		return "NONE";
	}

	public function startGame() {
		stream_set_blocking(STDIN, 0);
		system("stty cbreak -echo");
		echo "\e[?25l";

		while ($this->loadLevel($this->level)) {
			while (!$this->Level_board->Player->isWin()) {
				usleep(200000);
				$order = $this->getOrder();

				if ($order == "ESC") {
					break 2;
				}
				$this->Level_board->play($order);
			}
			$this->level ++;
		}

		echo "\e[?25h\n\e[A";
	}
}