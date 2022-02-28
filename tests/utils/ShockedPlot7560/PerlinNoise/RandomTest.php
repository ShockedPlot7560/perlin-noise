<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise\utils;

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class RandomTest extends TestCase {
	public function testNextSignedIntReturnsSignedInts() {
		$random = new Random(0);
		$negatives = false;

		for ($i = 0; $i < 100; ++$i) {
			if ($random->nextSignedInt() < 0) {
				$negatives = true;
				break;
			}
		}
		self::assertTrue($negatives);
	}

	public function testConsistency() {
		$random = new Random(15);
		assertEquals(6, $random->nextRange(0, 10));
		assertEquals(6, $random->nextRange(0, 10));
		assertEquals(3, $random->nextRange(0, 10));
		assertEquals(7, $random->nextRange(0, 10));
	}

	public function testZeroRange() {
		assertEquals(3, (new Random())->nextRange(3, 3));
	}
}
