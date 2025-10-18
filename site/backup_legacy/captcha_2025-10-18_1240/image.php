<?php
session_start();

$code = substr(strtoupper(bin2hex(random_bytes(3))), 0, 5);
$_SESSION['captcha_answer'] = $code;
session_write_close();

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$w = 160; $h = 50;
$im = imagecreatetruecolor($w, $h);

$bg  = imagecolorallocate($im, 245, 245, 245);
$fg  = imagecolorallocate($im,  20,  20,  20);
imagefilledrectangle($im, 0, 0, $w, $h, $bg);

for ($i = 0; $i < 6; $i++) {
    $c = imagecolorallocatealpha($im, rand(0,255), rand(0,255), rand(0,255), 80);
    imageline($im, rand(0,$w), rand(0,$h), rand(0,$w), rand(0,$h), $c);
}

$font = 5;
$text_w = imagefontwidth($font)  * strlen($code);
$text_h = imagefontheight($font) * 1;
$x = (int)(($w - $text_w) / 2);
$y = (int)(($h - $text_h) / 2);
imagestring($im, $font, $x, $y, $code, $fg);

imagepng($im);
imagedestroy($im);
exit;
