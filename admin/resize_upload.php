<?php 
  /**
   * ลดขนาดรูปภาพก่อนดาวน์โหลดด้วย PHP
   *
   * @link https://appzstory.dev
   * @author Yothin Sapsamran (Jame AppzStory Studio)
   */  
    if(isset($_POST["submit"])) {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] != UPLOAD_ERR_OK) {
            alertAndRedirect("เกิดข้อผิดพลาดในการอัปโหลดไฟล์!");
        }

        $file = $_FILES['file']['tmp_name']; 
        $sourceProperties = getimagesize($file);

        if (!$sourceProperties) {
            alertAndRedirect("ไฟล์ที่อัปโหลดไม่ใช่รูปภาพที่ถูกต้อง!");
        }

        $fileNewName = time();
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $imageType = $sourceProperties[2];

        // สร้างภาพที่ถูกปรับขนาดแล้ว
        switch ($imageType) {
            case IMAGETYPE_PNG:
                $imageResourceId = imagecreatefrompng($file); 
                break;
            case IMAGETYPE_GIF:
                $imageResourceId = imagecreatefromgif($file); 
                break;
            case IMAGETYPE_JPEG:
                $imageResourceId = imagecreatefromjpeg($file); 
                break;
            default:
                alertAndRedirect("รูปภาพต้องเป็น PNG, GIF หรือ JPEG เท่านั้น!");
        }

        if (!$imageResourceId) {
            alertAndRedirect("ไม่สามารถสร้างภาพจากไฟล์ที่อัปโหลดได้!");
        }

        $targetLayer = imageResize($imageResourceId, $sourceProperties[0], $sourceProperties[1]);

        // ตั้งค่า Header สำหรับการดาวน์โหลด
        header('Content-Type: image/' . strtolower($ext));
        header('Content-Disposition: attachment; filename="' . $fileNewName . "_thump." . $ext . '"');

        switch ($imageType) {
            case IMAGETYPE_PNG:
                imagepng($targetLayer);
                break;
            case IMAGETYPE_GIF:
                imagegif($targetLayer);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($targetLayer);
                break;
        }

        imagedestroy($imageResourceId);
        imagedestroy($targetLayer);
        exit; 

    } else {
        alertAndRedirect("กรุณาเลือกไฟล์ก่อนดำเนินการ!");
    }

    function imageResize($imageResourceId, $width, $height) {
        $targetWidth = $width < 1280 ? $width : 1280 ;
        $targetHeight = ($height / $width) * $targetWidth;
        $targetLayer = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($targetLayer, $imageResourceId, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        return $targetLayer;
    }

    function alertAndRedirect($message) {
        echo "<script>
                alert('$message');
                window.history.back();
              </script>";
        exit;
    }
?>
