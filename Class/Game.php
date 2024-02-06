<?php
include_once("Level.php");
include_once("Config.php");

class Game
{
	private $level;
	private $Level_board;
	public function __construct($level = 1) {
		$this->level = $level;
		$this->Level_board = new Level();
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
		preg_match("/\e(\[[ABCD])?|["."t"."]|/", $key, $result);

		switch ($result[0]) {
			case "\e":
				return "ESC";
			case " ":
			case "z":
			case "w":
			case "\e[A":
				return "UP";
			case "d":
			case "\e[C":
				return "RIGHT";
			case "q":
			case "a":
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
			while (!$this->Level_board->isWin()) {
				usleep(100000);
				$order = $this->getOrder();

				if (($back = $this->Level_board->isLost()) !== false) {
					$this->level -= 1 + $back;
					break;
				}
				
				if ($order == "ESC") {
					break 2;
				}
				$this->Level_board->play($order);

			}
			$this->level <= 0 ? $this->level = 1 : $this->level++;
		}
		system("stty cbreak echo");
		echo "\e[?25h\n\e[A";
	}
}