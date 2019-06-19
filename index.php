<?php
/**
 * Created by PhpStorm.
 * User: Nero
 * Date: 2019/6/5
 * Time: 10:39
 */


$zip=new ZipArchive();
$zip_file_name_array = scandir('./old_zip');
foreach ($zip_file_name_array as $zip_file_name){
    if($zip_file_name == '.'||$zip_file_name == '..'){
        continue;
    }
    $zip_name =  explode("." ,$zip_file_name);
    $zip_name =  explode("_" ,$zip_name[0]);
    $zip_name = $zip_name[2];


    //解压压缩文件

    $zip_path = 'E:/wamp64/www/qr_code/old_zip/'.$zip_file_name;
    $ext_path = 'E:/wamp64/www/qr_code/img';
    dr_unZip($zip_path,$ext_path);

    //开始制作
    $file_name_array = scandir('./img');
    $name = $zip_name;

//    $name =  iconv('utf-8','gbk',$name);

    foreach ($file_name_array as $key=>$file_name){
        if($file_name == '.'||$file_name == '..'){
            continue;
        }

        $pic_name =  explode("." ,$file_name);
        $pic_name =  explode("_" ,$pic_name[0]);
        $pic_name = $pic_name[2];

        //打开图片
        $file = './img/'.$file_name;
        $text_1 = '桌号：'.$pic_name;
        $text_2 = '扫码点餐 加菜 结账';
        $font = './fonts/SIMYOU.TTF'; // 字体文件
        $font_size_1 = 36;
        $font_size_2 = 28;
        $width = 492;
        $myImage = ImageCreate($width, $width); //参数为宽度和高度
        $qr_img = imagecreatefrompng($file);
        $qr_img_with = imagesx($qr_img);
        $new_qr_img_w_h = 313;
        $img_x_y = ($width - $new_qr_img_w_h) / 2;

        $white = imagecolorallocate($myImage, 255, 255, 255);
        $font_color = imagecolorallocate($myImage, 0, 0, 0); // 文字颜色

        $fontBox = imagettfbbox($font_size_1, 0, $font, $text_1);//文字水平居中实质
        imagettftext($myImage, $font_size_1, 0, ceil(($width - $fontBox[2]) / 2), 80, $font_color, $font, $text_1);

        $fontBox = imagettfbbox($font_size_2, 0, $font, $text_2);
        imagettftext($myImage, $font_size_2, 0, ceil(($width - $fontBox[2]) / 2), 440, $font_color, $font, $text_2);

        imagecopyresampled($myImage, $qr_img, $img_x_y, $img_x_y, 0, 0, $new_qr_img_w_h, $new_qr_img_w_h, $qr_img_with, $qr_img_with); //重新组合图片并调整大小
        header("Content-Type:image/png");
        if(!is_dir('./'.$name)){
            mkdir('./'.$name,0777);
        }
        imagepng($myImage, './'.$name.'/'.$file_name);
        imagedestroy($myImage);
    }


    if($zip->open('./zip/'.$name.'.zip', ZipArchive::CREATE)=== TRUE) {
        addFileToZip('./' . $name . '/', $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
        $zip->close(); //关闭处理的zip文件
    }

    deldir('E:/wamp64/www/qr_code/img/');
    deldir('E:/wamp64/www/qr_code/'.$name.'/');
    rmdir('E:/wamp64/www/qr_code/'.$name);
}



//压缩一个目录
function addFileToZip($path,$zip){
    $handler=opendir($path); //打开当前文件夹由$path指定。
    while(($filename=readdir($handler))!==false){
        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                addFileToZip($path."/".$filename, $zip);
            }else{ //将文件加入zip对象
                $zip->addFile($path."/".$filename);
            }
        }
    }
    @closedir($path);
}

/**
 * 解压zip文件到指定目录
 * $filepath： 文件路径
 * $extractTo: 解压路径
 */
function dr_unZip($filepath,$extractTo) {
    $zip = new ZipArchive;
    $res = $zip->open($filepath);
    if ($res === TRUE) {
        //解压缩到$extractTo指定的文件夹
        $zip->extractTo($extractTo);
        $zip->close();
    } else {
        echo 'failed, code:' . $res;
    }
}



//清空文件夹函数和清空文件夹后删除空文件夹函数的处理
function deldir($path){
    //如果是目录则继续
    if(is_dir($path)){
        //扫描一个文件夹内的所有文件夹和文件并返回数组
        $p = scandir($path);
        foreach($p as $val){
            //排除目录中的.和..
            if($val !="." && $val !=".."){
                //如果是目录则递归子目录，继续操作
                if(is_dir($path.$val)){
                    //子目录中操作删除文件夹和文件
                    deldir($path.$val.'/');
                    //目录清空后删除空文件夹
                    @rmdir($path.$val.'/');
                }else{
                    //如果是文件直接删除
                    unlink($path.$val);
                }
            }
        }
    }
}












