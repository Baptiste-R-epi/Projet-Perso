<?php

class Selectionnable
{
	private int $screenPositionX;
	private int $screenPositionY;
	private string $label;
	private $callback;
	public function __construct(string $label, int $x, int $y, ...$callback) {
		$this->label = $label;
		$this->screenPositionX = $x;
		$this->screenPositionY = $y;
		$this->callback = $callback;
	}

	public function basic_display() {
		echo "\e[" . $this->screenPositionY . ";" . $this->screenPositionX . "H";
		echo $this->label;
	}

	public function selected_display() {
		echo "\e[" . $this->screenPositionY . ";" . ($this->screenPositionX - 1) . "H";
		echo "\e[5m>\e[0m";
		echo "\e[7m" . $this->label . "\e[0m";
		echo "\e[5m<\e[0m";
	}
	public function get_callback() {
		return $this->callback;
	}
}