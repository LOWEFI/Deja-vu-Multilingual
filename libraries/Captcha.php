<?php

// libraries/Captcha.php

class Captcha
{
    protected $width;
    protected $height;
    protected $count;
    protected $correctIndex;
    protected $positions = [];
    protected $buttonName;

    public function __construct($count = 10, $width = 300, $height = 150, $is_verification = false, $buttonName = 'captcha_click')
    {
        $this->width = $width;
        $this->height = $height;
        $this->count = $count;
        $this->buttonName = $buttonName;

        if (!$is_verification) {
            $this->correctIndex = rand(0, $count - 1);
            $_SESSION['captcha_correct_index'] = $this->correctIndex;
            $this->generatePositions();
            $_SESSION['captcha_correct_position'] = $this->positions[$this->correctIndex];
        } else {
            $this->correctIndex = $_SESSION['captcha_correct_index'] ?? null;
            $this->positions = $_SESSION['captcha_positions'] ?? [];
        }
    }

    public function render()
    {
        $this->generateImage();
        $b64 = $this->getBase64();
        $html = '<div style="display:block;">';
        $html .= '<small>';
        $html .= Language::get('captcha_help');
        $html .= '</small>';
        $html .= '<input type="image" style="display:block; height:auto; width:auto;" src="data:image/png;base64,' . $b64 . '" name="' . $this->buttonName . '" width="' . $this->width . '" height="' . $this->height . '">';
        $html .= '<input type="hidden" name="' . $this->buttonName . '" value="submitted">';
        $html .= '</div>';
        return $html;
    }

    public function verify($clickX, $clickY)
    {
        if (!isset($_SESSION['captcha_correct_index']) || !isset($_SESSION['captcha_correct_position'])) {
            return false;
        }
        $cx = $_SESSION['captcha_correct_position']['x'];
        $cy = $_SESSION['captcha_correct_position']['y'];
        $size = $_SESSION['captcha_correct_position']['size'] ?? 24;
        unset($_SESSION['captcha_correct_index'], $_SESSION['captcha_correct_position'], $_SESSION['captcha_positions']);
        $baseRadius = floor($size / 2);
        $tolerance = 10;
        $effectiveRadius = $baseRadius + $tolerance;
        $dx = $clickX - $cx;
        $dy = $clickY - $cy;
        return (($dx * $dx + $dy * $dy) <= ($effectiveRadius * $effectiveRadius));
    }

    protected function generatePositions()
    {
        $positions = [];
        $maxAttempts = 30;
        $size = 24;
        $spacing = 10;
        for ($i = 0; $i < $this->count; $i++) {
            $placed = false;
            $attempts = 0;
            while (!$placed && $attempts < $maxAttempts) {
                $x = rand($size, $this->width - $size);
                $y = rand($size, $this->height - $size);
                $ok = true;
                foreach ($positions as $p) {
                    $dx = $p['x'] - $x;
                    $dy = $p['y'] - $y;
                    if (($dx * $dx + $dy * $dy) < pow($size + $spacing, 2)) {
                        $ok = false;
                        break;
                    }
                }
                if ($ok) {
                    $positions[] = [
                        'x' => $x,
                        'y' => $y,
                        'shape' => ($i === $this->correctIndex) ? 'open_circle' : 'circle',
                        'size' => $size
                    ];
                    $placed = true;
                }
                $attempts++;
            }
            if (!$placed) {
                $positions[] = [
                    'x' => rand($size, $this->width - $size),
                    'y' => rand($size, $this->height - $size),
                    'shape' => ($i === $this->correctIndex) ? 'open_circle' : 'circle',
                    'size' => $size
                ];
            }
        }
        $this->positions = $positions;
        $_SESSION['captcha_positions'] = $positions;
    }

    protected function generateImage()
    {
        $img = imagecreatetruecolor($this->width, $this->height);
        imageantialias($img, true);
        imagesavealpha($img, true);
        $bg = imagecolorallocate($img, 28, 30, 35);
        imagefilledrectangle($img, 0, 0, $this->width, $this->height, $bg);
        for ($i = 0; $i < 100; $i++) {
            $noiseColor = imagecolorallocatealpha($img, rand(200, 255), rand(200, 255), rand(200, 255), rand(80, 120));
            imageline($img, rand(0, $this->width), rand(0, $this->height), rand(0, $this->width), rand(0, $this->height), $noiseColor);
        }
        for ($i = 0; $i < 30; $i++) {
            $noiseCircleColor = imagecolorallocatealpha($img, rand(200, 255), rand(200, 255), rand(200, 255), rand(90, 110));
            $circleSize = rand(5, 20);
            $xPos = rand(0, $this->width);
            $yPos = rand(0, $this->height);
            imagefilledellipse($img, $xPos, $yPos, $circleSize, $circleSize, $noiseCircleColor);
        }
        $fg = imagecolorallocate($img, 255, 255, 255);
        foreach ($this->positions as $p) {
            $x = $p['x'];
            $y = $p['y'];
            $sz = $p['size'];
            if ($p['shape'] === 'open_circle') {
                imagearc($img, $x, $y, $sz, $sz, 15, 345, $fg);
            } else {
                imagearc($img, $x, $y, $sz, $sz, 0, 360, $fg);
            }
        }
        ob_start();
        imagepng($img);
        $_SESSION['captcha_img'] = ob_get_contents();
        ob_end_clean();
        imagedestroy($img);
    }

    protected function getBase64()
    {
        return base64_encode($_SESSION['captcha_img']);
    }

    public function getButtonName()
    {
        return $this->buttonName;
    }
}

?>