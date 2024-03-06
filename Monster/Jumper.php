<?php

include_once "Class/Monster_Template.php";

class Jumper extends Monster_Template
{
	const SLOWNESS = 8;
	const TYPE = "crawler";
	const CAN_FALL = false;
	const SPRITE = "^^^vv___";
	const COLOR = "0";
	const MAP_DISPLAY = "J";
	const ORDERS = [
		"UP"
	];
}