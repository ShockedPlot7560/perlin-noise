<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise;

use ShockedPlot7560\PerlinNoise\utils\Random;
use function array_key_last;
use function array_map;
use function array_search;
use function count;
use function is_int;
use function pow;
use function sort;

class OctaveSimplexNoiseSampler {
	/** @var SimplexNoiseSampler[] */
	private array $octaves = [];
	private float $persistence;
	private float $lacunarity;

	/**
	 * @param int[] $octaves
	 */
	public function __construct(Random $random, array $octaves) {
		array_map(function($element) {
			if (!is_int($element)) {
				throw new \InvalidArgumentException("Octaves list must contain only integer element");
			}
		}, $octaves);
		if (!sort($octaves)) { //get a int sort array
			throw new \RuntimeException("The sorting array has failed");
		}
		if (count($octaves) == 0) {
			throw new \InvalidArgumentException("Need more than 0 octaves");
		} else {
			$startOctave = -$octaves[0];
			$endOctave = $octaves[array_key_last($octaves)];
			$totalOctaves = $startOctave + $endOctave + 1;
			if ($totalOctaves < 1) {
				throw new \InvalidArgumentException("Number of octaves needs to be >= 1 in total");
			} else {
				$sampler = new SimplexNoiseSampler($random);
				if ($endOctave >= 0 && $endOctave < $totalOctaves && array_search(0, $octaves, true) !== false) {
					$this->octaves[$endOctave] = $sampler;
				}
				for ($i = $endOctave + 1; $i < $totalOctaves; $i++) {
					if ($i >= 0 && array_search($endOctave - $i, $octaves, true) !== false) {
						$this->octaves[$i] = new SimplexNoiseSampler($random);
					}
				}
				if ($endOctave > 0) {
					$rand = new Random($sampler->sample3D($sampler->xOffset, $sampler->yOffset, $sampler->zOffset) * 9.223372E18);
					for ($j = $endOctave - 1; $j >= 0; $j--) {
						if ($j < $totalOctaves && array_search($endOctave - $j, $octaves, true) !== false) {
							$this->octaves[$j] = new SimplexNoiseSampler($rand);
						}
					}
				}
				$this->lacunarity = pow(2.0, $endOctave);
				$this->persistence = 1.0 / (pow(2.0, $totalOctaves) - 1.0);
			}
		}
	}

	public function sampleNoiseOffset(float $x, float $y, bool $useNoiseOffset) : float {
		$sum = 0.0;
		$lacunarity = $this->lacunarity;
		$persistence = $this->persistence;
		foreach ($this->octaves as $sampler) {
			$sum += $sampler->sample2D($x * $lacunarity + ($useNoiseOffset ? $sampler->xOffset : 0.0), $y * $lacunarity + ($useNoiseOffset ? $sampler->yOffset : 0.0)) * $persistence;
		}

		$lacunarity /= 2.0;
		$persistence *= 2.0;
		return $sum;
	}

	public function sampleOffset(float $x, float $y, float $yScale, float $yOffset) : float {
		return $this->sampleNoiseOffset($x, $y, true) * 0.55;
	}
}
