<?php

namespace james090500;

use Intervention\Image\ImageManagerStatic as Image;
use Imagick;
use ImagickPixel;

class MinecraftSkinRenderer {

    //The provided source
    protected static $imageSrc;

    /**
     * Put all the components together into one big render
     *
     * @param  mixed $src
     * @return void
     */
    public static function render($src) {
        self::$imageSrc = realpath($src);

        $canvas = new Imagick();
        $canvas->newImage(300, 500, new ImagickPixel('transparent'), 'png');

        // //Create the parts
        $head = self::renderHead();

        // //Insert the parts
        $canvas->compositeImage($head, imagick::COMPOSITE_OVER, 50, 32);

        Header("Content-Type: image/png");
        return $canvas;
    }


    protected static function renderHead() {
        $head = new Imagick();
        $head->newImage(200, 200, new ImagickPixel('transparent'), 'png');

        $topHead = new Imagick(self::$imageSrc);
        $topHead->cropImage(8, 8, 8, 0);
        $topHead->resizeImage(64, 64, imagick::FILTER_BOX, 0);
        $topHead->setimagebackgroundcolor("transparent");
        $topHead->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topHead->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topHead->getImageHeight() / 2,

            0, $topHead->getImageHeight(),
            ($topHead->getImageWidth() / 4) * 3, $topHead->getImageHeight(),

            $topHead->getImageWidth(), 0,
            ($topHead->getImageWidth() / 4) * 3, 0,

            $topHead->getImageWidth(), $topHead->getImageHeight(),
            $topHead->getImageWidth() * 1.5, $topHead->getImageHeight() / 2
        ], true);
        $topHead->trimImage(0);

        $frontHead = new Imagick(self::$imageSrc);
        $frontHead->cropImage(8, 8, 8, 8);
        $frontHead->resizeImage(64, 64, imagick::FILTER_BOX, 0);
        $frontHead->setimagebackgroundcolor("transparent");
        $frontHead->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontHead->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topHead->getImageHeight() / 2,

            0, $frontHead->getImageHeight(),
            0, $frontHead->getImageHeight() + ($topHead->getImageHeight() / 2),

            $frontHead->getImageWidth(), 0,
            $topHead->getImageWidth() / 2, 0,

            $frontHead->getImageWidth(), $frontHead->getImageHeight(),
            $topHead->getImageWidth() / 2, $frontHead->getImageHeight()
        ], true);
        $frontHead->trimImage(0);

        $leftHead = new Imagick(self::$imageSrc);
        $leftHead->cropImage(8, 8, 0, 8);
        $leftHead->resizeImage(64, 64, imagick::FILTER_BOX, 0);
        $leftHead->setimagebackgroundcolor("transparent");
        $leftHead->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $leftHead->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topHead->getImageHeight() / 2,

            0, $leftHead->getImageHeight(),
            0, $leftHead->getImageHeight() + $topHead->getImageHeight() / 2,

            $leftHead->getImageWidth(), 0,
            $topHead->getImageWidth() / 2, $leftHead->getImageHeight(),

            $leftHead->getImageWidth(), $leftHead->getImageHeight(),
            $topHead->getImageWidth() / 2, $leftHead->getImageHeight() + $topHead->getImageHeight()
        ], true);
        $leftHead->trimImage(0);

        $head->compositeImage($topHead, imagick::COMPOSITE_OVER, 1, 1);
        $head->compositeImage($leftHead, imagick::COMPOSITE_OVER, 0, $topHead->getImageHeight() / 2);
        $head->compositeImage($frontHead, imagick::COMPOSITE_OVER, $topHead->getImageWidth() / 2, $topHead->getImageHeight() / 2);
        return $head;
    }
}