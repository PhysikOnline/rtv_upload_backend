# RTV Video Upload Backend

Enables upload of single videos using resumable.js. 
Videos will be converted and video info will be saved in provided YAML file.

## Routes

The API provides one route: `/upload` (POST)

It expects the following POST parameters (in addition to parameters provided by the resumable.js library).

```
title = 'video title',
abstract = 'video abstract',
description = 'video description',
category = 'video category',
thumbnail = 'time stamp for thumbnail creation (hh:mm:ss)',
[email] = 'email address to send mail to on completion (optional)'
```

## Installation

Install dependencies via `composer install`, then point resumable.js to `public/index.php/upload`.

For a resumable.js example see `test.html`.

The video will be converted using `ffmpeg` which has to be installed on your system.

## Configuration

Copy `.env.example` to `.env` and edit:
* `VIDEO_CONFIG_FILE`: Path to YAML config file.
* `VIDEO_SRC_DIRECTORY`: Path to directory where videos will be saved.
