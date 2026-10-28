<?php
$im = imagecreatefrompng('public/images/kokoh-kuat-terpercaya.png');
$w = imagesx($im);
$h = imagesy($im);

// Where is the red emblem located?
$redMinX = $w; $redMaxX = 0; $redMinY = $h; $redMaxY = 0;
for ($x = 0; $x < $w; $x++) {
    for ($y = 0; $y < $h; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $c = imagecolorsforindex($im, $rgb);
        if ($c['red'] > 200 && $c['green'] < 100 && $c['blue'] < 100 && $c['alpha'] < 50) {
            if ($x < $redMinX) $redMinX = $x;
            if ($x > $redMaxX) $redMaxX = $x;
            if ($y < $redMinY) $redMinY = $y;
            if ($y > $redMaxY) $redMaxY = $y;
        }
    }
}
echo "Red emblem bounds: minX=$redMinX, maxX=$redMaxX, minY=$redMinY, maxY=$redMaxY\n";

