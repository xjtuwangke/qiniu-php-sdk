<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in( __DIR__ . '/src' )
;

return new Sami($iterator, array(
    'title'               => 'qiniu-php-sdk API (for master)',
    'theme'               => 'enhanced',
    'build_dir'           => 'docs',
    'cache_dir'           => 'sami/cache/qiniu-php-sdk',
    'include_parent_data' => false,
    'default_opened_level' => 2,
));
