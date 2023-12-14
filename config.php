<?php

// The DISP config defines what the game will looks like
// String must be 1 character long
// Holes put on the ground will not display, but will also not display the cases below.

const
	DISP_CURSOR = "ǿ",
	DISP_END = "*",
	DISP_BLOCK = "#",
	DISP_AIR = " ",
	DISP_BORDER = "#",
	DISP_SPIKE = "^",
	DISP_HOLE = "_";


// The MAP config defines what must be feed in the levels to be recognize as different elements
// Since it is used in a regex, please do not use . / * + - or anything which might conflict
// String must be 1 character long
// Spike are always a 0. Holes are a number equal to the number of levels you loose.

const
	MAP_START = "S",
	MAP_END = "E",
	MAP_BLOCK = "#";