<?php
  
  //单文件上传：
  $param = $_POST;
  $uploadFiles = $_FILES['file']; // file文件域名称
  $name = $param['name'];
  $package = $param['package'];
  $version = $param['version'];
  $version_code = $param['version_code'];

  if (empty($name) || empty($package) || empty($version) || empty($version_code)) {
    $return = [
      'flag' => false,
      'msg' => '参数错误'
    ];
    echo json_encode($return);
    exit;
  }

  // move_uploaded_file($uploadFiles['tmp_name'], './uploads/'.$uploadFiles['name']); // 保存到当前目录
  $return = [
      'flag' => true,
      'msg' => '',
      'data' => [
          'id' => uniqid(),
          'name' => $name,
          'package' => $package,
          'version' => $version,
          'version_code' => $version_code,
          'upload_file' => $uploadFiles
      ],
  ];
  echo json_encode($return);   // 输出整个文件变量
  exit;
?>