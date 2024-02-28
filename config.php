<?php

// The DISPLAY config defines what the game will looks like.
// String must be at least 1 character long.

const
	DISPLAY_CURSOR = "ǿǿǿOOO",
	DISPLAY_END = "*",
	DISPLAY_BLOCK = "#",
	DISPLAY_END_COIN = "CCOO00",
	DISPLAY_SPIKE = "^",
	DISPLAY_AIR = " ",
	DISPLAY_BUMPER = "|",
	DISPLAY_BORDER = "#";


// The MAP config defines what must be fed in the levels to be recognize as different elements.
// String must be 1 character long. Do not use strange characters like "ǿ".

const
	MAP_START = "S",
	MAP_END = "E",
	MAP_BLOCK = "#",
	MAP_END_COIN = "C",
	MAP_SPIKE = "0",
	MAP_BUMPER = "B";


// The CONTROL config defines what keys in the keyboard are link to what action.
// As a default, the arrow keys are linked to the corresponding direction, and escapde quit the game.
// CONTROL_UP is for navigating in menu and jumping in game.
// CONTROL_DOWN is only usefull in menu.

const 
	CONTROL_LEFT = "aq",
	CONTROL_RIGHT = "d",
	CONTROL_UP = "zw ",
	CONTROL_DOWN = "s";