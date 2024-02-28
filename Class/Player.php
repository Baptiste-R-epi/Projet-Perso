<?php

include_once("Level.php");
include_once("config.php");
class Player
{
	private Level $Level;
	private int $Pos_x;
	private int $Pos_y;
	private int $jump = 0;

	public function set_level(Level $level) {
		$this->Level = $level;
	}
	public function setPosition($x, $y) {
		$this->Pos_x = $x;
		$this->Pos_y = $y;
	}

	public function getPosition() {
		return [
			$this->Pos_x,
			$this->Pos_y
		];
	}

	public function isCellFree($x, $y) {
		// @ ignore warning, allowing to quickly check if it is outside borders

		@$cell = $this->Level->getBoard()[$this->Pos_y + $y][$this->Pos_x + $x];
		return ($cell !== null && $cell !== "BLOCK");
	}
	public function isOnGround() {
		return !$this->isCellFree(0, 1);
	}

	public function getCell() {
		return $this->Level->getBoard()[$this->Pos_y][$this->Pos_x];
	}
	public function moveCursor($x, $y) {
		$this->Pos_x += $x;
		$this->Pos_y += $y;
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
		if ($this->Level->isWin()) {
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
		if (!$this->isOnGround()) {
			$this->moveCursor(0, 1);
		}
	}
}