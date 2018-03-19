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
category = 'video category'
```

## Installation

Install dependencies via `composer install`, then point resumable.js to `public/index.php/upload`.

## Configuration

Copy `.env.example` to `.env` and edit:
* `VIDEO_CONFIG_FILE`: Path to YAML config file.
* `VIDEO_SRC_DIRECTORY`: Path to directory where videos will be saved.
