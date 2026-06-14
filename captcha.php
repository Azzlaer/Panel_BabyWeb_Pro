<?php
require_once __DIR__ . '/includes/captcha.php';
$code = captcha_generate();
header('Content-Type: image/png');
$w = 170; $h = 54;
$img = imagecreatetruecolor($w, $h);
$bg = imagecolorallocate($img, 16, 18, 28);
$gold = imagecolorallocate($img, 232, 184, 91);
$red = imagecolorallocate($img, 120, 34, 30);
$line = imagecolorallocate($img, 70, 75, 95);
imagefilledrectangle($img, 0, 0, $w, $h, $bg);
for ($i=0; $i<8; $i++) imageline($img, random_int(0,$w), random_int(0,$h), random_int(0,$w), random_int(0,$h), $line);
for ($i=0; $i<80; $i++) imagesetpixel($img, random_int(0,$w-1), random_int(0,$h-1), random_int(80,180));
imagestring($img, 5, 34, 18, $code, $gold);
imagerectangle($img, 0, 0, $w-1, $h-1, $red);
imagepng($img);
imagedestroy($img);
