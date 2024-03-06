<?php
include_once "Config.php";

class Level
{
	private Config $config;
	private array $Game_board = [];
	private int $End_Coin = 0;
	public int $Score = 0;
	private int $Board_Width = 1;
	private int $Board_Height = 1;
	public array $starting_position = [0, 0];
	private Player $player;
	private array $Monsters = [];

	public function __construct(Config $config) {
		$this->config = $config;
	}

	public function setPlayer(PLayer $player) {
		$this->player = $player;
	}

	public function getBoard() {
		return $this->Game_board;
	}

	public function getWidth() {
		return $this->Board_Width;
	}
	public function getHeight() {
		return $this->Board_Height;
	}

	public function setBoard(string $level_content) {
		$this->Game_board = [];
		$this->Monsters = [];
		$this->End_Coin = 0;
		$level_content = explode("\n", $level_content);

		$this->Board_Height = sizeof($level_content);
		$this->Board_Width = strlen($level_content[0]);

		for ($i = 0; $i < $this->Board_Height; $i++) {
			$line = mb_str_split($level_content[$i]);
			for ($j = 0; $j < $this->Board_Width; $j++) {
				@$map_type = $this->config->get_type_from_map($line[$j]);
				switch ($map_type) {
					case "END_COIN":
						$this->End_Coin++;
						break;
					case "START":
						$this->starting_position = [$j, $i];
						break;
					case "OTHER":
						@$monster_class = $this->config->get_monster_from_map($line[$j]);
						if ($monster_class) {
							$new_monster = new $monster_class();
							$new_monster->set_level($this);
							$new_monster->set_position([$j, $i]);
							$this->Monsters[] = $new_monster;
						}
						$map_type = "AIR";
				}
				$line[$j] = $map_type;
			}
			$this->Game_board[] = $line;
		}
		$this->player->setPosition($this->starting_position);
	}

	public function empty_cell(int $x, int $y) {
		$this->Game_board[$y][$x] = "AIR";
	}

	public function displayBoard() {
		// Clear screen
		echo "\e[H\e[J";

		// Draw score
		echo $this->config->get_display_char_from_type("SCORE_COIN") . " : " . $this->Score . "\n\n";

		// Draw upper border
		$border = $this->config->get_display_char_from_type("BORDER");
		echo (str_repeat($border, $this->Board_Width + 2) . "\n");

		// Draw main screen
		for ($i = 0; $i < $this->Board_Height; $i++) {
			echo $border;
			for ($j = 0; $j < $this->Board_Width; $j++) {
				echo $this->config->get_display_char_from_type(
					$this->Game_board[$i][$j]
				);
			}
			echo $border . "\n";
		}

		// Draw lower border
		echo (str_repeat($border, $this->Board_Width + 2) . "\n");

		// Draw player
		$player = $this->player->getPosition();
		echo "\e[" . 4 + $player[1] . ";" . 2 + $player[0] . "H" .
			$this->config->get_display_char_from_type("CURSOR");

		// Draw monsters
		foreach ($this->Monsters as $monster) {
			$position = $monster->get_position();
			echo "\e[" . 4 + $position[1] . ";" . 2 + $position[0] . "H" .
				$this->config->get_monster_display_from_name(get_class($monster));
		}
	}

	public function isWin() {
		return(
			$this->End_Coin == 0 &&
			$this->player->getCell() == "END"
		);
	}

	public function isLost() {
		if ($this->player->getCell() == "SPIKE") {
			return true;
		}
		foreach ($this->Monsters as $monster) {
			if ($monster->get_position() == $this->player->getPosition()) {
				return true;
			}
		}
		return false;
	}

	public function collectCoin() {
		$player_pos = $this->player->getPosition();

		switch ($this->player->getCell()) {
			case "END_COIN":
				$this->End_Coin--;
				$this->empty_cell($player_pos[0], $player_pos[1]);
				break;
			case "SCORE_COIN":
				$this->Score++;
				$this->empty_cell($player_pos[0], $player_pos[1]);
		}
	}

	public function play(string $action = "NONE") {
		switch ($action) {
			case "LEFT":
				$this->player->moveLeft();
				break;
			case "RIGHT":
				$this->player->moveRight();
				break;
			case "UP":
				$this->player->moveUp();
				break;
		}
		$this->player->gravity();
		$this->collectCoin();

		foreach ($this->Monsters as $monster) {
			$monster->play();
			$monster->gravity();
		}
	}
}