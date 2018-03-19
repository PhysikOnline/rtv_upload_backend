<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;
use RTV\Upload\Uploader;
use RTV\Upload\VideoConfigParser;
use RTV\Upload\VideoModel;

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
        $video = new VideoModel();
        $video->title = $request->input('title');
        $video->abstract = $request->input('abstract');
        $video->description = $request->input('description');
        $video->publish_date = date('m/d/Y H:i');
        $video->taxonomy = $request->input('taxonomy');
        $category = $request->input('category');

        if (!isset($video->title) || !isset($video->abstract) || !isset($video->description) || !isset($category)) {
            return new Response('Missing parameters: title, abstract, category and description must be set!', 400);
        }

        $uploader = new Uploader();

        $uploadRootDirectory = env('VIDEO_SRC_DIRECTORY', null);
        if (!isset($uploadRootDirectory)) {
            throw new \Exception('VIDEO_SRC_DIRECTORY not set!');
        }

        if (!file_exists($uploadRootDirectory)) {
            mkdir($uploadRootDirectory);
        }
        $uploadCategoryDirectory = $uploadRootDirectory . DIRECTORY_SEPARATOR . str_replace(' ', '', $category);
        if (!file_exists($uploadCategoryDirectory)) {
            mkdir($uploadCategoryDirectory);
        }
        $uploadDirectoryName = date('Y_m_d_H_i') . '__' . str_replace(' ', '', $video->title);
        $uploadDirectory = $uploadCategoryDirectory . DIRECTORY_SEPARATOR . $uploadDirectoryName;

        $uploader->setUploadFolder($uploadDirectory);


        $uploader->process();

        if (!$uploader->isUploadComplete()) {
            return new Response('Upload was not successful!', 500);
        }

        $videoPath = $uploadDirectory . DIRECTORY_SEPARATOR . $uploader->getOriginalFileName();
        $video->content = $videoPath;
        var_dump($video);

        $parser = new VideoConfigParser();
        // TODO: create image
        // TODO: convert video
//        $parser->addVideo($category, $video);

//        $parser->write();
    }
}
