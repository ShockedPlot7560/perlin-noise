<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise\utils;

class Math {
	public static function smoothStep(float $d) : float {
		return $d * $d * $d * ($d * ($d * 6.0 - 15.0) + 10.0);
	}

	public static function lerp(float $delta, float $start, float $end) : float {
		return $start + $delta * ($end - $start);
	}

	public static function lerp2(float $deltaX, float $deltaY, float $val00, float $val10, float $val01, float $val11) : float {
		return self::lerp($deltaY, self::lerp($deltaX, $val00, $val10), self::lerp($deltaX, $val01, $val11));
	}

	public static function lerp3(float $deltaX, float $deltaY, float $deltaZ, float $val000, float $val100, float $val010, float $val110, float $val001, float $val101, float $val011, float $val111) : float {
		return self::lerp($deltaZ, self::lerp2($deltaX, $deltaY, $val000, $val100, $val010, $val110), self::lerp2($deltaX, $deltaY, $val001, $val101, $val011, $val111));
	}
}
