<?php

class Level
{
	private $config;
	private $Game_board = [];
	private $End_Coin = 0;
	private $Board_Width = 1;
	private $Board_Height = 1;
	private $player;

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
		$this->End_Coin = 0;
		$level_content = explode("\n", $level_content);

		$this->Board_Height = sizeof($level_content);
		$this->Board_Width = strlen($level_content[0]);

		for ($i = 0; $i < $this->Board_Height; $i++) {
			$line = mb_str_split($level_content[$i]);
			for ($j = 0; $j < $this->Board_Width; $j++) {
				@$line[$j] = $this->config->get_type_from_map($line[$j]);
				switch ($line[$j]) {
					case "COIN":
						$this->End_Coin++;
						break;
					case "START":
						$this->player->setPosition($j, $i);
						break;
				}
			}
			$this->Game_board[] = $line;
		}
	}

	public function empty_cell(int $x, int $y) {
		$this->Game_board[$y][$x] = "AIR";
	}

	public function displayBoard() {
		// Clear screen
		echo "\e[H\e[J";
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
		
		echo "\e[" . 2 + $player[1] . ";" . 2 + $player[0] . "H" . $this->config->get_display_char_from_type("CURSOR");

	}

	public function isWin() {
		return (
			$this->End_Coin == 0 &&
			$this->player->getCell() == "END"
		);
	}

	public function isLost() {
		return $this->player->getCell() == "SPIKE";
	}

	public function collectCoin() {
		if ($this->player->getCell() != "COIN")
			return;
		$player_pos = $this->player->getPosition();
		$this->empty_cell($player_pos[0], $player_pos[1]);
		$this->End_Coin--;
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
	}
}