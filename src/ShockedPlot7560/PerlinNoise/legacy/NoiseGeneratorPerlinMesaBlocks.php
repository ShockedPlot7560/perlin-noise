<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise\legacy;

use ShockedPlot7560\PerlinNoise\utils\Random;
use function count;

class NoiseGeneratorPerlinMesaBlocks {
	/** @var NoiseGeneratorSimplex[] */
	private array $noiseLevels = [];
	private int $levels;

	public function __construct(Random $random, int $levelsIn) {
		$this->levels = $levelsIn;
		for ($i = 0; $i < $levelsIn; $i++) {
			$this->noiseLevels[$i] = new NoiseGeneratorSimplex($random);
		}
	}

	public function getValue(float $p_151601_1_, float $p_151601_3_) : float {
		$d0 = 0.0;
		$d1 = 1.0;
		for ($i = 0; $i < $this->levels; $i++) {
			$d0 += $this->noiseLevels[$i]->getValue($p_151601_1_ * $d1, $p_151601_3_ * $d1);
			$d1 /= 2.0;
		}
		return $d0;
	}

	/**
	 * @param float[] $p_151600_1_
	 * @return float[]
	 */
	public function getRegion(array &$p_151600_1_, float $p_151600_2_, float $p_151600_4_, int $p_151600_6_, int $p_151600_7_, float $p_151600_8_, float $p_151600_10_, float $p_151600_12_, float $p_151600_14_ = 0.5) : array {
		if (count($p_151600_1_) >= $p_151600_6_ * $p_151600_7_) {
			for ($i = 0; $i < count($p_151600_1_); ++$i) {
				$p_151600_1_[$i] = 0.0;
			}
		} else {
			$p_151600_1_ = [];
			for ($i = 0; $i < $p_151600_6_ * $p_151600_7_; $i++) {
				$p_151600_1_[$i] = 0.0;
			}
		}
		$d0 = 0.0;
		$d1 = 0.0;

		for ($j = 0; $j < $this->levels; $j++) {
			$this->noiseLevels[$j]->add($p_151600_1_, $p_151600_2_, $p_151600_4_, $p_151600_6_, $p_151600_7_, $p_151600_8_ * $d0 * $d1, $p_151600_10_ * $d0 * $d1, 0.55 / $d1);
			$d0 *= $p_151600_12_;
			$d1 *= $p_151600_14_;
		}
		return $p_151600_1_;
	}
}
