<?php
namespace Tool;
final class Verify
{
    private $width;
    private $height;
    private $picRes;

    public function __construct($width = 60, $height = 34){
        $this->width = $width;
        $this->height = $height;
        $this->picInit();
        $this->createPic();
        $this->writeCode();
        $this->writeNoise();
        $this->outPutPic();
    }

    private function picInit(){
        header("Content-Type:image/png");
    }

    private function createPic(){
        //创建画布
        $this->picRes = imagecreatetruecolor($this->width, $this->height);
        //给画布分配颜色
        $color = imagecolorallocate($this->picRes, rand(150, 255), rand(150, 255), rand(150, 255));
        //给画布上色
        imagefill($this->picRes, 0, 0, $color);
    }

    private function writeCode(){
        $fontDir = ROOT . "Resources" . DS . "Verify.ttf";
        $codeLib = "abcdefghjknmpqrstuvwxyzABCDEFGHIJKLNMPQRSTUVWXYZ123456789";
        $code = "";
        for ($i = 0; $i < 4; $i++) {
            //验证码分配颜色
            $color = imagecolorallocate($this->picRes, rand(50, 150), rand(50, 150), rand(50, 150));
            //生成随机单个验证码
            $oneCode = $codeLib[rand(0,strlen($codeLib)-1)];
            //绘制单个验证码
            imagettftext($this->picRes,18,rand(-10,10),2+($i*14),25,$color,$fontDir,$oneCode);
            //保存整个验证码并写入session
            $code .= $oneCode;
        }
        $_SESSION["verifyCode"] = $code;
    }

    private function writeNoise(){
        for($i = 0; $i < 4; $i++){
            $color = imagecolorallocate($this->picRes,rand(50, 150), rand(50, 150), rand(50, 150));
            imageline($this->picRes,rand(0,60),rand(0,34),rand(0,60),rand(0,34),$color);
        }
    }

    private function outPutPic(){
        imagepng($this->picRes);
    }
}