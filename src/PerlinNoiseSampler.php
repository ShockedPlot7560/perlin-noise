<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise;

use ShockedPlot7560\PerlinNoise\utils\Math;
use ShockedPlot7560\PerlinNoise\utils\Random;
use function floor;
use function min;

class PerlinNoiseSampler {
	/** @var int[] */
	private array $permutations;
	public float $xOffset;
	public float $yOffset;
	public float $zOffset;

	public function __construct(Random $random) {
		$this->xOffset = $random->nextFloat() * 256.0;
		$this->yOffset = $random->nextFloat() * 256.0;
		$this->zOffset = $random->nextFloat() * 256.0;

		for ($i = 0; $i < 256; $i++) {
			$this->permutations[$i] = $i;
		}
		for ($j = 0; $j < 256; $j++) {
			$k = $random->nextBoundedInt(256 - $j);
			$byte0 = $this->permutations[$j];
			$this->permutations[$j] = $this->permutations[$j + $k];
			$this->permutations[$j + $k] = $byte0;
		}
	}

	public function sample(float $x, float $y, float $z, float $yScale, float $yOffset) : float {
		$d0 = $x + $this->xOffset;
		$d1 = $y + $this->yOffset;
		$d2 = $z + $this->zOffset;
		$i = (int) floor($d0);
		$j = (int) floor($d1);
		$k = (int) floor($d2);
		$d3 = $d0 - $i;
		$d4 = $d1 - $j;
		$d5 = $d2 - $k;
		$d6 = Math::smoothStep($d3);
		$d7 = Math::smoothStep($d4);
		$d8 = Math::smoothStep($d5);
		if ($yScale != 0.0) {
			$d10 = min($yOffset, $d4);
			$d9 = floor($d10 / $yScale) * $yScale;
		} else {
			$d9 = 0.0;
		}
		return $this->sampleValue($i, $j, $k, $d3, $d4 - $d9, $d5, $d6, $d7, $d8);
	}

	private function permute(int $x) : int {
		return $this->permutations[$x & 255] & 255;
	}

	private function grad(int $gradIndex, float $xFactor, float $yFactor, float $zFactor) : float {
		$i = $gradIndex & 15;
		return SimplexNoiseSampler::dot(SimplexNoiseSampler::GRAD[$i], $xFactor, $yFactor, $zFactor);
	}

	private function sampleValue(int $sectionX, int $sectionY, int $sectionZ, float $localX, float $localY, float $localZ, float $smoothedX, float $smoothedY, float $smoothedZ) : float {
		$i = $this->permute($sectionX) + $sectionY;
		$j = $this->permute($i) + $sectionZ;
		$k = $this->permute($i + 1) + $sectionZ;
		$l = $this->permute($sectionX + 1) + $sectionY;
		$i1 = $this->permute($l) + $sectionZ;
		$j1 = $this->permute($l + 1) + $sectionZ;
		$d0 = $this->grad($this->permute($j), $localX, $localY, $localZ);
		$d1 = $this->grad($this->permute($i1), $localX - 1.0, $localY, $localZ);
		$d2 = $this->grad($this->permute($k), $localX, $localY - 1.0, $localZ);
		$d3 = $this->grad($this->permute($j1), $localX - 1.0, $localY - 1.0, $localZ);
		$d4 = $this->grad($this->permute($j + 1), $localX, $localY, $localZ - 1.0);
		$d5 = $this->grad($this->permute($i1 + 1), $localX - 1.0, $localY, $localZ - 1.0);
		$d6 = $this->grad($this->permute($k + 1), $localX, $localY - 1.0, $localZ - 1.0);
		$d7 = $this->grad($this->permute($j1 + 1), $localX - 1.0, $localY - 1.0, $localZ - 1.0);
		return Math::lerp3($smoothedX, $smoothedY, $smoothedZ, $d0, $d1, $d2, $d3, $d4, $d5, $d6, $d7);
	}
}
