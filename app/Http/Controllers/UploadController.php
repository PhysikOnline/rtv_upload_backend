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
        $email = $request->input('email');
        $thumbnailTime = $request->input('thumbnail');

        if (!isset($video->title)
            || !isset($video->abstract)
            || !isset($video->description)
            || !isset($category)
            || !isset($thumbnailTime)) {
            return new Response('Missing parameters: title, abstract, category, thumbnail and description must be set!', 400);
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
        $videoBasename = $uploader->getOriginalFileName(true);
        $origVideoFileName = $videoBasename . '.orig.mp4';
        $uploader->setFileName($origVideoFileName);

        $uploader->process();

        if (!$uploader->isUploadComplete()) {
            return new Response('Upload was not successful!', 500);
        }

        $videoPath = $uploadDirectory . DIRECTORY_SEPARATOR . $uploader->getFileName();
        $video->content = $videoPath;

        $parser = new VideoConfigParser();
        // create image
        $pathToImage = $uploadDirectory . DIRECTORY_SEPARATOR . $videoBasename . '-thumb640.jpg';
        // $this->createThumbnail($thumbnailTime, $videoPath, $uploadDirectory);
        $video->image = $pathToImage;
        // convert video
        // $this->convertVideo($videoPath, $uploadDirectory, $email, $uploadDirectory . DIRECTORY_SEPARATOR . $videoBasename . '.log');
        $parser->addVideo($category, $video);
        print_r($parser->categories);
//        $parser->write();
    }

    private function createThumbnail(String $thumbnailTime, String $pathToVideo, String $pathToDirectory)
    {
        $thumbnailTime = escapeshellarg($thumbnailTime);
        $pathToVideo = escapeshellarg($pathToVideo);
        $pathToDirectory = escapeshellarg($pathToDirectory);
        $cmd = __DIR__ . "../../../scripts/thumbnail.sh $pathToVideo $pathToDirectory $thumbnailTime";
        return shell_exec($cmd);
    }

    private function convertVideo(String $pathToVideo, String $directory, String $email, String $logFile)
    {
        $pathToVideo = escapeshellarg($pathToVideo);
        $directory = escapeshellarg($directory);
        $email = escapeshellarg($email);
        $logFile = escapeshellarg($logFile);
        $cmd = __DIR__ . "../../../scripts/convert.sh -f $pathToVideo -t $directory -m $email -l $logFile";
        return shell_exec($cmd);
    }
}
