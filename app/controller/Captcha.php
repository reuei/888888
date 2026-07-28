<?php
namespace app\controller;

/**
 * 验证码控制器
 */
class Captcha extends BaseController
{
    /**
     * 生成数学验证码
     */
    public function generate()
    {
        $width = 120;
        $height = 40;

        $img = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($img, 240, 240, 240);
        imagefill($img, 0, 0, $bgColor);

        $num1 = mt_rand(1, 50);
        $num2 = mt_rand(1, 50);
        $operators = ['+', '-'];
        $op = $operators[array_rand($operators)];

        if ($op === '-') {
            if ($num1 < $num2) {
                list($num1, $num2) = [$num2, $num1];
            }
        }

        $answer = $op === '+' ? $num1 + $num2 : $num1 - $num2;
        $_SESSION['captcha_answer'] = $answer;

        $text = "{$num1} {$op} {$num2} = ?";

        $textColor = imagecolorallocate($img, 50, 50, 50);
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textX = ($width - $textWidth) / 2;
        $textY = ($height - imagefontheight($fontSize)) / 2;
        imagestring($img, $fontSize, $textX, $textY, $text, $textColor);

        for ($i = 0; $i < 3; $i++) {
            $lineColor = imagecolorallocate($img, mt_rand(100, 200), mt_rand(100, 200), mt_rand(100, 200));
            imageline($img, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
        }

        for ($i = 0; $i < 50; $i++) {
            $dotColor = imagecolorallocate($img, mt_rand(100, 200), mt_rand(100, 200), mt_rand(100, 200));
            imagesetpixel($img, mt_rand(0, $width), mt_rand(0, $height), $dotColor);
        }

        header('Content-Type: image/png');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        imagepng($img);
        imagedestroy($img);
        exit;
    }
}