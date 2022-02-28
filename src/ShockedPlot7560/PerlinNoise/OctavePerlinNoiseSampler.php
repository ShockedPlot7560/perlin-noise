<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise;

use ShockedPlot7560\PerlinNoise\utils\Pair;
use ShockedPlot7560\PerlinNoise\utils\Random;
use function array_key_last;
use function count;
use function floor;
use function pow;
use function sort;

class OctavePerlinNoiseSampler {
	/** @var PerlinNoiseSampler[] */
	private array $octaves = [];
	/** @var float[] */
	private array $amplitudes = [];
	private float $persistence;
	private float $lacunarity;

	/**
	 * @param Pair|int[] $octaves
	 */
	public function __construct(Random $random, Pair|array $octaves) {
		if (!$octaves instanceof Pair) {
			$octaves = $this->generateAmplitutes($octaves);
		}
		$octaveCount = $octaves->getInteger();
		$this->amplitudes = $octaves->getDoubleList();
		$sampler = new PerlinNoiseSampler($random);
		$amplitudeLength = count($this->amplitudes);
		$totalOctaves = -$octaveCount;
		if ($totalOctaves >= 0 && $totalOctaves < $amplitudeLength) {
			if ($this->amplitudes[$totalOctaves] !== 0.0) {
				$this->octaves[$totalOctaves] = $sampler;
			}
		}
		for ($i = $totalOctaves - 1; $i >= 0; --$i) {
			if ($i < $amplitudeLength) {
				if ($this->amplitudes[$i] !== 0.0) {
					$this->octaves[$i] = new PerlinNoiseSampler($random);
				}
			}
		}

		if ($totalOctaves < $amplitudeLength - 1) {
			$rand = new Random($sampler->sample(0.0, 0.0, 0.0, 0.0, 0.0) * 9.223372E18);
			for ($i = $totalOctaves + 1; $i < $amplitudeLength; ++$i) {
				if ($i >= 0) {
					if ($this->amplitudes[$i] !== 0.0) {
						$this->octaves[$i] = new PerlinNoiseSampler($rand);
					}
				}
			}
		}
		$this->lacunarity = pow(2.0, -$totalOctaves);
		$this->persistence = pow(2.0, $amplitudeLength - 1) / (pow(2.0, $amplitudeLength) - 1.0);
	}

	/**
	 * @param float[] $amplitudes
	 */
	public static function create(Random $random, int $octaves, array $amplitudes) : OctavePerlinNoiseSampler {
		return new OctavePerlinNoiseSampler($random, new Pair($octaves, $amplitudes));
	}

	public static function maintainPrecision(float $value) : float {
		return $value - floor($value / 3.3554432E7 + 0.5) * 3.3554432E7;
	}

	public function sample(float $x, float $y, float $z, float $yScale = 0.0, float $yOffset = 0.0, bool $useOffset = false) : float {
		$sum = 0.0;
		$lacunarity = $this->lacunarity;
		$persistence = $this->persistence;
		for ($i = 0; $i < count($this->octaves); $i++) {
			$sampler = $this->octaves[$i];
			$sum += $this->amplitudes[$i] * $sampler->sample(self::maintainPrecision($x * $lacunarity), $useOffset ? -$sampler->yOffset : self::maintainPrecision($y * $lacunarity), self::maintainPrecision($z * $lacunarity), $yScale * $lacunarity, $yOffset * $lacunarity) * $persistence;
			$lacunarity *= 2.0;
			$persistence /= 2.0;
		}
		return $sum;
	}

	public function getOctave(int $index) : PerlinNoiseSampler {
		return $this->octaves[count($this->octaves) - 1 - $index];
	}

	/**
	 * @param int[] $octaves
	 */
	private function generateAmplitutes(array $octaves) : Pair {
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
				$amplitudes = [];
				foreach ($octaves as $octave) {
					$amplitudes[$octave + $startOctave] = 1.0;
				}
				return new Pair(-$startOctave, $amplitudes);
			}
		}
	}
}
