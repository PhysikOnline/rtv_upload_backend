<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

require_once __DIR__ . '/../../uploader.php';
require_once __DIR__ . '/../../video_config_parser.php';
require_once __DIR__ . '/../../video_config.php';

class UploadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function upload(Request $request)
    {
        $uploader = new \RTV\Upload\Uploader();
        //$uploader->process();
        $parser = new \RTV\Upload\VideoConfigParser();

        $parser->read();

        $video = new \RTV\Upload\VideoModel();
        $video->title = 'Test';
        $parser->addVideo("Kategorie 1", $video);
        $parser->write();

        print_r($parser->config);
        echo $uploader->isUploadComplete();
    }
}
