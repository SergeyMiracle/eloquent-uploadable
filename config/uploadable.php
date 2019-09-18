<?php

return [
    'disk' => 'local',

    'root' => '/', // slashes in start and end of path required

    'file_name' => [
        'slugify' => false, // slugify original file name
        'uuid' => true // add uuid to file name
    ]
];
