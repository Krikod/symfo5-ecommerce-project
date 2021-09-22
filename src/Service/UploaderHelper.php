<?php

namespace App\Service;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderHelper {

	const PRODUCT_IMAGE = 'images_directory';
	/**
	 * @var string
	 */
	private $uploadsPath;
	private $slugger;

	public function __construct(string $uploadsPath, SluggerInterface $slugger) {
		$this->uploadsPath = $uploadsPath;
		$this->slugger = $slugger;
	}


	public function uploadProductImage(UploadedFile $uploadedFile): string {
//		$fileDestination = $this->uploadsPath.'/product_image';
		$fileDestination = $this->uploadsPath.'/'.self::PRODUCT_IMAGE;

		$originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
		$safeFileName = $this->slugger->slug($originalFilename);
		$newFilename = $safeFileName.'-'.uniqid().'.'.$uploadedFile->guessExtension();

		try {
			$uploadedFile->move($fileDestination, $newFilename);
		} catch (FileException $e) {
			throw new FileException('Un problème est survenu lors du téléchargement de votre image !');
		}
		// OU ~ SymfonyCasts
//			/** @var UploadedFile $uploadedFile */
//
//			$uploadedFile = $form->get('uploads')->getData();
//
//
//
//			$destination = $this->getParameter('kernel.project_dir').'/public/uploads';
//			$newFilename = uniqid().'_'.$uploadedFile->getClientOriginalName();
//			$uploadedFile->move($destination, $newFilename);
//			dd( $newFilename);
//				$images = $form->get('uploads')->getData();
//			foreach ($images as $image) {
//				$fichier = md5(uniqid()).'.'.$image->guessExtension();
//				$image->move(
//					$this->getParameter('images_directory'),
//					$fichier
//				);
		return $newFilename;
	}
}