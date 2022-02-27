<?php

declare(strict_types=1);

namespace ShockedPlot7560\PerlinNoise\utils;

use function array_map;
use function is_float;

final class Pair {
	private int $integer;
	/** @var float[] */
	private array $doubleList;

	public function __construct(int $integer, array $doubleList) {
		$this->integer = $integer;
		array_map(function($element) {
			if (!is_float($element)) {
				throw new \InvalidArgumentException("DoubleList must contain only float value");
			}
		}, $doubleList);
		$this->doubleList = $doubleList;
	}

	public function getInteger() : int {
		return $this->integer;
	}

	public function getDoubleList() : array {
		return $this->doubleList;
	}
}
