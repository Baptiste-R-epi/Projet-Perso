<?php
function math_lcm(...$inputs) {
	$inputs = array_unique($inputs);
	if (in_array(0, $inputs))
		return 0;

	foreach ($inputs as $key => $value) {
		$value = intval($value);
		if ($value == 1) {
			unset($inputs[$key]);
		} else {
			$inputs[$key] = $value;
		}
	}

	$multiple = false;
	$lcm = 1;

	do {
		$multiple = false;
		foreach ($inputs as $key => $value) {
			if ($value % 2 == 0) {
				$value /= 2;
				$multiple = true;

				if ($value == 1) {
					unset($inputs[$key]);
				} else {
					$inputs[$key] = $value;
				}
			}
		}

		if ($multiple)
			$lcm *= 2;

	} while ($multiple);

	for ($i = 3; $inputs != []; $i += 2) {
		do {
			$multiple = false;
			foreach ($inputs as $key => $value) {
				if ($value % $i == 0) {
					$value /= $i;
					$multiple = true;
					if ($value == 1) {
						unset($inputs[$key]);
					} else {
						$inputs[$key] = $value;
					}
				}
			}

			if ($multiple)
				$lcm *= $i;

		} while ($multiple);
	}
	return $lcm;
}