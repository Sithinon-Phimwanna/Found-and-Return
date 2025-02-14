<?php 
  /**
   * ลดขนาดรูปภาพก่อนดาวน์โหลดด้วย PHP
   *
   * @link https://appzstory.dev
   * @author Yothin Sapsamran (Jame AppzStory Studio)
   */  
    if(isset($_POST["submit"]) && !$_FILES['file']['error']) {
        $file = $_FILES['file']['tmp_name']; 
        $sourceProperties = getimagesize($file);
        $fileNewName = time();
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $imageType = $sourceProperties[2];

        // สร้างภาพที่ถูกปรับขนาดแล้ว
        switch ($imageType) {

            case IMAGETYPE_PNG:
                $imageResourceId = imagecreatefrompng($file); 
                $targetLayer = imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                // ตั้งค่า Header สำหรับการดาวน์โหลดไฟล์ PNG
                header('Content-Type: image/png');
                header('Content-Disposition: attachment; filename="' . $fileNewName . "_thump." . $ext . '"');
                imagepng($targetLayer);
                break;

            case IMAGETYPE_GIF:
                $imageResourceId = imagecreatefromgif($file); 
                $targetLayer = imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                // ตั้งค่า Header สำหรับการดาวน์โหลดไฟล์ GIF
                header('Content-Type: image/gif');
                header('Content-Disposition: attachment; filename="' . $fileNewName . "_thump." . $ext . '"');
                imagegif($targetLayer);
                break;

            case IMAGETYPE_JPEG:
                $imageResourceId = imagecreatefromjpeg($file); 
                $targetLayer = imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1]);
                // ตั้งค่า Header สำหรับการดาวน์โหลดไฟล์ JPEG
                header('Content-Type: image/jpeg');
                header('Content-Disposition: attachment; filename="' . $fileNewName . "_thump." . $ext . '"');
                imagejpeg($targetLayer);
                break;

            default:
                echo "Invalid Image type.";
                exit;
        }

        exit; // หลังจากดาวน์โหลดไฟล์แล้ว จะหยุดการทำงาน
    } else {
        header("location: ./");
    }

    function imageResize($imageResourceId, $width, $height) {
        $targetWidth = $width < 1280 ? $width : 1280 ;
        $targetHeight = ($height / $width) * $targetWidth;
        $targetLayer = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($targetLayer, $imageResourceId, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        return $targetLayer;
    }

    function size_as_kb($size = 0) {
        if($size < 1048576) {
            $size_kb = round($size / 1024, 2);
            return "{$size_kb} KB";
        } else {
            $size_mb = round($size / 1048576, 2);
            return "{$size_mb} MB";
        }
    }

    function imgSize($img) {
        $targetWidth = $img[0] < 1280 ? $img[0] : 1280 ;
        $targetHeight = ($img[1] / $img[0]) * $targetWidth;
        return [round($targetWidth, 2), round($targetHeight, 2)];
    }

?>