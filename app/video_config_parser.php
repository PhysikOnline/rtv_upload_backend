<?php

namespace RTV\Upload;

use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/video_config.php';

class VideoConfigParser
{
    private $configFile;
    public $config;
    public function __construct()
    {
        $this->configFile = env('VIDEO_CONFIG_FILE', null);
        if ($this->configFile === null) {
            throw new \Exception('No Path to video config file provided!');
        }
        if (!is_readable($this->configFile)) {
            throw new \Exception('Path to video config file is not readable!');
        }
        if (!is_writable($this->configFile)) {
            throw new \Exception('Path to video config file is not writable!');
        }

    }

    public function read()
    {
        $this->config = Yaml::parseFile($this->configFile);
    }

    public function write()
    {
        $yaml = Yaml::dump($this->config);

        file_put_contents($this->configFile, $yaml);
    }

    public function addVideo(String $category, VideoModel $video)
    {
        $categories = $this->config['videos'];
        foreach ($categories as $key => $category_object) {
            if ($category_object['title'] === $category) {
                array_push($category_object['items'], $this->convertVideoToArray($video));
                $categories[$key] = $category_object['items'];
                $this->config['videos'] = $categories;
                return;
            }
        }
        throw new \Exception("Could not find category " . $category . ".");
    }

    private function convertVideoToArray(VideoModel $video)
    {
        $result = array();
        $result['title'] = $video->title;
        $result['abstract'] = $video->abstract;
        $result['description'] = $video->description;
        $result['image'] = $video->image;
        $result['content'] = $video->content;
        $result['publish_date'] = $video->publish_date;
        $result['taxonomy'] = $video->taxonomy;
        return $result;
    }
}
