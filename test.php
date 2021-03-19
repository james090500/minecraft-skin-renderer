<?php
    use james090500\MinecraftSkinRenderer;
    require_once 'vendor/autoload.php';

    $skinUrl = "https://minecraftapi.net/api/v1/profile/ba4161c03a42496c8ae07d13372f3371/skin";

    Header("Content-Type: image/png");
    // echo MinecraftSkinRenderer::render('images/alex.png');
    // echo MinecraftSkinRenderer::render('images/steve.png');
    // echo MinecraftSkinRenderer::render('images/mov51.png');
    // echo MinecraftSkinRenderer::render('images/james090500.png');
    // echo MinecraftSkinRenderer::render('images/siriuo.png');

    echo MinecraftSkinRenderer::render($skinUrl);
    // echo MinecraftSkinRenderer::render(file_get_contents($skinUrl));