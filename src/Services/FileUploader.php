<?php

namespace App\Services;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service_locator;

class FileUploader
{

    private TranslatorInterface $translator;
    const UPLOADS_DIR = 'public/uploads/';
    const AVAILABLE_IMAGES_MIME_TYPES = ['image/gif', 'image/jpeg', 'image/jpg', 'image/png'];
    const AVAILABLE_DOCUMENTS_MIME_TYPES = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/mspowerpoint', 'application/powerpoint', 'application/vnd.ms-powerpoint', 'application/x-mspowerpoint', 'application/pdf', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    const IMAGE_PATH = 'image/';
    const DOCUMENT_PATH = 'document/';

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * This method validates the images uploaded
     * @param UploadedFile $file
     * @return array
     */
    public function validateImage(UploadedFile $file): array
    {
        $errors = [];

        $mimeType = $file->getClientMimeType();
        if (!in_array($mimeType, self::AVAILABLE_IMAGES_MIME_TYPES)) {
            $errors[] = $this->translator->trans("Image type not available");
        }

        return $errors;
    }

    /**
     * This method validates the documents uploaded
     * @param UploadedFile $file
     * @return array
     */
    public function validateDocument(UploadedFile $file): array
    {
        $errors = [];

        $mimeType = $file->getClientMimeType();
        if (!in_array($mimeType, self::AVAILABLE_DOCUMENTS_MIME_TYPES)) {
            $errors[] = $this->translator->trans("Document type not available");
        }

        return $errors;
    }

    public function uploadFile(Request $request, $entityFullPath, $formFieldName)
    {
        $entityName = $this->getEntityName($entityFullPath);
        $file = $request->files->get($entityName)[$formFieldName];
        $path = $this->generateFilePath($file);
    }

    //====================================================================================PRIVATE METHODS===========================================================

    /**
     * This method gets the entity name lower string
     * @param string $entityFullPath
     * @return string
     */
    private function getEntityName(string $entityFullPath): string
    {
        $entityFullPathArr = explode("\\", $entityFullPath);

        return strtolower(end($entityFullPathArr));
    }

    /**
     * This method generates the file path
     * @param UploadedFile $file
     * @return string|null
     */
    private function generateFilePath(UploadedFile $file): ?string
    {
        $path = null;
        $fileUploadedDate = $this->getFileUploadedDate($file);
        if (in_array($file->getClientMimeType(), self::AVAILABLE_IMAGES_MIME_TYPES)) { //image
            $path = self::UPLOADS_DIR . self::IMAGE_PATH . $fileUploadedDate["year"] . "/" . $fileUploadedDate["month"] . "/" . $fileUploadedDate["day"] . "/";
        } elseif (in_array($file->getClientMimeType(), self::AVAILABLE_DOCUMENTS_MIME_TYPES)) { //document
            $path = self::UPLOADS_DIR . self::DOCUMENT_PATH . $fileUploadedDate["year"] . "/" . $fileUploadedDate["month"] . "/" . $fileUploadedDate["day"] . "/";
        }

        return $path;
    }

    /**
     * This method gets the file uploaded date
     * @param UploadedFile $file
     * @return array
     */
    private function getFileUploadedDate(UploadedFile $file): array {
        $date = date("d/m/y", filemtime($file));
        $dateArr = explode("/", $date);

        return [
            "year" => $dateArr[2],
            "month" => $dateArr[1],
            "day" => $dateArr[0],
        ];
    }

    /**
     * This method moves the file to the uploaded path
     * @param UploadedFile $file
     */
    private function moveFileToProperDir(UploadedFile $file): void
    {
        $file->move($this->generateFilePath($file), $file->getClientOriginalName());
    }
}