<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $video->image = $request->input('image');
        $video->content = $request->input('content');
        $video->publish_date = $request->input('publish_date');
        $video->taxonomy = $request->input('taxonomy');

        $category = $request->input('category');

        $uploader = new \RTV\Upload\Uploader();
        $parser = new \RTV\Upload\VideoConfigParser();

        $uploader->process();

        $parser->addVideo($category, $video);

        $parser->write();
        echo $uploader->isUploadComplete();
    }
}
