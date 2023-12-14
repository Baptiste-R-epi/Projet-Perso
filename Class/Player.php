<?php

include_once("Level.php");
include_once("config.php");
class Player
{
	private $Level;
	private $Pos_x;
	private $Pos_y;
	private $jump = 0;
	public const CURSOR = DISP_CURSOR;

	public function __construct(Level $level) {
		$this->Level = $level;
	}
	public function setPosition($x, $y) {
		$this->Pos_x = $x;
		$this->Pos_y = $y;
	}

	public function clean_position() {
		echo "\e[" . 2 + $this->Pos_y . ";" . 2 + $this->Pos_x . "H" . $this->Level::AIR;
	}

	public function print_cursor() {
		echo "\e[" . 2 + $this->Pos_y . ";" . 2 + $this->Pos_x . "H" . $this::CURSOR;
	}

	public function moveLeft() {
		if ($this->Pos_x <= 0) {
			return;
		}

		if ($this->Level->getBoard()[$this->Pos_y][$this->Pos_x - 1] == $this->Level::MAP_BLOCK) {
			return;
		}

		$this->clean_position();
		$this->Pos_x--;
		$this->print_cursor();


	}

	public function moveRight() {
		if ($this->Pos_x >= $this->Level->getWidth() - 1) {
			return;
		}

		if ($this->Level->getBoard()[$this->Pos_y][$this->Pos_x + 1] == $this->Level::MAP_BLOCK) {
			return;
		}

		$this->clean_position();
		$this->Pos_x++;
		$this->print_cursor();

	}

	public function moveUp() {
		if (
			$this->Pos_y == $this->Level->getHeight() - 1 ?
			true :
			($this->Level->getBoard()[$this->Pos_y + 1][$this->Pos_x] == $this->Level::MAP_BLOCK ?
				true : false) // Equal OR, but without warning
		) {
			$this->jump = 3;
		}

	}

	public function gravity() {
		if ($this->isWin()) {
			return;
		}

		if ($this->jump > 0) {
			if (
				$this->Pos_y > 0 ?
				$this->Level->getBoard()[$this->Pos_y - 1][$this->Pos_x] != $this->Level::MAP_BLOCK ?
				true : false : false // Equal AND, but without warning
			) {
				$this->jump--;

				$this->clean_position();
				$this->Pos_y--;
				$this->print_cursor();
				return;
			} else {
				$this->jump = 0;
			}
		}
		if (
			$this->Pos_y != $this->Level->getHeight() - 1 &&
			$this->Level->getBoard()[$this->Pos_y + 1][$this->Pos_x] != $this->Level::MAP_BLOCK
		) {
			$this->clean_position();
			$this->Pos_y++;
			$this->print_cursor();
		}
	}

	public function isWin() {
		return $this->Level->getBoard()[$this->Pos_y][$this->Pos_x] == $this->Level::MAP_END;
	}
}