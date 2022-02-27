<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise;

use ShockedPlot7560\PerlinNoise\utils\Random;

use function floor;
use function sqrt;

class SimplexNoiseSampler {
	/** @var int[][] */
	public const GRAD = [[1, 1, 0], [-1, 1, 0], [1, -1, 0], [-1, -1, 0], [1, 0, 1], [-1, 0, 1], [1, 0, -1], [-1, 0, -1], [0, 1, 1], [0, -1, 1], [0, 1, -1], [0, -1, -1], [1, 1, 0], [0, -1, 1], [-1, 1, 0], [0, -1, -1]];
	public float $xOffset;
	public float $yOffset;
	public float $zOffset;

	private const SQRT_3 = sqrt(3.0);
	private const F2 = 0.5 * (self::SQRT_3 - 1.0);
	private const G2 = (3.0 - self::SQRT_3) / 6.0;
	/** @var int[] */
	private array $permutations;

	public function __construct(Random $random) {
		$this->xOffset = $random->nextFloat() * 256.0;
		$this->yOffset = $random->nextFloat() * 256.0;
		$this->zOffset = $random->nextFloat() * 256.0;

		for ($j = 0; $j < 256; $j++) {
			$k = $random->nextBoundedInt(256 - $j);
			$l = $this->permutations[$j];
			$this->permutations[$j] = $this->permutations[$j + $k];
			$this->permutations[$j + $k] = $l;
		}
	}

	/**
	 * @param int[] $gradElement
	 */
	public static function dot(array $gradElement, float $xFactor, float $yFactor, float $zFactor) : float {
		return (float) $gradElement[0] * $xFactor + (float) $gradElement[1] * $yFactor + (float) $gradElement[2] * $zFactor;
	}

	private function getPermutValue(int $permutIndex) : int {
		return $this->permutations[$permutIndex & 255];
	}

	private function grad(int $gradIndex, float $x, float $y, float $z, float $offset) : float {
		$d1 = $offset - $x * $x - $y * $y - $z * $z;
		if ($d1 < 0.0) {
			$ret = 0.0;
		} else {
			$d1 *= $d1;
			$ret = $d1 * $d1 * $this->dot(self::GRAD[$gradIndex], $x, $y, $z);
		}
		return $ret;
	}

	public function sample2D(float $x, float $y) : float {
		$d0 = ($x + $y) * self::F2;
		$i = floor($x + $d0);
		$j = floor($y + $d0);
		$d1 = ($i + $j) * self::G2;
		$d2 = $i - $d1;
		$d3 = $j - $d1;
		$d4 = $x - $d2;
		$d5 = $y - $d3;
		if ($d4 > $d5) {
			$k = 1;
			$l = 0;
		} else {
			$k = 0;
			$l = 1;
		}

		$d6 = $d4 - $k + self::G2;
		$d7 = $d5 - $l + self::G2;
		$d8 = $d4 - 1.0 + 2.0 * self::G2;
		$d9 = $d5 - 1.0 + 2.0 * self::G2;
		$i1 = $i & 255;
		$j1 = $j & 255;
		$k1 = $this->getPermutValue($i1 + $this->getPermutValue($j1)) % 12;
		$l1 = $this->getPermutValue($i1 + $k + $this->getPermutValue($j1 + $l)) % 12;
		$i2 = $this->getPermutValue($i1 + 1 + $this->getPermutValue($j1 + 1)) % 12;
		$d10 = $this->grad($k1, $d4, $d5, 0.0, 0.5);
		$d11 = $this->grad($l1, $d6, $d7, 0.0, 0.5);
		$d12 = $this->grad($i2, $d8, $d9, 0.0, 0.5);
		return 70.0 * ($d10 + $d11 + $d12);
	}

	public function sample3D(float $x, float $y, float $z) : float {
		$d1 = ($x + $y + $z) * 1 / 3;
		$i = floor($x + $d1);
		$j = floor($y + $d1);
		$k = floor($z + $d1);
		$d3 = (float) ($i + $j + $k) * 0.16666666666666666;
		$d4 = $i - $d3;
		$d5 = $j - $d3;
		$d6 = $k - $d3;
		$d7 = $x - $d4;
		$d8 = $y - $d5;
		$d9 = $z - $d6;
		if ($d7 >= $d8) {
			if ($d8 >= $d9) {
				$l = 1;
				$i1 = 0;
				$j1 = 0;
				$k1 = 1;
				$l1 = 1;
				$i2 = 0;
			} elseif ($d7 >= $d9) {
				$l = 1;
				$i1 = 0;
				$j1 = 0;
				$k1 = 1;
				$l1 = 0;
				$i2 = 1;
			} else {
				$l = 0;
				$i1 = 0;
				$j1 = 1;
				$k1 = 1;
				$l1 = 0;
				$i2 = 1;
			}
		} elseif ($d8 < $d9) {
			$l = 0;
			$i1 = 0;
			$j1 = 1;
			$k1 = 0;
			$l1 = 1;
			$i2 = 1;
		} elseif ($d7 < $d9) {
			$l = 0;
			$i1 = 1;
			$j1 = 0;
			$k1 = 0;
			$l1 = 1;
			$i2 = 1;
		} else {
			$l = 0;
			$i1 = 1;
			$j1 = 0;
			$k1 = 1;
			$l1 = 1;
			$i2 = 0;
		}

		$d10 = $d7 - (float) $l + 0.16666666666666666;
		$d11 = $d8 - (float) $i1 + 0.16666666666666666;
		$d12 = $d9 - (float) $j1 + 0.16666666666666666;
		$d13 = $d7 - (float) $k1 + 1 / 3;
		$d14 = $d8 - (float) $l1 + 1 / 3;
		$d15 = $d9 - (float) $i2 + 1 / 3;
		$d16 = $d7 - 1.0 + 0.5;
		$d17 = $d8 - 1.0 + 0.5;
		$d18 = $d9 - 1.0 + 0.5;
		$j2 = $i & 255;
		$k2 = $j & 255;
		$l2 = $k & 255;
		$i3 = $this->getPermutValue($j2 + $this->getPermutValue($k2 + $this->getPermutValue($l2))) % 12;
		$j3 = $this->getPermutValue($j2 + $l + $this->getPermutValue($k2 + $i1 + $this->getPermutValue($l2 + $j1))) % 12;
		$k3 = $this->getPermutValue($j2 + $k1 + $this->getPermutValue($k2 + $l1 + $this->getPermutValue($l2 + $i2))) % 12;
		$l3 = $this->getPermutValue($j2 + 1 + $this->getPermutValue($k2 + 1 + $this->getPermutValue($l2 + 1))) % 12;
		$d19 = $this->grad($i3, $d7, $d8, $d9, 0.6);
		$d20 = $this->grad($j3, $d10, $d11, $d12, 0.6);
		$d21 = $this->grad($k3, $d13, $d14, $d15, 0.6);
		$d22 = $this->grad($l3, $d16, $d17, $d18, 0.6);
		return 32.0 * ($d19 + $d20 + $d21 + $d22);
	}
}
