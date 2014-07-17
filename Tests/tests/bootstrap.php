<?php

// if the bundle is within a symfony project, try to reuse the project's autoload

$files = array(
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../../../../../app/autoload.php',
);

$autoload = false;
foreach ($files as $file) {
    if (is_file($file)) {
        $autoload = include_once $file;
        break;
    }
}

if (!$autoload) {
    die('Unable to find autoload.php file, please use composer to load dependencies:

wget http://getcomposer.org/composer.phar
php composer.phar install

Visit http://getcomposer.org/ for more information.

');
}

if (class_exists('Doctrine\Common\Annotations\AnnotationRegistry')) {
    \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($autoload, 'loadClass'));
}

// force loading the ApiDoc annotation since the composer target-dir autoloader does not run through $loader::loadClass
class_exists('Nelmio\ApiDocBundle\Annotation\ApiDoc');