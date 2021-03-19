# Minecraft Skin Renderer
Renders a 3D minecraft skin using PHP. Work in progress.

**To Do**
 - [ ] Alex Skin Support

## Usage
```php
require_once 'vendor/autoload.php';
use james090500\MinecraftSkinRenderer;
$skin = "https://minecraftapi.net/api/v1/profile/ba4161c03a42496c8ae07d13372f3371/skin";
$renderedSkin = MinecraftSkinRenderer::render($skin);
header("Content-Type: image/png");
echo $renderedSkin;
```
`$skin` can be a url, file handle or a path to a file. The render method returns a rendered skin image.

## Timings
I ran 250 tests compiled of 5 different skins on a i5-10500. The average runtime of all these tests where 342ms per operation. It would be wise to cache the images after generation for a period of time.

**Skins tests and results**
Average - 342ms
 1. [Alex](https://namemc.com/skin/c178117c21bd0a1c) - 395ms
 2. [Steve](https://namemc.com/skin/12b92a9206470fe2) - 410ms
 3. [Mov51](https://namemc.com/skin/2e8a0ff28885ef08) - 416ms
 4. [Siriuo](https://namemc.com/skin/16550fd305913e82) - 163ms
 5. [james090500](https://namemc.com/skin/2d6c6571b285553c) - 217ms

## Authors
- [James Harrison](https://github.com/james090500)