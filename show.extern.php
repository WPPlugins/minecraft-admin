<?php
if ($_GET["output"] == "image") {
    header('Content-type: image/png');
    $im = imagecreatetruecolor(300, 300);
    $white = imagecolorallocate($im, 255, 255, 255);
    $grey = imagecolorallocate($im, 128, 128, 128);
    $black = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 299, 299, $white);
    $text = file_get_contents("../../minecraft.dir/servers/" . $_GET["blog"] . "_" . $_GET["server"] . "/" . $_GET["file"]);
    if (empty($text)) {
        $text = "No content...";
    }
    if (!is_file("/tmp/font.ttf")) {
        $font = file_get_contents("http://www.drivehq.com/file/df.aspx/shareID2129391/fileID59377155/arial.ttf");
        file_put_contents("/tmp/font.ttf", $font);
    }
    $font = "/tmp/font.ttf";
    imagettftext($im, 12, 0, 11, 21, $grey, $font, $text);
    imagettftext($im, 12, 0, 10, 20, $black, $font, $text);
    imagepng($im);
    imagedestroy($im);
}