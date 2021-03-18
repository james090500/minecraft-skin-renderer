<?php

namespace james090500;

use Imagick;
use ImagickPixel;

class MinecraftSkinRenderer {

    //The provided source
    protected static $imageSrc;

    //Skin Type
    protected static $legacySkin;

    //Enlarge Ration
    protected static $enlargeRatio = 8;

    //Body Parts
    protected static $head = null;
    protected static $body = null;
    protected static $leftArm = null;
    protected static $rightArm = null;
    protected static $leftLeg = null;
    protected static $rightLeg = null;

    //Armour Parts
    protected static $helmet = null;
    protected static $jacket = null;
    protected static $leftSleeve = null;
    protected static $rightSleeve = null;
    protected static $leftPants = null;
    protected static $rightPants = null;

    /**
     * Put all the components together into one big render
     *
     * @param  mixed $src
     * @return void
     */
    public static function render($src) {
        self::$imageSrc = realpath($src);

        self::checkSkinSize();

        $canvas = new Imagick();
        $canvas->newImage(120 * self::$enlargeRatio, 270 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        // Create the parts
        self::$head = self::renderHead();
        self::$leftArm = self::renderLeftArm();
        self::$rightArm = self::renderRightArm();
        self::$body = self::renderBody();
        self::$leftLeg = self::renderLeftLeg();
        self::$rightLeg = self::renderRightLeg();

        // Create the armor
        self::$helmet = self::renderHelmet();
        if(!self::$legacySkin) {
            self::$leftSleeve = self::renderLeftSleeve();
            self::$rightSleeve = self::renderRightSleeve();
            self::$jacket = self::renderJacket();
            self::$leftPants = self::renderLeftPants();
            self::$rightPants = self::renderRightPants();
        }

        // Insert the body and armor parts
        $canvas->compositeImage(self::$leftLeg, imagick::COMPOSITE_OVER, self::$leftArm->getImageWidth() / 2, self::$head->getImageHeight() + (self::$enlargeRatio * 7));
        $canvas->compositeImage(self::$rightLeg, imagick::COMPOSITE_OVER, self::$leftLeg->getImageWidth(), self::$head->getImageHeight() + (self::$enlargeRatio * 5));
        if(!self::$legacySkin) {
            $canvas->compositeImage(self::$leftPants, imagick::COMPOSITE_OVER, self::$leftSleeve->getImageWidth() / 2, self::$helmet->getImageHeight() + (self::$enlargeRatio * 7));
            $canvas->compositeImage(self::$rightPants, imagick::COMPOSITE_OVER, self::$leftPants->getImageWidth(), self::$helmet->getImageHeight() + (self::$enlargeRatio * 5));
        }
        $canvas->compositeImage(self::$leftArm, imagick::COMPOSITE_OVER, 0, self::$head->getImageHeight() - (self::$enlargeRatio * 3));
        $canvas->compositeImage(self::$rightArm, imagick::COMPOSITE_OVER, self::$body->getImageWidth(), self::$head->getImageHeight() - (self::$enlargeRatio * 9));
        $canvas->compositeImage(self::$body, imagick::COMPOSITE_OVER, self::$leftArm->getImageWidth() / 2, self::$head->getImageHeight() - (self::$enlargeRatio * 5));
        if(!self::$legacySkin) {
            $canvas->compositeImage(self::$jacket, imagick::COMPOSITE_OVER, self::$leftSleeve->getImageWidth() / 2, self::$helmet->getImageHeight() - (self::$enlargeRatio * 5));
        }
        if(!self::$legacySkin) {
            $canvas->compositeImage(self::$leftSleeve, imagick::COMPOSITE_OVER, 0, self::$helmet->getImageHeight() - (self::$enlargeRatio * 3));
            $canvas->compositeImage(self::$rightSleeve, imagick::COMPOSITE_OVER, self::$jacket->getImageWidth(), self::$helmet->getImageHeight() - (self::$enlargeRatio * 9));
        }
        $canvas->compositeImage(self::$head, imagick::COMPOSITE_OVER, self::$enlargeRatio * 1.5, 0);
        $canvas->compositeImage(self::$helmet, imagick::COMPOSITE_OVER, self::$enlargeRatio * 1.5, 0);

        // Crop the image
        $canvas->trimImage(9999);
        $canvas->setImagePage(0, 0, 0, 0);
        $canvas->adaptiveResizeImage(120, 270);

        Header("Content-Type: image/png");
        return $canvas;
    }

    protected static function checkSkinSize() {
        $skin = new Imagick(self::$imageSrc);
        self::$legacySkin = ($skin->getImageHeight() == 32);
    }

    /**
     * Generate the head render
     *
     * @return void
     */
    protected static function renderHead() {
        $head = new Imagick();
        $head->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topHead = new Imagick(self::$imageSrc);
        $topHead->cropImage(8, 8, 8, 0);
        $topHead->resizeImage($topHead->getImageWidth() * self::$enlargeRatio, $topHead->getImageHeight() * self::$enlargeRatio, imagick::FILTER_POINT, 0);
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
        $frontHead->resizeImage($frontHead->getImageWidth() * self::$enlargeRatio, $frontHead->getImageHeight() * self::$enlargeRatio, imagick::FILTER_POINT, 0);
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

        $leftHelmet = new Imagick(self::$imageSrc);
        $leftHelmet->cropImage(8, 8, 0, 8);
        $leftHelmet->resizeImage($leftHelmet->getImageWidth() * self::$enlargeRatio, $leftHelmet->getImageHeight() * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $leftHelmet->setimagebackgroundcolor("transparent");
        $leftHelmet->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $leftHelmet->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topHead->getImageHeight() / 2,

            0, $leftHelmet->getImageHeight(),
            0, $leftHelmet->getImageHeight() + $topHead->getImageHeight() / 2,

            $leftHelmet->getImageWidth(), 0,
            $topHead->getImageWidth() / 2, $leftHelmet->getImageHeight(),

            $leftHelmet->getImageWidth(), $leftHelmet->getImageHeight(),
            $topHead->getImageWidth() / 2, $leftHelmet->getImageHeight() + $topHead->getImageHeight()
        ], true);
        $leftHelmet->trimImage(9999);
        $leftHelmet->setImagePage(0, 0, 0, 0);

        $head->compositeImage($topHead, imagick::COMPOSITE_OVER, 0, 0);
        $head->compositeImage($leftHelmet, imagick::COMPOSITE_OVER, 0, $topHead->getImageHeight() / 2 - 1);
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
        $body->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topBody = new Imagick(self::$imageSrc);
        $topBody->cropImage(8, 4, 20, 16);
        $topBody->resizeImage(8 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
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
        $frontBody->resizeImage($frontBody->getImageWidth() * self::$enlargeRatio, $frontBody->getImageHeight() * self::$enlargeRatio, imagick::FILTER_POINT, 0);
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
        $leftArm->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topLeftArm = new Imagick(self::$imageSrc);
        if(self::$legacySkin) {
            $topLeftArm->cropImage(4, 4, 44, 16);
        } else {
            $topLeftArm->cropImage(4, 4, 36, 48);
            $topLeftArm->flopImage();
        }
        $topLeftArm->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
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
        if(self::$legacySkin) {
            $leftLeftArm->cropImage(4, 12, 48, 20);
        } else {
            $leftLeftArm->cropImage(4, 12, 40, 52);
            $leftLeftArm->flopImage();
        }
        $leftLeftArm->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
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
        if(self::$legacySkin) {
            $frontLeftArm->cropImage(4, 12, 44, 20);
        } else {
            $frontLeftArm->cropImage(4, 12, 36, 52);
            $frontLeftArm->flopImage();
        }
        $frontLeftArm->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
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
        $rightArm->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topRightArm = new Imagick(self::$imageSrc);
        $topRightArm->cropImage(4, 4, 44, 16);
        $topRightArm->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
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
        $frontRightArm->cropImage(4, 12, 44, 20);
        if(self::$legacySkin) {
            $frontRightArm->flopImage();
        }
        $frontRightArm->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
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

    /**
     * Generate the left leg render
     *
     * @return void
     */
    protected static function renderLeftLeg() {
        $leftLeg = new Imagick();
        $leftLeg->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topLeftLeg = new Imagick(self::$imageSrc);
        if(self::$legacySkin) {
            $topLeftLeg->cropImage(4, 4, 4, 16);
        } else {
            $topLeftLeg->cropImage(4, 4, 20, 48);
        }
        $topLeftLeg->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $topLeftLeg->setimagebackgroundcolor("transparent");
        $topLeftLeg->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topLeftLeg->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topLeftLeg->getImageHeight() / 2,

            0, $topLeftLeg->getImageHeight(),
            ($topLeftLeg->getImageWidth() / 4) * 3, $topLeftLeg->getImageHeight(),

            $topLeftLeg->getImageWidth(), 0,
            ($topLeftLeg->getImageWidth() / 4) * 3, 0,

            $topLeftLeg->getImageWidth(), $topLeftLeg->getImageHeight(),
            $topLeftLeg->getImageWidth() * 1.5, $topLeftLeg->getImageHeight() / 2
        ], true);
        $topLeftLeg->trimImage(9999);
        $topLeftLeg->setImagePage(0, 0, 0, 0);

        $leftleftLeg = new Imagick(self::$imageSrc);
        if(self::$legacySkin) {
            $leftleftLeg->cropImage(4, 12, 8, 20);
        } else {
            $leftleftLeg->cropImage(4, 12, 24, 52);
            $leftleftLeg->flopImage();
        }
        $leftleftLeg->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $leftleftLeg->setimagebackgroundcolor("transparent");
        $leftleftLeg->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $leftleftLeg->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $leftleftLeg->getImageHeight(),
            0, $leftleftLeg->getImageHeight(),

            $leftleftLeg->getImageWidth(), 0,
            $topLeftLeg->getImageWidth() / 2, $topLeftLeg->getImageHeight() / 2,

            $leftleftLeg->getImageWidth(), $leftleftLeg->getImageHeight(),
            $topLeftLeg->getImageWidth() / 2, $leftleftLeg->getImageHeight() + ($topLeftLeg->getImageHeight() / 2)
        ], true);
        $leftleftLeg->trimImage(9999);
        $leftleftLeg->setImagePage(0, 0, 0, 0);

        $frontleftLeg = new Imagick(self::$imageSrc);
        if(self::$legacySkin) {
            $frontleftLeg->cropImage(4, 12, 4, 20);
        } else {
            $frontleftLeg->cropImage(4, 12, 20, 52);
            $frontleftLeg->flopImage();
        }
        $frontleftLeg->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $frontleftLeg->setimagebackgroundcolor("transparent");
        $frontleftLeg->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontleftLeg->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $frontleftLeg->getImageHeight(),
            0, $frontleftLeg->getImageHeight(),

            $frontleftLeg->getImageWidth(), 0,
            $topLeftLeg->getImageWidth() / 2, -($topLeftLeg->getImageHeight() / 2),

            $frontleftLeg->getImageWidth(), $frontleftLeg->getImageHeight(),
            $topLeftLeg->getImageWidth() / 2, $frontleftLeg->getImageHeight() - ($topLeftLeg->getImageHeight() / 2),
        ], true);
        $frontleftLeg->trimImage(9999);
        $frontleftLeg->setImagePage(0, 0, 0, 0);

        $leftLeg->compositeImage($topLeftLeg, imagick::COMPOSITE_OVER, 0, 0);
        $leftLeg->compositeImage($leftleftLeg, imagick::COMPOSITE_OVER, 0, $topLeftLeg->getImageHeight() / 2);
        $leftLeg->compositeImage($frontleftLeg, imagick::COMPOSITE_OVER, $topLeftLeg->getImageWidth() / 2 - 1, $topLeftLeg->getImageHeight() / 2);

        $leftLeg->trimImage(9999);
        $leftLeg->setImagePage(0, 0, 0, 0);
        return $leftLeg;
    }

    /**
     * Generate the right leg render
     *
     * @return void
     */
    protected static function renderRightLeg() {
        $rightLeg = new Imagick();
        $rightLeg->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topRightLeg = new Imagick(self::$imageSrc);
        $topRightLeg->cropImage(4, 4, 4, 16);
        $topRightLeg->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $topRightLeg->setimagebackgroundcolor("transparent");
        $topRightLeg->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topRightLeg->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topRightLeg->getImageHeight() / 2,

            0, $topRightLeg->getImageHeight(),
            ($topRightLeg->getImageWidth() / 4) * 3, $topRightLeg->getImageHeight(),

            $topRightLeg->getImageWidth(), 0,
            ($topRightLeg->getImageWidth() / 4) * 3, 0,

            $topRightLeg->getImageWidth(), $topRightLeg->getImageHeight(),
            $topRightLeg->getImageWidth() * 1.5, $topRightLeg->getImageHeight() / 2
        ], true);
        $topRightLeg->trimImage(9999);
        $topRightLeg->setImagePage(0, 0, 0, 0);

        $frontRightLeg = new Imagick(self::$imageSrc);

        $frontRightLeg->cropImage(4, 12, 4, 20);
        $frontRightLeg->flopImage();
        $frontRightLeg->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $frontRightLeg->setimagebackgroundcolor("transparent");
        $frontRightLeg->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontRightLeg->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $frontRightLeg->getImageHeight(),
            0, $frontRightLeg->getImageHeight(),

            $frontRightLeg->getImageWidth(), 0,
            $topRightLeg->getImageWidth() / 2, -($topRightLeg->getImageHeight() / 2),

            $frontRightLeg->getImageWidth(), $frontRightLeg->getImageHeight(),
            $topRightLeg->getImageWidth() / 2, $frontRightLeg->getImageHeight() - ($topRightLeg->getImageHeight() / 2),
        ], true);
        $frontRightLeg->trimImage(9999);
        $frontRightLeg->setImagePage(0, 0, 0, 0);

        $rightLeg->compositeImage($topRightLeg, imagick::COMPOSITE_OVER, 0, 0);
        $rightLeg->compositeImage($frontRightLeg, imagick::COMPOSITE_OVER, $topRightLeg->getImageWidth() / 2 - 1, $topRightLeg->getImageHeight() / 2);

        $rightLeg->trimImage(9999);
        $rightLeg->setImagePage(0, 0, 0, 0);
        return $rightLeg;
    }

    /**
     * Generate the helmet render
     *
     * @return void
     */
    protected static function renderHelmet() {
        $helmet = new Imagick();
        $helmet->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topHelmet = new Imagick(self::$imageSrc);
        $topHelmet->cropImage(8, 8, 40, 0);
        $topHelmet->resizeImage($topHelmet->getImageWidth() * self::$enlargeRatio, $topHelmet->getImageHeight() * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $topHelmet->setimagebackgroundcolor("transparent");
        $topHelmet->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topHelmet->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topHelmet->getImageHeight() / 2,

            0, $topHelmet->getImageHeight(),
            ($topHelmet->getImageWidth() / 4) * 3, $topHelmet->getImageHeight(),

            $topHelmet->getImageWidth(), 0,
            ($topHelmet->getImageWidth() / 4) * 3, 0,

            $topHelmet->getImageWidth(), $topHelmet->getImageHeight(),
            $topHelmet->getImageWidth() * 1.5, $topHelmet->getImageHeight() / 2
        ], true);
        $topHelmet->trimImage(9999);
        $topHelmet->setImagePage(0, 0, 0, 0);

        $frontHelmet = new Imagick(self::$imageSrc);
        $frontHelmet->cropImage(8, 8, 40, 8);
        $frontHelmet->resizeImage($frontHelmet->getImageWidth() * self::$enlargeRatio, $frontHelmet->getImageHeight() * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $frontHelmet->setimagebackgroundcolor("transparent");
        $frontHelmet->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontHelmet->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topHelmet->getImageHeight() / 2,

            0, $frontHelmet->getImageHeight(),
            0, $frontHelmet->getImageHeight() + ($topHelmet->getImageHeight() / 2),

            $frontHelmet->getImageWidth(), 0,
            $topHelmet->getImageWidth() / 2, 0,

            $frontHelmet->getImageWidth(), $frontHelmet->getImageHeight(),
            $topHelmet->getImageWidth() / 2, $frontHelmet->getImageHeight()
        ], true);
        $frontHelmet->trimImage(9999);
        $frontHelmet->setImagePage(0, 0, 0, 0);

        $leftHelmet = new Imagick(self::$imageSrc);
        $leftHelmet->cropImage(8, 8, 32, 8);
        $leftHelmet->resizeImage($leftHelmet->getImageWidth() * self::$enlargeRatio, $leftHelmet->getImageHeight() * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $leftHelmet->setimagebackgroundcolor("transparent");
        $leftHelmet->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $leftHelmet->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topHelmet->getImageHeight() / 2,

            0, $leftHelmet->getImageHeight(),
            0, $leftHelmet->getImageHeight() + $topHelmet->getImageHeight() / 2,

            $leftHelmet->getImageWidth(), 0,
            $topHelmet->getImageWidth() / 2, $leftHelmet->getImageHeight(),

            $leftHelmet->getImageWidth(), $leftHelmet->getImageHeight(),
            $topHelmet->getImageWidth() / 2, $leftHelmet->getImageHeight() + $topHelmet->getImageHeight()
        ], true);
        $leftHelmet->trimImage(9999);
        $leftHelmet->setImagePage(0, 0, 0, 0);

        $helmet->compositeImage($topHelmet, imagick::COMPOSITE_OVER, 0, 0);
        $helmet->compositeImage($leftHelmet, imagick::COMPOSITE_OVER, 0, $topHelmet->getImageHeight() / 2 - 1);
        $helmet->compositeImage($frontHelmet, imagick::COMPOSITE_OVER, $topHelmet->getImageWidth() / 2, $topHelmet->getImageHeight() / 2 - 1);

        $helmet->trimImage(0);
        return $helmet;
    }

    /**
     * Generate the jacket render
     *
     * @return void
     */
    protected static function renderJacket() {
        $jacket = new Imagick();
        $jacket->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topJacket = new Imagick(self::$imageSrc);
        $topJacket->cropImage(8, 4, 20, 32);
        $topJacket->resizeImage(8 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $topJacket->setimagebackgroundcolor("transparent");
        $topJacket->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topJacket->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, ($topJacket->getImageHeight() / 2),

            0, $topJacket->getImageHeight(),
            ($topJacket->getImageHeight() / 4) * 3, $topJacket->getImageHeight(),

            $topJacket->getImageWidth(), 0,
            ($topJacket->getImageHeight() / 4) * 3, 0,

            $topJacket->getImageWidth(), $topJacket->getImageHeight(),
            $topJacket->getImageHeight() * 1.5, $topJacket->getImageHeight() / 2,
        ], true);
        $topJacket->trimImage(9999);
        $topJacket->setImagePage(0, 0, 0, 0);

        $frontJacket = new Imagick(self::$imageSrc);
        $frontJacket->cropImage(8, 12, 20, 36);
        $frontJacket->resizeImage($frontJacket->getImageWidth() * self::$enlargeRatio, $frontJacket->getImageHeight() * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $frontJacket->setimagebackgroundcolor("transparent");
        $frontJacket->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontJacket->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            //top left
            0, 0,
            0, 0,

            //bottom left
            0, $frontJacket->getImageHeight(),
            0, $frontJacket->getImageHeight(),

            //top right
            $frontJacket->getImageWidth(), 0,
            $topJacket->getImageWidth(), -($topJacket->getImageHeight()),

            //bottom right
            $frontJacket->getImageWidth(), $frontJacket->getImageHeight(),
            $topJacket->getImageWidth(), $frontJacket->getImageHeight() - ($topJacket->getImageHeight()),
        ], true);
        $frontJacket->trimImage(999);
        $frontJacket->setImagePage(0, 0, 0, 0);

        $jacket->compositeImage($topJacket, imagick::COMPOSITE_OVER, 0, 0);
        $jacket->compositeImage($frontJacket, imagick::COMPOSITE_OVER, 22, 0);
        $jacket->trimImage(9999);
        $jacket->setImagePage(0, 0, 0, 0);

        return $jacket;
    }

    /**
     * Generate the left sleeve render
     *
     * @return void
     */
    protected static function renderLeftSleeve() {
        $leftSleeve = new Imagick();
        $leftSleeve->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topLeftSleeve = new Imagick(self::$imageSrc);
        $topLeftSleeve->cropImage(4, 4, 52, 48);
        $topLeftSleeve->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $topLeftSleeve->setimagebackgroundcolor("transparent");
        $topLeftSleeve->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topLeftSleeve->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topLeftSleeve->getImageHeight() / 2,

            0, $topLeftSleeve->getImageHeight(),
            ($topLeftSleeve->getImageWidth() / 4) * 3, $topLeftSleeve->getImageHeight(),

            $topLeftSleeve->getImageWidth(), 0,
            ($topLeftSleeve->getImageWidth() / 4) * 3, 0,

            $topLeftSleeve->getImageWidth(), $topLeftSleeve->getImageHeight(),
            $topLeftSleeve->getImageWidth() * 1.5, $topLeftSleeve->getImageHeight() / 2
        ], true);
        $topLeftSleeve->trimImage(9999);
        $topLeftSleeve->setImagePage(0, 0, 0, 0);

        $leftLeftSleeve = new Imagick(self::$imageSrc);
        $leftLeftSleeve->cropImage(4, 12, 56, 52);
        $leftLeftSleeve->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $leftLeftSleeve->setimagebackgroundcolor("transparent");
        $leftLeftSleeve->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $leftLeftSleeve->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $leftLeftSleeve->getImageHeight(),
            0, $leftLeftSleeve->getImageHeight(),

            $leftLeftSleeve->getImageWidth(), 0,
            $topLeftSleeve->getImageWidth() / 2, $topLeftSleeve->getImageHeight() / 2,

            $leftLeftSleeve->getImageWidth(), $leftLeftSleeve->getImageHeight(),
            $topLeftSleeve->getImageWidth() / 2, $leftLeftSleeve->getImageHeight() + ($topLeftSleeve->getImageHeight() / 2)
        ], true);
        $leftLeftSleeve->trimImage(9999);
        $leftLeftSleeve->setImagePage(0, 0, 0, 0);

        $frontLeftSleeve = new Imagick(self::$imageSrc);
        $frontLeftSleeve->cropImage(4, 12, 52, 52);
        $frontLeftSleeve->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $frontLeftSleeve->setimagebackgroundcolor("transparent");
        $frontLeftSleeve->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontLeftSleeve->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $frontLeftSleeve->getImageHeight(),
            0, $frontLeftSleeve->getImageHeight(),

            $frontLeftSleeve->getImageWidth(), 0,
            $topLeftSleeve->getImageWidth() / 2, -($topLeftSleeve->getImageHeight() / 2),

            $frontLeftSleeve->getImageWidth(), $frontLeftSleeve->getImageHeight(),
            $topLeftSleeve->getImageWidth() / 2, $frontLeftSleeve->getImageHeight() - ($topLeftSleeve->getImageHeight() / 2),
        ], true);
        $frontLeftSleeve->trimImage(9999);
        $frontLeftSleeve->setImagePage(0, 0, 0, 0);

        $leftSleeve->compositeImage($topLeftSleeve, imagick::COMPOSITE_OVER, 0, 0);
        $leftSleeve->compositeImage($leftLeftSleeve, imagick::COMPOSITE_OVER, 0, $topLeftSleeve->getImageHeight() / 2);
        $leftSleeve->compositeImage($frontLeftSleeve, imagick::COMPOSITE_OVER, $topLeftSleeve->getImageWidth() / 2 - 1, $topLeftSleeve->getImageHeight() / 2);

        $leftSleeve->trimImage(9999);
        $leftSleeve->setImagePage(0, 0, 0, 0);
        return $leftSleeve;
    }

    /**
     * Generate the right arm render
     *
     * @return void
     */
    protected static function renderRightSleeve() {
        $rightSleeve = new Imagick();
        $rightSleeve->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topRightSleeve = new Imagick(self::$imageSrc);
        $topRightSleeve->cropImage(4, 4, 44, 32);
        $topRightSleeve->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $topRightSleeve->setimagebackgroundcolor("transparent");
        $topRightSleeve->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topRightSleeve->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topRightSleeve->getImageHeight() / 2,

            0, $topRightSleeve->getImageHeight(),
            ($topRightSleeve->getImageWidth() / 4) * 3, $topRightSleeve->getImageHeight(),

            $topRightSleeve->getImageWidth(), 0,
            ($topRightSleeve->getImageWidth() / 4) * 3, 0,

            $topRightSleeve->getImageWidth(), $topRightSleeve->getImageHeight(),
            $topRightSleeve->getImageWidth() * 1.5, $topRightSleeve->getImageHeight() / 2
        ], true);
        $topRightSleeve->trimImage(9999);
        $topRightSleeve->setImagePage(0, 0, 0, 0);

        $frontRightSleeve = new Imagick(self::$imageSrc);
        $frontRightSleeve->cropImage(4, 12, 44, 36);
        $frontRightSleeve->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $frontRightSleeve->setimagebackgroundcolor("transparent");
        $frontRightSleeve->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontRightSleeve->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $frontRightSleeve->getImageHeight(),
            0, $frontRightSleeve->getImageHeight(),

            $frontRightSleeve->getImageWidth(), 0,
            $topRightSleeve->getImageWidth() / 2, -($topRightSleeve->getImageHeight() / 2),

            $frontRightSleeve->getImageWidth(), $frontRightSleeve->getImageHeight(),
            $topRightSleeve->getImageWidth() / 2, $frontRightSleeve->getImageHeight() - ($topRightSleeve->getImageHeight() / 2),
        ], true);
        $frontRightSleeve->trimImage(9999);
        $frontRightSleeve->setImagePage(0, 0, 0, 0);

        $rightSleeve->compositeImage($topRightSleeve, imagick::COMPOSITE_OVER, 0, 0);
        $rightSleeve->compositeImage($frontRightSleeve, imagick::COMPOSITE_OVER, $topRightSleeve->getImageWidth() / 2 - 1, $topRightSleeve->getImageHeight() / 2);

        $rightSleeve->trimImage(9999);
        $rightSleeve->setImagePage(0, 0, 0, 0);
        return $rightSleeve;
    }

    /**
     * Generate the left leg render
     *
     * @return void
     */
    protected static function renderLeftPants() {
        $leftPants = new Imagick();
        $leftPants->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topLeftPants = new Imagick(self::$imageSrc);
        $topLeftPants->cropImage(4, 4, 4, 48);
        $topLeftPants->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $topLeftPants->setimagebackgroundcolor("transparent");
        $topLeftPants->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topLeftPants->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topLeftPants->getImageHeight() / 2,

            0, $topLeftPants->getImageHeight(),
            ($topLeftPants->getImageWidth() / 4) * 3, $topLeftPants->getImageHeight(),

            $topLeftPants->getImageWidth(), 0,
            ($topLeftPants->getImageWidth() / 4) * 3, 0,

            $topLeftPants->getImageWidth(), $topLeftPants->getImageHeight(),
            $topLeftPants->getImageWidth() * 1.5, $topLeftPants->getImageHeight() / 2
        ], true);
        $topLeftPants->trimImage(9999);
        $topLeftPants->setImagePage(0, 0, 0, 0);

        $leftLeftPants = new Imagick(self::$imageSrc);
        $leftLeftPants->cropImage(4, 12, 8, 52);
        $leftLeftPants->flopImage();
        $leftLeftPants->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $leftLeftPants->setimagebackgroundcolor("transparent");
        $leftLeftPants->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $leftLeftPants->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $leftLeftPants->getImageHeight(),
            0, $leftLeftPants->getImageHeight(),

            $leftLeftPants->getImageWidth(), 0,
            $topLeftPants->getImageWidth() / 2, $topLeftPants->getImageHeight() / 2,

            $leftLeftPants->getImageWidth(), $leftLeftPants->getImageHeight(),
            $topLeftPants->getImageWidth() / 2, $leftLeftPants->getImageHeight() + ($topLeftPants->getImageHeight() / 2)
        ], true);
        $leftLeftPants->trimImage(9999);
        $leftLeftPants->setImagePage(0, 0, 0, 0);

        $frontLeftPants = new Imagick(self::$imageSrc);
        $frontLeftPants->cropImage(4, 12, 4, 52);
        $frontLeftPants->flopImage();
        $frontLeftPants->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $frontLeftPants->setimagebackgroundcolor("transparent");
        $frontLeftPants->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontLeftPants->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $frontLeftPants->getImageHeight(),
            0, $frontLeftPants->getImageHeight(),

            $frontLeftPants->getImageWidth(), 0,
            $topLeftPants->getImageWidth() / 2, -($topLeftPants->getImageHeight() / 2),

            $frontLeftPants->getImageWidth(), $frontLeftPants->getImageHeight(),
            $topLeftPants->getImageWidth() / 2, $frontLeftPants->getImageHeight() - ($topLeftPants->getImageHeight() / 2),
        ], true);
        $frontLeftPants->trimImage(9999);
        $frontLeftPants->setImagePage(0, 0, 0, 0);

        $leftPants->compositeImage($topLeftPants, imagick::COMPOSITE_OVER, 0, 0);
        $leftPants->compositeImage($leftLeftPants, imagick::COMPOSITE_OVER, 0, $topLeftPants->getImageHeight() / 2);
        $leftPants->compositeImage($frontLeftPants, imagick::COMPOSITE_OVER, $topLeftPants->getImageWidth() / 2 - 1, $topLeftPants->getImageHeight() / 2);

        $leftPants->trimImage(9999);
        $leftPants->setImagePage(0, 0, 0, 0);
        return $leftPants;
    }

    /**
     * Generate the right leg render
     *
     * @return void
     */
    protected static function renderRightPants() {
        $rightPants = new Imagick();
        $rightPants->newImage(100 * self::$enlargeRatio, 100 * self::$enlargeRatio, new ImagickPixel('transparent'), 'png');

        $topRightPants = new Imagick(self::$imageSrc);
        $topRightPants->cropImage(4, 4, 4, 32);
        $topRightPants->resizeImage(4 * self::$enlargeRatio, 4 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $topRightPants->setimagebackgroundcolor("transparent");
        $topRightPants->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $topRightPants->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, $topRightPants->getImageHeight() / 2,

            0, $topRightPants->getImageHeight(),
            ($topRightPants->getImageWidth() / 4) * 3, $topRightPants->getImageHeight(),

            $topRightPants->getImageWidth(), 0,
            ($topRightPants->getImageWidth() / 4) * 3, 0,

            $topRightPants->getImageWidth(), $topRightPants->getImageHeight(),
            $topRightPants->getImageWidth() * 1.5, $topRightPants->getImageHeight() / 2
        ], true);
        $topRightPants->trimImage(9999);
        $topRightPants->setImagePage(0, 0, 0, 0);

        $frontRightPants = new Imagick(self::$imageSrc);
        $frontRightPants->cropImage(4, 12, 4, 36);
        $frontRightPants->flopImage();
        $frontRightPants->resizeImage(4 * self::$enlargeRatio, 12 * self::$enlargeRatio, imagick::FILTER_POINT, 0);
        $frontRightPants->setimagebackgroundcolor("transparent");
        $frontRightPants->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_BACKGROUND);
        $frontRightPants->distortImage(Imagick::DISTORTION_PERSPECTIVE, [
            0, 0,
            0, 0,

            0, $frontRightPants->getImageHeight(),
            0, $frontRightPants->getImageHeight(),

            $frontRightPants->getImageWidth(), 0,
            $topRightPants->getImageWidth() / 2, -($topRightPants->getImageHeight() / 2),

            $frontRightPants->getImageWidth(), $frontRightPants->getImageHeight(),
            $topRightPants->getImageWidth() / 2, $frontRightPants->getImageHeight() - ($topRightPants->getImageHeight() / 2),
        ], true);
        $frontRightPants->trimImage(9999);
        $frontRightPants->setImagePage(0, 0, 0, 0);

        $rightPants->compositeImage($topRightPants, imagick::COMPOSITE_OVER, 0, 0);
        $rightPants->compositeImage($frontRightPants, imagick::COMPOSITE_OVER, $topRightPants->getImageWidth() / 2 - 1, $topRightPants->getImageHeight() / 2);

        $rightPants->trimImage(9999);
        $rightPants->setImagePage(0, 0, 0, 0);
        return $rightPants;
    }
}