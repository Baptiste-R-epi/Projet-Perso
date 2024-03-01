<?php

include_once "Class/Menu manager/Menus_Manager.php";

stream_set_blocking(STDIN, 0);
system("stty cbreak -echo");
echo "\e[?25l";

$menu = new Menus_Manager(new Config);
$menu->get_started();

system("stty cbreak echo");
echo "\e[?25h\n\e[A";