<?php

include_once("Player.php");
include_once("config.php");
class Level
{
	public const MAP_START = MAP_START;
	public const END = DISP_END;
	public const MAP_END = MAP_END;
	public const BLOCK = DISP_BLOCK;
	public const MAP_BLOCK = MAP_BLOCK;
	public const END_COIN = DISP_END_COIN;
	public const MAP_END_COIN = MAP_END_COIN;
	public const DISP_END_COIN = DISP_END_COIN;
	public const SPIKE = DISP_SPIKE;
	public const HOLE = DISP_HOLE;
	public const AIR = DISP_AIR;
	public const BORDER = DISP_BORDER;

	private $Game_board = [];
	private $End_Coin = 0;
	private $Board_Width = 1;
	private $Board_Height = 1;
	private $Player;

	public function getBoard() {
		return $this->Game_board;
	}

	public function getWidth() {
		return $this->Board_Width;
	}
	public function getHeight() {
		return $this->Board_Height;
	}

	public function __construct() {
		$this->Player = new Player($this);
	}
	public function setBoard($level_content) {
		$board = [];
		$this->End_Coin = 0;
		$level_content = explode("\n", $level_content);

		$this->Board_Height = sizeof($level_content);
		$this->Board_Width = strlen($level_content[0]);

		foreach ($level_content as $line) {
			// Extend or reduce line to match first line.
			$line = substr($line, 0, $this->Board_Width);
			$line = str_pad($line
				, $this->Board_Width
				, $this::AIR
			);

			// Place player at the start if it exists.
			if (($pos = strpos($line, $this::MAP_START)) !== false) {
				$this->Player->setPosition($pos, sizeof($board));
			}

			// Clear unreferrenced chars to empty space.
			$line = preg_replace("/[^\d"
				. $this::MAP_END
				. $this::MAP_BLOCK
				. $this::MAP_END_COIN
				. "]/"
				, $this::AIR
				, $line
			);

			// Count Ending coins.
			$this->End_Coin += substr_count($line, $this::MAP_END_COIN);


			$board[] = $line;
		}

		$this->Game_board = $board;

	}

	public function emptyCell($x, $y) {
		$this->Game_board[$y][$x] = $this::AIR;
	}

	public function displayStartingBoard() {
		// Clear screen
		echo "\e[H\e[J";
		// Draw upper border
		echo (str_repeat($this::BORDER, $this->Board_Width + 2) . "\n");

		for ($i = 0; $i < $this->Board_Height; $i++) {
			$line = $this->Game_board[$i];
			if ($i != $this->Board_Height - 1) {
				$line = preg_replace("/[1-9]/", $this::HOLE, $line);
			} else {
				$line = preg_replace("/[1-9]/", $this::AIR, $line);
			}
			$line = strtr($line
				, $this::MAP_END
				. $this::MAP_BLOCK
				. $this::MAP_END_COIN
				. "0"

				, ($this->End_Coin == 0 ? $this::END : $this::AIR)
				. $this::BLOCK
				. $this::END_COIN
				. $this::SPIKE
			);

			echo (""
				. $this::BORDER
				. $line
				. $this::BORDER
				. "\n"
			);
		}

		$under_border = $this->Game_board[$this->Board_Height - 1];
		for ($i = 0; $i <= strlen($under_border) - 1; $i++) {
			$under_border[$i] = (preg_match("/[1-9]/", $under_border[$i]) ? $this::AIR : $this::BORDER);
		}

		echo $this::BORDER, $under_border, $this::BORDER;

		$this->Player->print_cursor();
	}

	public function isWin() {
		return (
			$this->End_Coin == 0 &&
			$this->Player->getCell() == $this::MAP_END
		);
	}

	public function isLost() {
		if (is_numeric($back = $this->Player->getCell())) {
			return $back;
		}
		return false;
	}

	public function collectCoin() {
		if ($this->Player->getCell() != $this::END_COIN)
			return;
		$player_pos = $this->Player->getPosition();
		$this->emptyCell($player_pos[0], $player_pos[1]);
		$this->End_Coin--;

		if ($this->End_Coin == 0) {
			$this->displayStartingBoard();
		}
	}

	public function play($action = "NONE") {
		switch ($action) {
			case "LEFT":
				$this->Player->moveLeft();
				break;
			case "RIGHT":
				$this->Player->moveRight();
				break;
			case "UP":
				$this->Player->moveUp();
				break;
		}
		$this->Player->gravity();
		$this->collectCoin();
	}
}