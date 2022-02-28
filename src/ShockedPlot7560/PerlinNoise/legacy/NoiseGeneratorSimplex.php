<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise\legacy;

use ShockedPlot7560\PerlinNoise\utils\Random;

use function floor;
use function sqrt;

class NoiseGeneratorSimplex {
	/** @var array<array<int>> */
	public const GRAD = [[1, 1, 0], [-1, 1, 0], [1, -1, 0], [-1, -1, 0], [1, 0, 1], [-1, 0, 1], [1, 0, -1], [-1, 0, -1], [0, 1, 1], [0, -1, 1], [0, 1, -1], [0, -1, -1], [1, 1, 0], [0, -1, 1], [-1, 1, 0], [0, -1, -1]];
	public float $xOffset;
	public float $yOffset;
	public float $zOffset;

	/** @var float */
	private static $SQRT_3;
	/** @var float */
	private static $F2;
	/** @var float */
	private static $G2;
	/** @var int[] */
	private array $permutations = [];

	public function __construct(Random $random = new Random()) {
		self::$SQRT_3 = sqrt(3.0);
		self::$F2 = 0.5 * (self::$SQRT_3 - 1.0);
		self::$G2 = (3.0 - self::$SQRT_3) / 6.0;
		$this->xOffset = $random->nextFloat() * 256.0;
		$this->yOffset = $random->nextFloat() * 256.0;
		$this->zOffset = $random->nextFloat() * 256.0;

		for ($i = 0; $i < 256; ++$i) {
			$this->permutations[$i] = $i;
		}

		for ($j = 0; $j < 256; ++$j) {
			$k = $random->nextBoundedInt(256 - $j);
			$l = $this->permutations[$j];
			$this->permutations[$j] = $this->permutations[$j + $k];
			$this->permutations[$j + $k] = $l;
		}
	}

	/**
	 * @param int[] $gradElement
	 */
	public static function dot(array $gradElement, float $xFactor, float $yFactor) : float {
		return (float) $gradElement[0] * $xFactor + (float) $gradElement[1] * $yFactor;
	}

	private function getPermutValue(int $permutIndex) : int {
		return $this->permutations[$permutIndex];
	}

	public function getValue(float $p_151605_1_, float $p_151605_3_) : float {
		$d3 = 0.5 * (self::$SQRT_3 - 1.0);
		$d4 = ($p_151605_1_ + $p_151605_3_) / $d3;
		$i = floor($p_151605_1_ + $d4);
		$j = floor($p_151605_3_ + $d4);
		$d5 = (3.0 - self::$SQRT_3) / 6.0;
		$d6 = ($i + $j) * $d5;
		$d7 = $i - $d6;
		$d8 = $j - $d6;
		$d9 = $p_151605_1_ - $d7;
		$d10 = $p_151605_3_ - $d8;
		if ($d9 > $d10) {
			$k = 1;
			$l = 0;
		} else {
			$k = 0;
			$l = 1;
		}

		$d11 = $d9 - $k + $d5;
		$d12 = $d10 - $l + $d5;
		$d13 = $d9 - 1.0 + 2.0 * $d5;
		$d14 = $d10 - 1.0 + 2.0 * $d5;
		$i1 = $i & 255;
		$j1 = $j & 255;
		$k1 = $this->getPermutValue($i1 + $this->getPermutValue($j1)) % 12;
		$l1 = $this->getPermutValue($i1 + $k + $this->getPermutValue($j1 + $l)) % 12;
		$i2 = $this->getPermutValue($i1 + 1 + $this->getPermutValue($j1 + 1)) % 12;
		$d15 = 0.5 - $d9 * $d9 - $d10 * $d10;
		if ($d15 < 0.0) {
			$d0 = 0.0;
		} else {
			$d15 *= $d15;
			$d0 = $d15 * $d15 * $this->dot(self::GRAD[$k1], $d9, $d10);
		}
		$d16 = 0.5 - $d11 * $d11 - $d12 * $d12;
		if ($d16 < 0.0) {
			$d1 = 0.0;
		} else {
			$d16 *= $d16;
			$d1 = $d16 * $d16 * $this->dot(self::GRAD[$l1], $d11,$d12);
		}
		$d17 = 0.5 - $d13 * $d13 - $d14 * $d14;
		if ($d17 < 0.0) {
			$d2 = 0.0;
		} else {
			$d17 *= $d17;
			$d2 = $d17 * $d17 * $this->dot(self::GRAD[$i2], $d13, $d14);
		}
		return 70.0 * ($d0 + $d1 + $d2);
	}

	/**
	 * @param float[] $p_151606_1_
	 */
	public function add(array &$p_151606_1_, float $p_151606_2_, float $p_151606_4_, int $p_151606_6_, int $p_151606_7_, float $p_151606_8_, float $p_151606_10_, float $p_151606_12_) : void {
		$i = 0;
		for ($j = 0; $j < $p_151606_7_; ++$j) {
			$d0 = ($p_151606_4_ + $j) * $p_151606_10_ + $this->yOffset;
			for ($k = 0; $k < $p_151606_6_; ++$k) {
				$d1 = ($p_151606_2_ + $k) * $p_151606_8_ + $this->xOffset;
				$d5 = ($d1 + $d0) * self::$F2;

				$l = floor($d1 + $d5);
				$i1 = floor($d0 + $d5);
				$d6 = ($l + $i1) * self::$G2;
				$d7 = $l - $d6;
				$d8 = $i1 - $d6;
				$d9 = $d1 - $d7;
				$d10 = $d0 - $d8;

				if ($d9 > $d10) {
					$j1 = 1;
					$k1 = 0;
				} else {
					$j1 = 0;
					$k1 = 1;
				}

				$d11 = $d9 - $j1 + self::$G2;
				$d12 = $d10 - $k1 + self::$G2;
				$d13 = $d9 - 1.0 + 2.0 * self::$G2;
				$d14 = $d10 - 1.0 + 2.0 * self::$G2;
				$l1 = $l & 255;
				$i2 = $i1 & 255;
				$j2 = $this->getPermutValue($l1 + $this->getPermutValue($i2)) % 12;
				$k2 = $this->getPermutValue($l1 + $j1 + $this->getPermutValue($i2 + $k1)) % 12;
				$l2 = $this->getPermutValue($l1 + 1 + $this->getPermutValue($i2 + 1)) % 12;
				$d15 = 0.5 - $d9 * $d9 - $d10 * $d10;
				if ($d15 < 0.0) {
					$d2 = 0.0;
				} else {
					$d15 *= $d15;
					$d2 = $d15 * $d15 * $this->dot(self::GRAD[$j2], $d9, $d10);
				}

				$d16 = 0.5 - $d11 * $d11 - $d12 * $d12;
				if ($d16 < 0.0) {
					$d3 = 0.0;
				} else {
					$d16 *= $d16;
					$d3 = $d16 * $d16 * $this->dot(self::GRAD[$k2], $d11, $d12);
				}

				$d17 = 0.5 - $d13 * $d13 - $d14 * $d14;
				if ($d17 < 0.0) {
					$d4 = 0.0;
				} else {
					$d17 = $d17 * $d17;
					$d4 = $d17 * $d17 * $this->dot(self::GRAD[$l2], $d13, $d14);
				}

				$i3 = $i++;
				$p_151606_1_[$i3] += 70.0 * ($d2 + $d3 + $d4) * $p_151606_12_;
			}
		}
	}
}
