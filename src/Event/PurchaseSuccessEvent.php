<?php

namespace App\Event;

use App\Entity\Purchase;
use Symfony\Contracts\EventDispatcher\Event;

class PurchaseSuccessEvent extends Event {

	private $purchase;

	public function __construct($purchase) {
		$this->purchase = $purchase;
	}

	public function getPurchase(): Purchase {
		return $this->purchase;
	}
}