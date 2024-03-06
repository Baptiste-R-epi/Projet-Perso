<?php

include_once "Class/Monster_Template.php";

class Monkey extends Monster_Template {
	const SLOWNESS = 2;
	const TYPE = "climber";
	const CAN_FALL = false;
	const SPRITE = "nm";
	const MAP_DISPLAY = "M";
	const ORDERS = [
		"RIGHT",
		"LEFT"
	];
}