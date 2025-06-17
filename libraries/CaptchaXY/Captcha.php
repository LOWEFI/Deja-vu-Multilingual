<?php

// libraries/Captcha.php

class Captcha
{
    protected $width;
    protected $height;
    protected $count;
    protected $correctIndex;
    protected $positions = [];

    public function __construct($count = 10, $width = 450, $height = 250, $is_verification = false)
    {
        $this->width = $width;
        $this->height = $height;
        $this->count = $count;
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
        $html = '<div style="display:inline-block; border:1px solid #fff;">';
        $html = '<small>Veuillez sélectionner le cercle partiellement ouvert.</small>';
        $html .= '<form method="POST" style="margin:0; padding:0;">';
        $html .= '<input type="image" style="display:block; height:auto; width:auto;" src="data:image/png;base64,' . $b64 . '" name="captcha_click" width="' . $this->width . '" height="' . $this->height . '">';
        $html .= '</form>';
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
        $shape = $_SESSION['captcha_correct_position']['shape'] ?? 'circle';
        // On n'a plus de vérification spécifique pour le triangle ; 
        // les deux types (normal et "open_circle") sont testés avec la même méthode (détection circulaire)
        unset($_SESSION['captcha_correct_index'], $_SESSION['captcha_correct_position'], $_SESSION['captcha_positions']);
        
        $r = floor($size / 2);
        $dx = $clickX - $cx;
        $dy = $clickY - $cy;
        return (($dx * $dx + $dy * $dy) <= ($r * $r));
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
                        // Le cercle correct est maintenant un "open_circle" (cercle vide avec portion ouverte)
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
        // Pour activer la transparence (alpha)
        imagesavealpha($img, true);
        // Fond de l'image
        $bg = imagecolorallocate($img, 0, 64, 82);
        imagefilledrectangle($img, 0, 0, $this->width, $this->height, $bg);

        // Ajout de bruit : tracé de nombreuses lignes aléatoires
        for ($i = 0; $i < 100; $i++) {
            $noiseColor = imagecolorallocatealpha($img, rand(200, 255), rand(200, 255), rand(200, 255), rand(80, 120));
            imageline($img, rand(0, $this->width), rand(0, $this->height), rand(0, $this->width), rand(0, $this->height), $noiseColor);
        }

        // Ajout de ronds remplis et transparents dispersés aléatoirement
        for ($i = 0; $i < 30; $i++) {
            $noiseCircleColor = imagecolorallocatealpha($img, rand(200, 255), rand(200, 255), rand(200, 255), rand(90, 110));
            $circleSize = rand(5, 20);
            $xPos = rand(0, $this->width);
            $yPos = rand(0, $this->height);
            imagefilledellipse($img, $xPos, $yPos, $circleSize, $circleSize, $noiseCircleColor);
        }

        // Couleur de premier plan pour les dessins principaux (blanc)
        $fg = imagecolorallocate($img, 255, 255, 255);
        // Dessin des formes captcha
        foreach ($this->positions as $p) {
            $x = $p['x'];
            $y = $p['y'];
            $sz = $p['size'];
            if ($p['shape'] === 'open_circle') {
                // Un cercle vide avec une petite portion ouverte : on trace un arc qui ne couvre pas 360°
                // Ici, on dessine l'arc de 15° à 345° (330° tracés, soit un écart de 30° non dessiné)
                imagearc($img, $x, $y, $sz, $sz, 15, 345, $fg);
            } else {
                // Un cercle vide complet : tracé de l'arc complet (de 0 à 360°)
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
}
?>