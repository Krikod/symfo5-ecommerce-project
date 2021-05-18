<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension {
	public function getFilters():array {
		return [
			new TwigFilter('amount', [$this, 'amount'])
		];
	}

	public function amount($value, $symbol = ' €', $dec_sep = ',', $thous_sep = ' ') {
		$finalValue = $value / 100;
		$finalValue = number_format($finalValue, 2, $dec_sep, $thous_sep);
		return $finalValue . $symbol;

		// To override default values in Twig:
  		// {{ item.product.price|amount(' $', '.', ',') }}

		}
}