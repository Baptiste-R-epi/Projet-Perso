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
	public function isCellFree($x, $y) {
		// @ ignore warning, allowing to quickly check if it is outside borders
		@$cell = $this->Level->getBoard()[$this->Pos_y + $y][$this->Pos_x + $x];
		return $cell == null ?
			false :
			$cell != $this->Level::BLOCK;
	}
	public function isOnGround() {
		return !$this->isCellFree(0, 1);
	}
	public function moveCursor($x, $y) {
		$this->clean_position();
		$this->Pos_x += $x;
		$this->Pos_y += $y;
		$this->print_cursor();
	}

	public function moveLeft() {
		if ($this->isCellFree(-1, 0)) {
			$this->moveCursor(-1, 0);
		} elseif (
			$this->isCellFree(0, -1) &&
			$this->isCellFree(-1, -1) && (
				$this->isOnGround() ||
				$this->jump > 0
			)
		) {
			$this->moveCursor(-1, -1);
			$this->jump = 0;
		}
	}

	public function moveRight() {
		if ($this->isCellFree(1, 0)) {
			$this->moveCursor(1, 0);
		} elseif (
			$this->isCellFree(0, -1) &&
			$this->isCellFree(1, -1) && (
				$this->isOnGround() ||
				$this->jump > 0
			)
		) {
			$this->moveCursor(1, -1);
			$this->jump = 0;
		}
	}

	public function moveUp() {
		if ($this->isOnGround()) {
			$this->jump = 3;
		}
	}

	public function gravity() {
		if ($this->isWin()) {
			return;
		}

		if ($this->jump > 0) {
			if ($this->isCellFree(0, -1)) {
				$this->jump--;
				$this->moveCursor(0, -1);
				return;
			}
			$this->jump = 0;
		}
		if ($this->isCellFree(0, 1)) {
			$this->moveCursor(0, 1);
		}
	}

	public function isWin() {
		return $this->Level->getBoard()[$this->Pos_y][$this->Pos_x] == $this->Level::MAP_END;
	}

	public function isLost() {
		if (is_numeric($back = $this->Level->getBoard()[$this->Pos_y][$this->Pos_x])) {
			return $back;
		}
		return false;
	}
}