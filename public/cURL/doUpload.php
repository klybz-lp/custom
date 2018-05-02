<?php
header('content-type:text/html;charset=utf-8');
// print_r($_FILES);
$fileInfo=$_FILES['file'];
if($fileInfo['error']===0){
    if(move_uploaded_file($fileInfo['tmp_name'], 'uploads/'.$fileInfo['name'])){
        echo '上传成功';
    }else{
        exit('上传失败');
    }
}else{
    exit('有错误');
}
