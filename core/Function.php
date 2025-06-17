<?php

// core/Function.php 

function sanitise_output($data)
{
    return htmlspecialchars((string) $data, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

function sanitise_input($data, $type, $min = null, $max = null)
{
    switch ($type) {
        case 'int':
            $result = filter_var($data, FILTER_VALIDATE_INT);
            return ($result === false) ? null : $result;
        case 'bool':
            return in_array($data, [0, 1, '0', '1'], true) ? (int) $data : null;
        case 'decimal':
            $result = filter_var($data, FILTER_VALIDATE_FLOAT);
            return ($result === false) ? null : $result;
        case 'text':
            $data = trim($data);
            $length = mb_strlen($data);
            if (($min !== null && $length < $min) || ($max !== null && $length > $max)) {
                return null;
            }
            return $data;
        default:
            return null;
    }
}

function sanitise_hex($hex) {
    $hex = trim($hex);

    if (!preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $hex)) {
        return null;
    }

    if (strlen($hex) === 4) {
        $hex = '#' . 
            str_repeat(substr($hex, 1, 1), 2) .
            str_repeat(substr($hex, 2, 1), 2) .
            str_repeat(substr($hex, 3, 1), 2);
    }

    return strtolower($hex);
}

function sanitise_onion_link($website_link)
{

    $website_link = preg_replace('~^(?:https?://)~i', '', $website_link);

    $parts = explode('/', $website_link, 2);
    $domain = $parts[0];

    $pattern = '/^([2-7a-z]{56})\.onion$/i';
    if (preg_match($pattern, $domain, $matches)) {
        return $matches[1];
    }

    return null;
}

function trim_text($text, $max_length, $encoding = 'UTF-8')
{
    if (mb_strlen($text, $encoding) <= $max_length) {
        return $text;
    }

    $trimmed = mb_substr($text, 0, $max_length, $encoding);

    $last_space = mb_strrpos($trimmed, ' ', 0, $encoding);

    if ($last_space !== false) {
        $trimmed = mb_substr($trimmed, 0, $last_space, $encoding);
    }

    if (mb_strlen($trimmed, $encoding) < mb_strlen($text, $encoding)) {
        $trimmed .= '...';
    }

    return $trimmed;
}

function sanitise_image($file, $max_size = 2097152, $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    if (!isset($file['size']) || $file['size'] > $max_size) {
        return null;
    }

    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        return null;
    }

    $mime_type = $image_info['mime'];
    if (!in_array($mime_type, $allowed_types)) {
        return null;
    }

    if (!class_exists('Imagick')) {
        return null;
    }

    try {
        $imagick = new Imagick();
        $imagick->readImage($file['tmp_name']);

        $isAnimated = ($imagick->getNumberImages() > 1);

        if ($isAnimated) {
            $imagick = $imagick->coalesceImages();

            foreach ($imagick as $frame) {
                $frame->stripImage();
                $frame->setImageCompressionQuality(35);
                $frame->setOption('webp:method', '3');
                $frame->setImageFormat('webp');
            }
            $imagick = $imagick->deconstructImages();
        } else {
            $imagick->stripImage();
            $imagick->setImageCompressionQuality(35);
            $imagick->setOption('webp:method', '3');
            $imagick->setImageFormat('webp');
        }

        $webp_data = $imagick->getImagesBlob();

        $imagick->clear();
        $imagick->destroy();

        return ['avatar.webp' => $webp_data];

    } catch (Exception $e) {
        error_log("Error treatment Image : " . $e->getMessage());
        return null;
    }
}

function sanitise_avatar($file, $max_size = 2097152, $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return null;
    }
    if (!isset($file['size']) || $file['size'] > $max_size) {
        return null;
    }
    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        return null;
    }
    $mime_type = $image_info['mime'];
    if (!in_array($mime_type, $allowed_types)) {
        return null;
    }
    if (!class_exists('Imagick')) {
        return null;
    }
    try {
        $imagick = new Imagick();
        $imagick->readImage($file['tmp_name']);
        $isAnimated = ($imagick->getNumberImages() > 1);
        if ($isAnimated) {
            $imagick = $imagick->coalesceImages();
        }
        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();
        if ($width <= 0 || $height <= 0) {
            $imagick->clear();
            $imagick->destroy();
            return null;
        }
        $min_dim = min($width, $height);
        $crop_x = (int) (($width - $min_dim) / 2);
        $crop_y = (int) (($height - $min_dim) / 2);
        $sizes = [512, 128];
        $output = [];
        foreach ($sizes as $target_size) {
            $imgClone = clone $imagick;
            foreach ($imgClone as $frame) {
                $frame->stripImage();
                $frame->cropImage($min_dim, $min_dim, $crop_x, $crop_y);
                $frame->thumbnailImage($target_size, $target_size);
                $frame->setImageCompressionQuality(35);
                $frame->setOption('webp:method', '3');
                $frame->setImageFormat('webp');
            }
            $imgClone = $imgClone->deconstructImages();
            $webp_data = $imgClone->getImagesBlob();
            if ($target_size === 512) {
                $output['avatar.webp'] = $webp_data;
            } elseif ($target_size === 128) {
                $output['avatar-min.webp'] = $webp_data;
            }
            $imgClone->clear();
            $imgClone->destroy();
        }
        $imagick->clear();
        $imagick->destroy();
        return $output;
    } catch (Exception $e) {
        error_log("Error treatment Avatar : " . $e->getMessage());
        return null;
    }
}

function generate_random_image($input)
{
    // Ensure WebP support exists
    if (!function_exists('imagewebp'))
        return null;

    // Seed the random generator based on input for reproducibility
    $seed = crc32($input);
    mt_srand($seed);

    // Create base image (512x512)
    $base_size = 512;
    $base = imagecreatetruecolor($base_size, $base_size);
    if (!$base)
        return null;

    // Generate a pastel background using deterministic random values
    $r = mt_rand(200, 255);
    $g = mt_rand(200, 255);
    $b = mt_rand(200, 255);
    $bg_color = imagecolorallocate($base, $r, $g, $b);
    imagefilledrectangle($base, 0, 0, $base_size, $base_size, $bg_color);

    // Draw 3 random circles on the base image
    for ($i = 0; $i < 3; $i++) {
        $circle_color = imagecolorallocate($base, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        $diameter = mt_rand($base_size / 4, $base_size / 2);
        $cx = mt_rand($diameter / 2, $base_size - $diameter / 2);
        $cy = mt_rand($diameter / 2, $base_size - $diameter / 2);
        imagefilledellipse($base, $cx, $cy, $diameter, $diameter, $circle_color);
    }

    // Generate 512x512 WebP image from the base image
    ob_start();
    imagewebp($base, null, 80);
    $webp_512 = ob_get_clean();

    // Create 128x128 version by resizing the same base image
    $small_size = 128;
    $small = imagecreatetruecolor($small_size, $small_size);
    imagecopyresampled($small, $base, 0, 0, 0, 0, $small_size, $small_size, $base_size, $base_size);

    ob_start();
    imagewebp($small, null, 80);
    $webp_128 = ob_get_clean();

    // Clean up resources
    imagedestroy($small);
    imagedestroy($base);

    return [
        'avatar.webp' => $webp_512,
        'avatar-min.webp' => $webp_128
    ];
}

function display_messages(array $errors = [], array $successes = []): string
{
    $output = '';

    if (!empty($errors)) {
        $output .= '<div class="errors">' . PHP_EOL;
        $output .= '    <ul>' . PHP_EOL;
        foreach ($errors as $error) {
            if (is_array($error)) {
                foreach ($error as $sub_error) {
                    $output .= '        <li>' . sanitise_output($sub_error) . '</li>' . PHP_EOL;
                }
            } else {
                $output .= '        <li>' . sanitise_output($error) . '</li>' . PHP_EOL;
            }
        }
        $output .= '    </ul>' . PHP_EOL;
        $output .= '</div>' . PHP_EOL;
    }
    if (!empty($successes)) {
        $output .= '<div class="successes">' . PHP_EOL;
        $output .= '    <ul>' . PHP_EOL;
        foreach ($successes as $success) {
            if (is_array($success)) {
                foreach ($success as $sub_success) {
                    $output .= '        <li>' . sanitise_output($sub_success) . '</li>' . PHP_EOL;
                }
            } else {
                $output .= '        <li>' . sanitise_output($success) . '</li>' . PHP_EOL;
            }
        }
        $output .= '    </ul>' . PHP_EOL;
        $output .= '</div>' . PHP_EOL;
    }

    return $output;
}

function build_pagination_url($page)
{
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

function anti_injection_sql()
{
    $injection = 'INSERT|UNION|SELECT|NULL|COUNT|FROM|LIKE|DROP|TABLE|WHERE|COUNT|COLUMN|TABLES|INFORMATION_SCHEMA|OR|UPDATE|TRUNCATE|DELETE';
    foreach ($_GET as $get_searchs) {
        $get_search = explode(" ", $get_searchs);
        foreach ($get_search as $k => $v) {
            if (in_array(strtoupper(trim($v)), explode('|', $injection))) {
                die();
            }
        }
    }
}

function remove_directory($directory)
{
    if (!file_exists($directory)) { return true; }

    if (!is_dir($directory)) {
        return unlink($directory);
    }

    foreach (scandir($directory) as $item) {

        if ($item === '.' || $item === '..') {
            continue;
        }

        if (!remove_directory($directory . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }

    return rmdir($directory);
    
}

