<?php

namespace james090500;

use Imagick;
use ImagickPixel;

class MinecraftSkinRenderer {

    //The provided source
    protected static $imageSrc;

    //Enlarge Ration
    protected static $enlargeRatio = 8;

    //Body Parts
    protected static $head = null;
    protected static $body = null;
    protected static $leftArm = null;
    protected static $rightArm = null;
    protected static $leftLeg = null;
    protected static $rightLeg = null;

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
        self::$head = self::renderHead();
        self::$leftArm = self::renderLeftArm();
        self::$rightArm = self::renderRightArm();
        self::$body = self::renderBody();

        // //Insert the parts
        $canvas->compositeImage(self::$leftArm, imagick::COMPOSITE_OVER, 0, self::$head->getImageHeight() - (self::$enlargeRatio * 3));
        $canvas->compositeImage(self::$rightArm, imagick::COMPOSITE_OVER, 72, self::$head->getImageHeight() - (self::$enlargeRatio * 9));
        $canvas->compositeImage(self::$body, imagick::COMPOSITE_OVER, self::$leftArm->getImageWidth() / 2, self::$head->getImageHeight() - (self::$enlargeRatio * 5));
        $canvas->compositeImage(self::$head, imagick::COMPOSITE_OVER, self::$enlargeRatio * 1.5, 0);

        Header("Content-Type: image/png");
        return $canvas;
    }

    /**
     * Generate the head render
     *
     * @return void
     */
    protected static function renderHead() {
        $head = new Imagick();
        $head->newImage(200, 200, new ImagickPixel('transparent'), 'png');

        $topHead = new Imagick(self::$imageSrc);
        $topHead->cropImage(8, 8, 8, 0);
        $topHead->resizeImage($topHead->getImageWidth() * self::$enlargeRatio, $topHead->getImageHeight() * self::$enlargeRatio, imagick::FILTER_BOX, 0);
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
        $topHead->trimImage(9999);
        $topHead->setImagePage(0, 0, 0, 0);

        $frontHead = new Imagick(self::$imageSrc);
        $frontHead->cropImage(8, 8, 8, 8);
        $frontHead->resizeImage($frontHead->getImageWidth() * self::$enlargeRatio, $frontHead->getImageHeight() * self::$enlargeRatio, imagick::FILTER_BOX, 0);
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
        $frontHead->trimImage(9999);
        $frontHead->setImagePage(0, 0, 0, 0);

        $leftHead = new Imagick(self::$imageSrc);
        $leftHead->cropImage(8, 8, 0, 8);
        $leftHead->resizeImage($leftHead->getImageWidth() * self::$enlargeRatio, $leftHead->getImageHeight() * self::$enlargeRatio, imagick::FILTER_BOX, 0);
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
        $leftHead->trimImage(9999);
        $leftHead->setImagePage(0, 0, 0, 0);

        $head->compositeImage($topHead, imagick::COMPOSITE_OVER, 0, 0);
        $head->compositeImage($leftHead, imagick::COMPOSITE_OVER, 0, $topHead->getImageHeight() / 2 - 1);
        $head->compositeImage($frontHead, imagick::COMPOSITE_OVER, $topHead->getImageWidth() / 2, $topHead->getImageHeight() / 2 - 1);

        $head->trimImage(0);
        return $head;
    }

    /**
     * Generate the body render
     *
     * @return void
     */
    protected static function renderBody() {
        $body = new Imagick();
        $body->newImage(200, 200, new ImagickPixel('transparent'), 'png');

        $topBody = new Imagick(self::$imageSrc);
        $topBody->cropImage(8, 4, 20, 16);
        $topBody->resizeImage(8 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_BOX, 0);
        $topBody->setimagebackgroundcolor("transparent");
        $topBody->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topBody->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, ($topBody->getImageHeight() / 2),

            0, $topBody->getImageHeight(),
            ($topBody->getImageHeight() / 4) * 3, $topBody->getImageHeight(),

            $topBody->getImageWidth(), 0,
            ($topBody->getImageHeight() / 4) * 3, 0,

            $topBody->getImageWidth(), $topBody->getImageHeight(),
            $topBody->getImageHeight() * 1.5, $topBody->getImageHeight() / 2,
        ], true);
        $topBody->trimImage(9999);
        $topBody->setImagePage(0, 0, 0, 0);

        $frontBody = new Imagick(self::$imageSrc);
        $frontBody->cropImage(8, 12, 20, 20);
        $frontBody->resizeImage($frontBody->getImageWidth() * self::$enlargeRatio, $frontBody->getImageHeight() * self::$enlargeRatio, imagick::FILTER_BOX, 0);
        $frontBody->setimagebackgroundcolor("transparent");
        $frontBody->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontBody->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            //top left
            0, 0,
            0, 0,

            //bottom left
            0, $frontBody->getImageHeight(),
            0, $frontBody->getImageHeight(),

            //top right
            $frontBody->getImageWidth(), 0,
            $topBody->getImageWidth(), -($topBody->getImageHeight()),

            //bottom right
            $frontBody->getImageWidth(), $frontBody->getImageHeight(),
            $topBody->getImageWidth(), $frontBody->getImageHeight() - ($topBody->getImageHeight()),
        ], true);
        $frontBody->trimImage(999);
        $frontBody->setImagePage(0, 0, 0, 0);

        $body->compositeImage($topBody, imagick::COMPOSITE_OVER, 0, 0);
        $body->compositeImage($frontBody, imagick::COMPOSITE_OVER, 22, 0);
        $body->trimImage(9999);
        $body->setImagePage(0, 0, 0, 0);

        return $body;
    }

    /**
     * Generate the left arm render
     *
     * @return void
     */
    protected static function renderLeftArm() {
        $leftArm = new Imagick();
        $leftArm->newImage(200, 200, new ImagickPixel('transparent'), 'png');

        $topLeftArm = new Imagick(self::$imageSrc);
        $topLeftArm->cropImage(4, 4, 44, 16);
        $topLeftArm->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_BOX, 0);
        $topLeftArm->setimagebackgroundcolor("transparent");
        $topLeftArm->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topLeftArm->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topLeftArm->getImageHeight() / 2,

            0, $topLeftArm->getImageHeight(),
            ($topLeftArm->getImageWidth() / 4) * 3, $topLeftArm->getImageHeight(),

            $topLeftArm->getImageWidth(), 0,
            ($topLeftArm->getImageWidth() / 4) * 3, 0,

            $topLeftArm->getImageWidth(), $topLeftArm->getImageHeight(),
            $topLeftArm->getImageWidth() * 1.5, $topLeftArm->getImageHeight() / 2
        ], true);
        $topLeftArm->trimImage(9999);
        $topLeftArm->setImagePage(0, 0, 0, 0);

        $leftLeftArm = new Imagick(self::$imageSrc);
        $leftLeftArm->cropImage(4, 12, 40, 20);
        $leftLeftArm->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_BOX, 0);
        $leftLeftArm->setimagebackgroundcolor("transparent");
        $leftLeftArm->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $leftLeftArm->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $leftLeftArm->getImageHeight(),
            0, $leftLeftArm->getImageHeight(),

            $leftLeftArm->getImageWidth(), 0,
            $topLeftArm->getImageWidth() / 2, $topLeftArm->getImageHeight() / 2,

            $leftLeftArm->getImageWidth(), $leftLeftArm->getImageHeight(),
            $topLeftArm->getImageWidth() / 2, $leftLeftArm->getImageHeight() + ($topLeftArm->getImageHeight() / 2)
        ], true);
        $leftLeftArm->trimImage(9999);
        $leftLeftArm->setImagePage(0, 0, 0, 0);

        $frontLeftArm = new Imagick(self::$imageSrc);
        $frontLeftArm->cropImage(4, 12, 44, 20);
        $frontLeftArm->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_BOX, 0);
        $frontLeftArm->setimagebackgroundcolor("transparent");
        $frontLeftArm->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontLeftArm->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $frontLeftArm->getImageHeight(),
            0, $frontLeftArm->getImageHeight(),

            $frontLeftArm->getImageWidth(), 0,
            $topLeftArm->getImageWidth() / 2, -($topLeftArm->getImageHeight() / 2),

            $frontLeftArm->getImageWidth(), $frontLeftArm->getImageHeight(),
            $topLeftArm->getImageWidth() / 2, $frontLeftArm->getImageHeight() - ($topLeftArm->getImageHeight() / 2),
        ], true);
        $frontLeftArm->trimImage(9999);
        $frontLeftArm->setImagePage(0, 0, 0, 0);

        $leftArm->compositeImage($topLeftArm, imagick::COMPOSITE_OVER, 0, 0);
        $leftArm->compositeImage($leftLeftArm, imagick::COMPOSITE_OVER, 0, $topLeftArm->getImageHeight() / 2);
        $leftArm->compositeImage($frontLeftArm, imagick::COMPOSITE_OVER, $topLeftArm->getImageWidth() / 2 - 1, $topLeftArm->getImageHeight() / 2);

        $leftArm->trimImage(9999);
        $leftArm->setImagePage(0, 0, 0, 0);
        return $leftArm;
    }

    /**
     * Generate the right arm render
     *
     * @return void
     */
    protected static function renderRightArm() {
        $rightArm = new Imagick();
        $rightArm->newImage(200, 200, new ImagickPixel('transparent'), 'png');

        $topRightArm = new Imagick(self::$imageSrc);
        $topRightArm->cropImage(4, 4, 36, 48);
        $topRightArm->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_BOX, 0);
        $topRightArm->setimagebackgroundcolor("transparent");
        $topRightArm->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topRightArm->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topRightArm->getImageHeight() / 2,

            0, $topRightArm->getImageHeight(),
            ($topRightArm->getImageWidth() / 4) * 3, $topRightArm->getImageHeight(),

            $topRightArm->getImageWidth(), 0,
            ($topRightArm->getImageWidth() / 4) * 3, 0,

            $topRightArm->getImageWidth(), $topRightArm->getImageHeight(),
            $topRightArm->getImageWidth() * 1.5, $topRightArm->getImageHeight() / 2
        ], true);
        $topRightArm->trimImage(9999);
        $topRightArm->setImagePage(0, 0, 0, 0);

        $frontRightArm = new Imagick(self::$imageSrc);
        $frontRightArm->cropImage(4, 12, 36, 52);
        $frontRightArm->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_BOX, 0);
        $frontRightArm->setimagebackgroundcolor("transparent");
        $frontRightArm->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontRightArm->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $frontRightArm->getImageHeight(),
            0, $frontRightArm->getImageHeight(),

            $frontRightArm->getImageWidth(), 0,
            $topRightArm->getImageWidth() / 2, -($topRightArm->getImageHeight() / 2),

            $frontRightArm->getImageWidth(), $frontRightArm->getImageHeight(),
            $topRightArm->getImageWidth() / 2, $frontRightArm->getImageHeight() - ($topRightArm->getImageHeight() / 2),
        ], true);
        $frontRightArm->trimImage(9999);
        $frontRightArm->setImagePage(0, 0, 0, 0);

        $rightArm->compositeImage($topRightArm, imagick::COMPOSITE_OVER, 0, 0);
        $rightArm->compositeImage($frontRightArm, imagick::COMPOSITE_OVER, $topRightArm->getImageWidth() / 2 - 1, $topRightArm->getImageHeight() / 2);

        $rightArm->trimImage(9999);
        $rightArm->setImagePage(0, 0, 0, 0);
        return $rightArm;
    }
}