<?php

return [
  'root' => '/upload/', // slashes in start and end of path required

  'images' => [
    'optimize' => false,  // optimize images using spatie/image-optimizer
    'max_height' => 900  // auto resize image to a height when optimizing and constrain aspect ratio (auto width)
  ]
];
