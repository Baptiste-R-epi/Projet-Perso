<?php

include_once "Level.php";
class Monster_Template
{
	private Level $Level;
	private int $posX;
	private int $posY;
	private int $jump = 0;
	private int $actualFrame;
	const SLOWNESS = 1;
	// The 3 types are crawler, climber and flighter
	const TYPE = "crawler";
	const CAN_FALL = false;
	const SPRITE = "";
	const COLOR = "0";
	const MAP_DISPLAY = "";
	const ORDERS = ["WAIT"];
	private array $actualOrder;

	private string $move_left = "";
	private string $move_right = "";
	private string $move_down = "";
	private string $move_up = "";
	private string $gravity = "";
	private function move_left() {
		if (method_exists($this, $this->move_left)) {
			$order = $this->move_left;
			return $this->$order();
		}
		return false;
	}
	private function move_right() {
		if (method_exists($this, $this->move_right)) {
			$order = $this->move_right;
			return $this->$order();
		}
		return false;
	}
	private function move_down() {
		if (method_exists($this, $this->move_down)) {
			$order = $this->move_down;
			return $this->$order();
		}
		return false;
	}
	private function move_up() {
		if (method_exists($this, $this->move_up)) {
			$order = $this->move_up;
			return $this->$order();
		}
		return false;
	}

	public function gravity() {
		if (method_exists($this, $this->gravity)) {
			$order = $this->gravity;
			return $this->$order();
		}
		return false;
	}

	public function __construct() {
		switch (true) {
			default:
			case $this::TYPE == "crawler" && !$this::CAN_FALL:
				$this->move_left = "crawler_no_fall_move_left";
				$this->move_right = "crawler_no_fall_move_right";
				$this->move_up = "not_flighter_move_up";
				$this->gravity = "basic_gravity";
				break;
			case $this::TYPE == "crawler" && $this::CAN_FALL:
				$this->move_left = "crawler_can_fall_move_left";
				$this->move_right = "crawler_can_fall_move_right";
				$this->move_up = "not_flighter_move_up";
				$this->gravity = "basic_gravity";
				break;
			case $this::TYPE == "climber" && !$this::CAN_FALL:
				$this->move_left = "climber_no_fall_move_left";
				$this->move_right = "climber_no_fall_move_right";
				$this->move_up = "not_flighter_move_up";
				$this->gravity = "basic_gravity";
				break;
			case $this::TYPE == "climber" && $this::CAN_FALL:
				$this->move_left = "climber_can_fall_move_left";
				$this->move_right = "climber_can_fall_move_right";
				$this->move_up = "not_flighter_move_up";
				$this->gravity = "basic_gravity";
				break;
			case $this::TYPE == "flighter":
				$this->move_left = "flighter_move_left";
				$this->move_right = "flighter_move_right";
				$this->move_up = "flighter_move_up";
				$this->move_down = "flighter_move_down";
				break;
		}

		$this->actualFrame = $this::SLOWNESS - 1;
		$order = explode(" ", $this::ORDERS[0]);
		$this->actualOrder = [
			"position" => 0,
			"order" => $order[0],
			"repeat" => intval($order[1] ?? 0)
		];
	}

	public function play() {
		if (++$this->actualFrame != $this::SLOWNESS) {
			return;
		}

		$this->actualFrame = 0;

		switch ($this->actualOrder["order"]) {
			case "LEFT":
				if (
					!$this->move_left() ||
					--$this->actualOrder["repeat"] == 0
				) {
					$this->next_order();
				}
				break;
			case "RIGHT":
				if (
					!$this->move_right() ||
					--$this->actualOrder["repeat"] == 0
				) {
					$this->next_order();
				}
				break;
			case "UP":
				if (
					!$this->move_up() ||
					--$this->actualOrder["repeat"] == 0
				) {
					$this->next_order();
				}
				break;
			case "DOWN":
				if (
					!$this->move_down() ||
					--$this->actualOrder["repeat"] == 0
				) {
					$this->next_order();
				}
				break;
			case "WAIT":
				if (
					--$this->actualOrder["repeat"] == 0
				) {
					$this->next_order();
				}
				break;
			default:
				$this->next_order();
		}
	}

	private function next_order() {
		$position = ($this->actualOrder["position"] + 1) % sizeof($this::ORDERS);
		$order = explode(" ", $this::ORDERS[$position]);

		$this->actualOrder = [
			"position" => $position,
			"order" => $order[0],
			"repeat" => ($order[1] ?? -1)
		];
	}

	public function set_level(Level $level) {
		$this->Level = $level;
	}
	public function set_position(array $pos) {
		$this->posX = $pos[0];
		$this->posY = $pos[1];
		$this->jump = 0;
	}

	public function get_position() {
		return [
			$this->posX,
			$this->posY
		];
	}
	private function is_cell_free($x, $y) {
		// @ ignore warning, allowing to quickly check if it is outside borders

		@$cell = $this->Level->getBoard()[$this->posY + $y][$this->posX + $x];
		return($cell !== null && $cell !== "BLOCK" && $cell !== "BUMPER");
	}

	private function is_on_ground() {
		return !$this->is_cell_free(0, 1);
	}

	public function get_cell($x = 0, $y = 0) {
		return $this->Level->getBoard()[$this->posY + $y][$this->posX + $x] ?? "BORDER";
	}
	private function move_monster($x, $y) {
		$this->posX += $x;
		$this->posY += $y;
	}

	private function flighter_move_left() {
		if ($this->is_cell_free(-1, 0)) {
			$this->move_monster(-1, 0);
			return true;
		}
		return false;
	}

	private function climber_no_fall_move_left() {
		if (
			$this->is_cell_free(0, -1) &&
			$this->is_cell_free(-1, -1) &&
			!$this->is_cell_free(-1, 0)
		) {
			$this->move_monster(-1, -1);
			return true;
		}

		if (
			$this->is_cell_free(-1, 0) && (
				!$this->is_cell_free(-1, 1) ||
				!$this->is_on_ground()
			)
		) {
			$this->move_monster(-1, 0);
			return true;
		}

		if (
			$this->is_cell_free(-1, 0) &&
			$this->is_cell_free(-1, 1) &&
			!$this->is_cell_free(-1, 2)
		) {
			$this->move_monster(-1, 1);
			return true;
		}

		return false;

	}

	private function climber_can_fall_move_left() {
		if ($this->is_cell_free(-1, 0)) {
			$this->move_monster(-1, 0);
		} elseif (
			$this->is_cell_free(0, -1) &&
			$this->is_cell_free(-1, -1) && (
				$this->is_on_ground() ||
				$this->jump > 0
			)
		) {
			$this->move_monster(-1, -1);
			$this->jump = 0;
		} else {
			return false;
		}
		return true;
	}

	private function crawler_no_fall_move_left() {
		if (
			$this->is_cell_free(-1, 0) && (
				!$this->is_cell_free(-1, 1) ||
				!$this->is_on_ground()
			)
		) {
			$this->move_monster(-1, 0);
			return true;
		}
		return false;
	}

	private function crawler_can_fall_move_left() {
		if ($this->is_cell_free(-1, 0)) {
			$this->move_monster(-1, 0);
			return true;
		}
		return false;
	}

	private function flighter_move_right() {
		if ($this->is_cell_free(1, 0)) {
			$this->move_monster(1, 0);
			return true;
		}
		return false;
	}

	private function climber_no_fall_move_right() {
		if (
			$this->is_cell_free(0, -1) &&
			$this->is_cell_free(1, -1) &&
			!$this->is_cell_free(1, 0) && (
				$this->is_on_ground() ||
				$this->jump > 0
			)
		) {
			$this->move_monster(1, -1);
			return true;
		}

		if (
			$this->is_cell_free(1, 0) && (
				!$this->is_cell_free(1, 1) ||
				!$this->is_on_ground()
			)
		) {
			$this->move_monster(1, 0);
			return true;
		}

		if (
			$this->is_cell_free(1, 0) &&
			$this->is_cell_free(1, 1) &&
			!$this->is_cell_free(1, 2)
		) {
			$this->move_monster(1, 1);
			return true;
		}

		return false;

	}

	private function climber_can_fall_move_right() {
		if ($this->is_cell_free(1, 0)) {
			$this->move_monster(1, 0);
		} elseif (
			$this->is_cell_free(0, -1) &&
			$this->is_cell_free(1, -1) && (
				$this->is_on_ground() ||
				$this->jump > 0
			)
		) {
			$this->move_monster(1, -1);
			$this->jump = 0;
		} else {
			return false;
		}
		return true;
	}

	private function crawler_no_fall_move_right() {
		if (
			$this->is_cell_free(1, 0) && (
				!$this->is_cell_free(1, 1) ||
				!$this->is_on_ground()
			)
		) {
			$this->move_monster(1, 0);
			return true;
		}
		return false;
	}

	private function crawler_can_fall_move_right() {
		if ($this->is_cell_free(1, 0)) {
			$this->move_monster(1, 0);
			return true;
		}
		return false;
	}

	private function flighter_move_up() {
		if ($this->is_cell_free(0, -1)) {
			$this->move_monster(0, -1);
			return true;
		}
		return false;
	}

	private function not_flighter_move_up() {
		if (
			$this->is_on_ground() &&
			$this->is_cell_free(0, -1)
		) {
			$this->jump = 3;
			return true;
		}
		return false;
	}

	private function flighter_move_down() {
		if ($this->is_cell_free(0, 1)) {
			$this->move_monster(0, 1);
			return true;
		}
		return false;
	}

	private function basic_gravity() {

		if ($this->jump > 0) {
			if ($this->is_cell_free(0, -1)) {
				$this->jump--;
				$this->move_monster(0, -1);
				return;
			}
			$this->jump = 0;
		}
		if (!$this->is_on_ground()) {
			$this->move_monster(0, 1);
		}

		if ($this->get_cell(0, 1) == "BUMPER") {
			$this->jump = 6;
		}
	}
}