<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise\utils;

use function time;

class Random {
	public const X = 123456789;
	public const Y = 362436069;
	public const Z = 521288629;
	public const W = 88675123;

	private int $x;
	private int $y;
	private int $z;
	private int $w;

	protected float $seed;

	public function __construct(float $seed = -1) {
		if ($seed === -1.0) {
			$seed = time();
		}

		$this->setSeed($seed);
	}

	public function setSeed(float $seed) : void {
		$this->seed = $seed;
		$this->x = self::X ^ $seed;
		$this->y = self::Y ^ ($seed << 17) | (($seed >> 15) & 0x7fffffff) & 0xffffffff;
		$this->z = self::Z ^ ($seed << 31) | (($seed >> 1) & 0x7fffffff) & 0xffffffff;
		$this->w = self::W ^ ($seed << 18) | (($seed >> 14) & 0x7fffffff) & 0xffffffff;
	}

	public function getSeed() : float {
		return $this->seed;
	}

	public function nextInt() : int {
		return $this->nextSignedInt() & 0x7fffffff;
	}

	public function nextSignedInt() : int {
		$t = ($this->x ^ ($this->x << 11)) & 0xffffffff;

		$this->x = $this->y;
		$this->y = $this->z;
		$this->z = $this->w;
		$this->w = ($this->w ^ (($this->w >> 19) & 0x7fffffff) ^ ($t ^ (($t >> 8) & 0x7fffffff))) & 0xffffffff;

		return $this->w << 32 >> 32;
	}

	public function nextFloat() : float {
		return $this->nextInt() / 0x7fffffff;
	}

	public function nextSignedFloat() : float {
		return $this->nextSignedInt() / 0x7fffffff;
	}

	public function nextBoolean() : bool {
		return ($this->nextSignedInt() & 0x01) === 0;
	}

	public function nextRange(int $start = 0, int $end = 0x7fffffff) : int {
		return $start + ($this->nextInt() % ($end + 1 - $start));
	}

	public function nextBoundedInt(int $bound) : int {
		return $this->nextInt() % $bound;
	}
}
