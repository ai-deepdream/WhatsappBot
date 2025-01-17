<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit63193922318a76f8e0cc5d24d6fef6d8
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'setasign\\Fpdi\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'setasign\\Fpdi\\' => 
        array (
            0 => __DIR__ . '/..' . '/setasign/fpdi/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'CzProject\\PdfRotate\\PdfRotate' => __DIR__ . '/..' . '/czproject/pdf-rotate/src/PdfRotate.php',
        'FPDF' => __DIR__ . '/..' . '/setasign/fpdf/fpdf.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit63193922318a76f8e0cc5d24d6fef6d8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit63193922318a76f8e0cc5d24d6fef6d8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit63193922318a76f8e0cc5d24d6fef6d8::$classMap;

        }, null, ClassLoader::class);
    }
}
