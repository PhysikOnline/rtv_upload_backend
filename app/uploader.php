<?php

namespace RTV\Upload;

use Dilab\Network\SimpleRequest;
use Dilab\Network\SimpleResponse;
use Dilab\Resumable;

class Uploader
{
    private $resumable;

    public function __construct()
    {
        $request = new SimpleRequest();
        $response = new SimpleResponse();
        $this->resumable = new Resumable($request, $response);
        $this->setTmpFolder();
        $this->setUploadFolder();
    }

    public function process()
    {
        $this->resumable->process();
    }

    public function getOriginalFileName($withoutExtension = false)
    {
        return $this->resumable->getOriginalFilename($withoutExtension);
    }

    public function setFileName(String $filename)
    {
        return $this->resumable->setFilename($filename);
    }

    public function isUploadComplete()
    {
        return $this->resumable->isUploadComplete();
    }

    public function setUploadFolder($uploadFolder = './')
    {
        $this->resumable->uploadFolder = $uploadFolder;
    }

    public function setTmpFolder($tmpFolder = null)
    {
        if ($tmpFolder === null) {
            $tmpFolder = env('TEMP_FOLDER', '/tmp');
        }
        $this->resumable->tempFolder = $tmpFolder;
    }
}
