<?php

// The DISP config defines what the game will looks like
// String must be 1 character long

const
	DISP_CURSOR = "ǿ",
	DISP_END = "*",
	DISP_BLOCK = "#",
	DISP_AIR = " ",
	DISP_BORDER = "#";


// The MAP config defines what must be feed in the levels to be recognize as different elements
// Since it is used in a regex, please do not use . / * + - or anything which might conflict
// String must be 1 character long

const
	MAP_START = "S",
	MAP_END = "E",
	MAP_BLOCK = "#";