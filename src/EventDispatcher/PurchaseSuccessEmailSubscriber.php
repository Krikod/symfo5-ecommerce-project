<?php

namespace App\EventDispatcher;


use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface {

	private $logger;
	private $mailer;

	public function __construct(LoggerInterface $logger, MailerInterface $mailer) {
		$this->logger = $logger;
		$this->mailer = $mailer;
	}

	public static function getSubscribedEvents() {
		return [
			'purchase.success' => 	'sendSuccessEmail'
		];
	}

	public function sendSuccessEmail(PurchaseSuccessEvent $event) {

		$purchase = $event->getPurchase();
		$user = $purchase->getUser();

		$email = new TemplatedEmail();
		$email->to(new Address($user->getEmail(), $user->getFullName()))
		      ->from('contact@mail.com') // todo changer email
				->subject("Votre commande n°{$purchase->getId()} a bien été confirmée !")
				->htmlTemplate('emails/purchase_success.html.twig')
				->context([
					'purchase' => $purchase,
					'user' => $user
				]);

		$this->mailer->send($email);

		$this->logger->info("Email envoyé pour la commande n° " .
		$event->getPurchase()->getId());
	}
}