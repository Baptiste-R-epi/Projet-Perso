Welcome to this little game project.

It is a CLI based game. You move yourself around with the arrow keys, zqsd/awsd, space. You cannot "move down", and moving up makes you jump. Space is a jump too. Escape exit the game.

The "config.php" file allows you to edit the way the map must be build, and how the game display. Check the file, comments are explicite enough. It also provides more information about some features.

Levels can be freely created, named Level_01.txt, Level_02.txt ... up to Level_99.txt if you wish.
    There is an example of how a level is build. An empty line is regognized as a fully empty line by the game.
    The screen's width is dictated by the first line's width.
    Anything that is not regognized as either start, end or block, will be empty.
    There is no need to fill entire lines. If you have nothing more but empty cases to fill in a line, just leave it empty.

Breaking the game with CTRL-C will hide your cursor.
	To quickly solve this, launch the game again and hit the escape key.
	Alternatively, enter this command : echo "\e[?25h"