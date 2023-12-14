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
	public const SPIKE = DISP_SPIKE;
	public const HOLE = DISP_HOLE;
	public const AIR = DISP_AIR;
	public const BORDER = DISP_BORDER;

	private $Game_board = [];
	private $Board_Width = 1;
	private $Board_Height = 1;
	public $Player;

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
		$level_content = explode("\n", $level_content);

		$this->Board_Height = sizeof($level_content);
		$this->Board_Width = strlen($level_content[0]);

		foreach ($level_content as $line) {

			$line = preg_replace("/[^\d"
				. $this::MAP_START
				. $this::MAP_END
				. $this::MAP_BLOCK
				. "]/"
				, $this::AIR
				, $line
			);

			$line = substr($line, 0, $this->Board_Width);
			$line = str_pad($line
				, $this->Board_Width
				, $this::AIR
			);

			if (($pos = strpos($line, $this::MAP_START)) !== false) {
				$this->Player->setPosition($pos, sizeof($board));
			}

			$board[] = $line;
		}

		$this->Game_board = $board;

	}

	public function displayStartingBoard() {
		// Clear screen
		echo "\e[H\e[J";
		// Draw upper border
		echo (str_repeat($this::BORDER, $this->Board_Width + 2) . "\n");

		$board_size = sizeof($this->Game_board);
		for ($i = 0; $i < $board_size; $i++) {
			$line = $this->Game_board[$i];
			if ($i != $board_size - 1) {
				$line = preg_replace("/[1-9]/", $this::HOLE, $line);
			} else {
				$line = preg_replace("/[1-9]/", $this::AIR, $line);
			}
			$line = str_replace(
				[$this::MAP_END, $this::MAP_BLOCK, 0],
				[$this::END, $this::BLOCK, $this::SPIKE],
				$line);

			echo (""
				. $this::BORDER
				. $line
				. $this::BORDER
				. "\n"
			);
		}

		$under_border = $this->Game_board[$board_size - 1];
		for ($i = 0; $i <= strlen($under_border) - 1; $i ++) {
			$under_border[$i] = (preg_match("/[1-9]/", $under_border[$i]) ? $this::AIR : $this::BORDER);
		}

		echo $this::BORDER , $under_border , $this::BORDER;

		$this->Player->print_cursor();
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
	}
}