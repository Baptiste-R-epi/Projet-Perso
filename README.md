Welcome to this little game project.

It is a CLI based game. You move yourself around with the arrow keys. You cannot "move down", and moving up makes you jump. Space is not actually supported.

The "config.php" file allows you to edit the way the map must be build, and how the game display. Check the file, comments are explicite enough.

Levels can be freely created, named Level_01, Level_02 ... up to Level_99 if you wish.
    There is an example of how a level is build. An empty line is regognized as a fully empty line by the game.
    The screen's width is dictated by the first line's width.
    Anything that is not regognized as either start, end or block, will be empty.
    There is no need to fill entire lines. If you have nothing but empty cases to fill in a line, just leave it empty.
