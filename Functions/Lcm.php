<?php
function math_lcm(...$inputs) {
	$inputs = array_unique($inputs);
	if (in_array(0, $inputs))
		return 0;
	$multiple = false;
	$lcm = 1;
	do {
		$multiple = false;
		foreach ($inputs as &$value) {
			if ($value % 2 == 0) {
				$value /= 2;
				$multiple = true;
			}
		}
		if ($multiple)
			$lcm *= 2;

	} while ($multiple);

	for ($i = 3; array_unique($inputs) != [1]; $i += 2) {
		do {
			$multiple = false;
			foreach ($inputs as &$value) {
				if ($value % $i == 0) {
					$value /= $i;
					$multiple = true;
				}
			}
			if ($multiple)
				$lcm *= $i;

		} while ($multiple);
	}
	return $lcm;
}