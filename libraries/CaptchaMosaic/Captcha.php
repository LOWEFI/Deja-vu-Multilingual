<?php

// libraries/Captcha.php

// Mosaic

class Captcha
{
    protected $gridRows;
    protected $gridCols;
    protected $tileSize;
    protected $sourceImagePath;
    protected $offIndices = [];
    protected $shapeX = 0;
    protected $shapeY = 0;
    protected $shapeW = 20;
    protected $shapeH = 20;
    protected $imageFolder;

    public function __construct($gridRows = 5, $gridCols = 5, $tileSize = 60, $imageFolder = 'assets/images/Captcha/source/', $isVerification = false)
    {
        $this->gridRows = $gridRows;
        $this->gridCols = $gridCols;
        $this->tileSize = $tileSize;
        $this->imageFolder = $imageFolder;
        if (!$isVerification) {
            $this->initializeCaptcha();
        } else {
            $this->offIndices = $_SESSION['captcha_off_indices'] ?? [];
            $this->sourceImagePath = $_SESSION['captcha_source_image'] ?? null;
            $this->shapeX = $_SESSION['captcha_shape_x'] ?? 0;
            $this->shapeY = $_SESSION['captcha_shape_y'] ?? 0;
            $this->shapeW = $_SESSION['captcha_shape_w'] ?? 20;
            $this->shapeH = $_SESSION['captcha_shape_h'] ?? 20;
        }
    }

    protected function initializeCaptcha()
    {
        $this->sourceImagePath = $this->getRandomImageFromFolder($this->imageFolder);
        $factor = rand(10, 20) / 10.0;
        $baseSize = 40;
        $this->shapeW = max(1, (int) round($baseSize * $factor));
        $this->shapeH = max(1, (int) round($baseSize * $factor));
        $mosaicWidth = $this->gridCols * $this->tileSize;
        $mosaicHeight = $this->gridRows * $this->tileSize;
        $maxX = max(0, $mosaicWidth - $this->shapeW);
        $maxY = max(0, $mosaicHeight - $this->shapeH);
        $this->shapeX = rand(0, $maxX);
        $this->shapeY = rand(0, $maxY);
        $tileIndex = 0;
        for ($r = 0; $r < $this->gridRows; $r++) {
            for ($c = 0; $c < $this->gridCols; $c++) {
                $tileLeft = $c * $this->tileSize;
                $tileTop = $r * $this->tileSize;
                $tileRight = $tileLeft + $this->tileSize;
                $tileBottom = $tileTop + $this->tileSize;
                $shapeLeft = $this->shapeX;
                $shapeTop = $this->shapeY;
                $shapeRight = $shapeLeft + $this->shapeW;
                $shapeBottom = $shapeTop + $this->shapeH;
                $overlap = !($shapeRight <= $tileLeft || $shapeLeft >= $tileRight || $shapeBottom <= $tileTop || $shapeTop >= $tileBottom);
                if ($overlap) {
                    $this->offIndices[] = $tileIndex;
                }
                $tileIndex++;
            }
        }
        $_SESSION['captcha_source_image'] = $this->sourceImagePath;
        $_SESSION['captcha_off_indices'] = $this->offIndices;
        $_SESSION['captcha_shape_x'] = $this->shapeX;
        $_SESSION['captcha_shape_y'] = $this->shapeY;
        $_SESSION['captcha_shape_w'] = $this->shapeW;
        $_SESSION['captcha_shape_h'] = $this->shapeH;
    }

    protected function getRandomImageFromFolder($folder)
    {
        $files = glob($folder . "*.png");
        return (count($files) > 0) ? $files[array_rand($files)] : null;
    }

    public function render()
    {
        $mosaicWidth = $this->gridCols * $this->tileSize;
        $mosaicHeight = $this->gridRows * $this->tileSize;
        $mosaicImg = imagecreatetruecolor($mosaicWidth, $mosaicHeight);
        imagealphablending($mosaicImg, false);
        imagesavealpha($mosaicImg, true);
        $source = @imagecreatefrompng($this->sourceImagePath);
        if (!$source) {
            $bgColor = imagecolorallocate($mosaicImg, 200, 200, 200);
            imagefilledrectangle($mosaicImg, 0, 0, $mosaicWidth, $mosaicHeight, $bgColor);
        } else {
            imagecopyresampled($mosaicImg, $source, 0, 0, 0, 0, $mosaicWidth, $mosaicHeight, imagesx($source), imagesy($source));
            imagedestroy($source);
        }
        imagealphablending($mosaicImg, true);
        $shapeImage = $this->createRandomShapeWithBlur($this->shapeW, $this->shapeH, $mosaicImg, $this->shapeX, $this->shapeY, 6);
        imagecopy($mosaicImg, $shapeImage, $this->shapeX, $this->shapeY, 0, 0, $this->shapeW, $this->shapeH);
        imagedestroy($shapeImage);
        $this->applyRandomShapesOverlay($mosaicImg, rand(5, 10));
        $html = '<style>
            .captcha-form { font-family: Arial, sans-serif; margin: 0; padding: 0; }
            .captcha-form p { margin: 4px 0; font-size: 14px; }
            .captcha-table { border-collapse: collapse; }
            .captcha-table tr { gap: 0; margin-bottom: 0; }
            .captcha-table td { padding: 0; margin: 0; }
            .captcha-label { cursor: pointer; display: block; position: relative; }
            .captcha-label input { display: none; }
            .captcha-img { display: block; border: 1px solid transparent; box-sizing: border-box; }
            .captcha-label input:checked + .captcha-img { border-color: #f00; }
            .captcha-submit { margin-top: 1rem; margin-bottom: 1rem; height: 1.5rem; }
        </style>';
        $html .= '<p><strong>Sélectionnez toutes les cases qui contiennent la tâche transparente.</strong></p>';
        $html .= '<form method="POST" action="" class="captcha-form">';
        $html .= '<table class="captcha-table" style="border:none;">';
        $tileIndex = 0;
        for ($r = 0; $r < $this->gridRows; $r++) {
            $html .= '<tr>';
            for ($c = 0; $c < $this->gridCols; $c++) {
                $x = $c * $this->tileSize;
                $y = $r * $this->tileSize;
                $tileImg = imagecreatetruecolor($this->tileSize, $this->tileSize);
                imagealphablending($tileImg, false);
                imagesavealpha($tileImg, true);
                imagecopy($tileImg, $mosaicImg, 0, 0, $x, $y, $this->tileSize, $this->tileSize);
                if (rand(0, 100) < 50) {
                    $this->increaseSaturation($tileImg, 1.1 + (rand(-5, 5) / 100.0));
                }
                $this->applyRandomNoise($tileImg, rand(6, 10));
                imagealphablending($tileImg, true);
                ob_start();
                imagepng($tileImg);
                $tileData = ob_get_clean();
                imagedestroy($tileImg);
                $base64 = base64_encode($tileData);
                $html .= '<td>
                    <label class="captcha-label">
                        <input type="checkbox" name="captcha_tiles[]" value="' . $tileIndex . '">
                        <img src="data:image/png;base64,' . $base64 . '" width="' . $this->tileSize . '" height="' . $this->tileSize . '" class="captcha-img">
                    </label>
                </td>';
                $tileIndex++;
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        $html .= '<input type="submit" value="Valider" class="captcha-submit">';
        $html .= '</form>';
        imagedestroy($mosaicImg);
        return $html;
    }

    public function verify($selectedTiles)
    {
        $selectedTiles = array_map('intval', $selectedTiles);
        sort($selectedTiles);
        $correct = $this->offIndices;
        sort($correct);
        unset($_SESSION['captcha_off_indices']);
        unset($_SESSION['captcha_source_image']);
        unset($_SESSION['captcha_shape_x']);
        unset($_SESSION['captcha_shape_y']);
        unset($_SESSION['captcha_shape_w']);
        unset($_SESSION['captcha_shape_h']);
        return ($selectedTiles === $correct);
    }

    protected function createRandomShapeWithBlur($width, $height, $background, $offsetX, $offsetY, $blurPasses = 2)
    {
        $mask = $this->createRandomMask($width, $height);
        for ($i = 0; $i < $blurPasses; $i++) {
            imagefilter($mask, IMG_FILTER_GAUSSIAN_BLUR);
        }
        $shape = imagecreatetruecolor($width, $height);
        imagealphablending($shape, false);
        imagesavealpha($shape, true);
        $transparent = imagecolorallocatealpha($shape, 0, 0, 0, 127);
        imagefilledrectangle($shape, 0, 0, $width, $height, $transparent);
        $this->applyBackgroundSamplingWithFeather($shape, $mask, $background, $offsetX, $offsetY);
        imagedestroy($mask);
        return $shape;
    }

    protected function createRandomMask($width, $height)
    {
        $mask = imagecreatetruecolor($width, $height);
        $black = imagecolorallocate($mask, 0, 0, 0);
        $white = imagecolorallocate($mask, 255, 255, 255);
        imagefilledrectangle($mask, 0, 0, $width, $height, $black);
        $nPolygons = rand(2, 4);
        for ($i = 0; $i < $nPolygons; $i++) {
            $pointsCount = rand(5, 10) * 2;
            $points = [];
            for ($p = 0; $p < $pointsCount; $p += 2) {
                $px = rand(-$width / 2, $width * 1.5);
                $py = rand(-$height / 2, $height * 1.5);
                $points[] = $px;
                $points[] = $py;
            }
            imagefilledpolygon($mask, $points, $pointsCount / 2, $white);
        }
        $nHoles = rand(0, 2);
        for ($i = 0; $i < $nHoles; $i++) {
            $diam = rand(10, max($width, $height));
            $cx = rand(0, $width);
            $cy = rand(0, $height);
            imagefilledellipse($mask, $cx, $cy, $diam, $diam, $black);
        }
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if (rand(0, 100) < 2) {
                    $pix = imagecolorat($mask, $x, $y);
                    $c = imagecolorsforindex($mask, $pix);
                    $gray = $c['red'];
                    $flip = $gray === 255 ? $black : $white;
                    imagesetpixel($mask, $x, $y, $flip);
                }
            }
        }
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $distEdge = min($x, $y, $width - 1 - $x, $height - 1 - $y);
                if ($distEdge < 5) {
                    $pix = imagecolorat($mask, $x, $y);
                    $c = imagecolorsforindex($mask, $pix);
                    $g = $c['red'];
                    $fade = (int) ($g * ($distEdge / 5.0));
                    $col = imagecolorallocate($mask, $fade, $fade, $fade);
                    imagesetpixel($mask, $x, $y, $col);
                }
            }
        }
        return $mask;
    }

    protected function applyBackgroundSamplingWithFeather($shape, $mask, $background, $offsetX, $offsetY)
    {
        $baseR = rand(128, 255);
        $baseG = rand(0, 128);
        $baseB = rand(0, 128);
        $blendFactor = 0.4;
        $w = imagesx($shape);
        $h = imagesy($shape);
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $grayIndex = imagecolorat($mask, $x, $y);
                $colors = imagecolorsforindex($mask, $grayIndex);
                $val = $colors['red'];
                $alpha = (int) (127 - (127 * ($val / 255.0)));
                if ($alpha < 127) {
                    $bgX = $offsetX + $x;
                    $bgY = $offsetY + $y;
                    if ($bgX < 0 || $bgY < 0 || $bgX >= imagesx($background) || $bgY >= imagesy($background)) {
                        continue;
                    }
                    $bgColorIndex = imagecolorat($background, $bgX, $bgY);
                    $bgColors = imagecolorsforindex($background, $bgColorIndex);
                    $rC = (int) (($bgColors['red'] * (1 - $blendFactor)) + ($baseR * $blendFactor));
                    $gC = (int) (($bgColors['green'] * (1 - $blendFactor)) + ($baseG * $blendFactor));
                    $bC = (int) (($bgColors['blue'] * (1 - $blendFactor)) + ($baseB * $blendFactor));
                    if (rand(0, 100) < 15) {
                        $rC += rand(-25, 25);
                        $gC += rand(-25, 25);
                        $bC += rand(-25, 25);
                    }
                    $rC = max(0, min(255, $rC));
                    $gC = max(0, min(255, $gC));
                    $bC = max(0, min(255, $bC));
                    $col = imagecolorallocatealpha($shape, $rC, $gC, $bC, $alpha);
                    imagesetpixel($shape, $x, $y, $col);
                }
            }
        }
    }

    protected function increaseSaturation($img, $factor)
    {
        $width = imagesx($img);
        $height = imagesy($img);
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                list($h, $s, $l) = $this->rgb2hsl($r, $g, $b);
                $s *= $factor;
                $s = max(0, min(1, $s));
                list($rNew, $gNew, $bNew) = $this->hsl2rgb($h, $s, $l);
                $color = imagecolorallocate($img, $rNew, $gNew, $bNew);
                imagesetpixel($img, $x, $y, $color);
            }
        }
    }

    protected function applyRandomNoise($img, $intensity = 10)
    {
        $width = imagesx($img);
        $height = imagesy($img);
        for ($i = 0; $i < $intensity * 100; $i++) {
            $x = rand(0, $width - 1);
            $y = rand(0, $height - 1);
            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $r += rand(-30, 30);
            $g += rand(-30, 30);
            $b += rand(-30, 30);
            $r = max(0, min(255, $r));
            $g = max(0, min(255, $g));
            $b = max(0, min(255, $b));
            $color = imagecolorallocate($img, $r, $g, $b);
            imagesetpixel($img, $x, $y, $color);
        }
    }

    protected function applyRandomShapesOverlay($img, $count = 3)
    {
        $width = imagesx($img);
        $height = imagesy($img);
        for ($i = 0; $i < $count; $i++) {
            $col = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
            $shapeType = rand(0, 2);
            switch ($shapeType) {
                case 0:
                    $x1 = rand(0, $width - 1);
                    $y1 = rand(0, $height - 1);
                    $x2 = $x1 + rand(-20, 20);
                    $y2 = $y1 + rand(-20, 20);
                    imageline($img, $x1, $y1, $x2, $y2, $col);
                    break;
                case 1:
                    $x1 = rand(0, $width - 1);
                    $y1 = rand(0, $height - 1);
                    $w = rand(10, 20);
                    $h = rand(10, 20);
                    imagerectangle($img, $x1, $y1, $x1 + $w, $y1 + $h, $col);
                    break;
                case 2:
                    $cx = rand(0, $width - 1);
                    $cy = rand(0, $height - 1);
                    $radius = rand(5, 15);
                    imagearc($img, $cx, $cy, $radius * 2, $radius * 2, 0, 360, $col);
                    break;
            }
        }
    }

    protected function rgb2hsl($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        if ($max == $min) {
            $h = 0;
            $s = 0;
        } else {
            $d = $max - $min;
            $s = ($l > 0.5) ? ($d / (2 - $max - $min)) : ($d / ($max + $min));
            if ($max == $r) {
                $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
            } elseif ($max == $g) {
                $h = ($b - $r) / $d + 2;
            } else {
                $h = ($r - $g) / $d + 4;
            }
            $h /= 6;
        }
        return [$h, $s, $l];
    }

    protected function hsl2rgb($h, $s, $l)
    {
        if ($s == 0) {
            $r = $l;
            $g = $l;
            $b = $l;
        } else {
            $q = ($l < 0.5) ? ($l * (1 + $s)) : ($l + $s - $l * $s);
            $p = 2 * $l - $q;
            $r = $this->hue2rgb($p, $q, $h + 1 / 3);
            $g = $this->hue2rgb($p, $q, $h);
            $b = $this->hue2rgb($p, $q, $h - 1 / 3);
        }
        return [(int) round($r * 255), (int) round($g * 255), (int) round($b * 255)];
    }

    protected function hue2rgb($p, $q, $t)
    {
        if ($t < 0)
            $t += 1;
        if ($t > 1)
            $t -= 1;
        if ($t < 1 / 6)
            return $p + ($q - $p) * 6 * $t;
        if ($t < 1 / 2)
            return $q;
        if ($t < 2 / 3)
            return $p + ($q - $p) * (2 / 3 - $t) * 6;
        return $p;
    }
}

?>