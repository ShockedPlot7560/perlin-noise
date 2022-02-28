<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise\utils;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PairTest extends TestCase {
	public function testConstructor() {
		new Pair(10, []);
		new Pair(10, [1.0]);
		$this->expectException(InvalidArgumentException::class);
		new Pair(10, [10]);
		$this->expectException(InvalidArgumentException::class);
		new Pair(10, [1.0, 10]);
	}
}
