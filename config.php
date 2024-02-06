<?php

// The DISPLAY config defines what the game will looks like.
// String must be at least 1 character long.
// Holes put on the ground will not display, but will also not display the cases below.

const
	DISPLAY_CURSOR = "ǿ",
	DISPLAY_END = "*",
	DISPLAY_BLOCK = "#",
	DISPLAY_END_COIN = "C",
	DISPLAY_AIR = " ",
	DISPLAY_BORDER = "#",
	DISPLAY_SPIKE = "^",
	DISPLAY_HOLE = "_";


// The MAP config defines what must be fed in the levels to be recognize as different elements.
// Since it is used in a regex, please do not use . / * + - or anything which might conflict.
// String must be 1 character long.
// Spike are always a 0. Holes are a number equal to the number of levels you loose.

const
	MAP_START = "S",
	MAP_END = "E",
	MAP_BLOCK = "#",
	MAP_END_COIN = "C";


// The CONTROL config defines what keys in the keyboard are link to what action.
// As a default, the arrow keys are linked to the corresponding direction, and escapde quit the game.
// CONTROL_UP is for navigating in menu and jumping in game.
// CONTROL_DOWN is only usefull in menu.

const 
	CONTROL_LEFT = "aq",
	CONTROL_RIGHT = "d",
	CONTROL_UP = "zw ",
	CONTROL_DOWN = "s";