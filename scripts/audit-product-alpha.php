<?php

$relativeDirectory = $argv[1] ?? 'public/images/product';
$directory = realpath(__DIR__.'/../'.$relativeDirectory);

if ($directory === false) {
    throw new RuntimeException("Direktori tidak ditemukan: {$relativeDirectory}");
}

$files = glob($directory.'/*') ?: [];
sort($files);

foreach ($files as $file) {
    $image = @imagecreatefromstring((string) file_get_contents($file));

    if ($image === false) {
        printf("%s|unknown|unsupported\n", basename($file));

        continue;
    }

    $width = imagesx($image);
    $height = imagesy($image);
    $stepX = max(1, (int) floor($width / 80));
    $stepY = max(1, (int) floor($height / 80));
    $hasTransparency = false;

    for ($y = 0; $y < $height && ! $hasTransparency; $y += $stepY) {
        for ($x = 0; $x < $width; $x += $stepX) {
            $alpha = (imagecolorat($image, $x, $y) >> 24) & 0x7F;

            if ($alpha > 0) {
                $hasTransparency = true;
                break;
            }
        }
    }

    printf(
        "%s|%dx%d|%s\n",
        basename($file),
        $width,
        $height,
        $hasTransparency ? 'alpha' : 'opaque',
    );

    imagedestroy($image);
}
