<?php

include_once "Class/Monster_Template.php";

class Jumper extends Monster_Template
{
	const SLOWNESS = 8;
	const TYPE = "crawler";
	const CAN_FALL = false;
	const SPRITE = "11111___";
	const MAP_DISPLAY = "J";
	const ORDERS = [
		"UP"
	];
}