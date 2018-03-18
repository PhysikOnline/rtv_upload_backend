<?php

namespace RTV\Upload;

use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/video_config.php';

class VideoConfigParser
{
    public $config;
    private $configFile;
    private $categories;

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
        $this->read();
        print_r($this->config);
    }

    private function read()
    {
        $this->config = Yaml::parseFile($this->configFile);
        $this->categories = $this->config['videos'];
    }

    public function write()
    {
        $config = $this->config;
        $config['videos'] = $this->categories;
        $yaml = Yaml::dump($config);

        file_put_contents($this->configFile, $yaml);
    }

    public function addVideo(String $category, VideoModel $video)
    {
        foreach ($this->categories as $key => $category_object) {
            if ($category_object['title'] === $category) {
                $this->categories[$key]['items'] = array_merge($category_object['items'], [$this->convertVideoToArray($video)]);
                return;
            }
        }
        throw new \Exception("Could not find category " . $category . ".");
    }

    private function convertVideoToArray(VideoModel $video)
    {
        $result = array(
            'title' => $video->title,
            'abstract' => $video->abstract,
            'description' => $video->description,
            'image' => $video->image,
            'content' => $video->content,
            'publish_date' => $video->publish_date,
            'taxonomy' => $video->taxonomy
        );
        return $result;
    }
}
