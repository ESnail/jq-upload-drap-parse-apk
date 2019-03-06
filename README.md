# 基于 jq 实现拖拽上传 APK 文件，js解析 APK 信息

### 技术栈

- jquery
- 文件上传：jquery.fileupload，[github 文档](https://github.com/blueimp/jQuery-File-Upload/wiki/API)
- apk 文件解析：app-info-parser，[github 文档](https://github.com/chenquincy/app-info-parser)

参考：[前端解析ipa、apk安装包信息 —— app-info-parser](https://www.jianshu.com/p/db6347d95947)

### 支持功能

- 点击或拖拽上传 apk 文件
- 校验文件类型及文件大小
- js 解析 apk 文件信息展示并通过上传接口提交给后端
- 支持上传过程中取消上传
- 支持上传成功显示上传信息
- 支持解析、上传等友好提示
- 支持从历史记录（所有已上传文件）中选择一个
- 支持假文件处理，比如 .txt 文件改为 .apk 文件
- 上传进度实时更新，百分比，B/s
- 拖拽进入拖拽区时，高亮显示

### demo 预览
**说明**

由于上传接口需要后端接口的支持，所以没法用静态页面展示完整的交互。因此，在这儿放个预览图。

![demo](https://github.com/ESnail/jq-upload-drap-parse-apk/blob/master/shortscreen/tree-select-newtree-loading.gif?raw=true)

为了避免 gif 图太大，只录屏了点击上传成功的情况。其他情况没录屏，可自行下载 demo ，搭建后端环境，模拟上传接口实现。demo 中用 php 语言模拟实现了上传接口。

### 难点

- js 解析 APK 文件信息
- 拖拽上传，点击上传和拖拽上传绑定到一起
- 在上传之前不知道 APK 文件信息，需要执行上传操作过程中将解析的文件信息作为参数放到上传接口中
- 上传过程中取消上传
- 假文件解析错误处理，js 监控控制台错误

### 实现

**1. js 解析 APK 文件信息**

经过查阅，了解到 **APK** 文件的本质就是一个**压缩包**，其中包含一堆XML文件，资产和类文件。javascript 解析 APK 文件信息，要做的就是先解压，然后读取其中相关的文件，就能得到文件信息了。

难点在解压上，参考的基本都需要借助 node 环境。由于现在维护的系统是基于 jquery 环境的。所以最终采用了
[前端解析ipa、apk安装包信息 —— app-info-parser](https://www.jianshu.com/p/db6347d95947) 该文的方案，很好的解决了问题。在此非常感谢该作者。

```
 // apk 文件解析
var parser = new AppInfoParser(data.files[0]);
parser.parse().then(function(result) {
    uploadMod.doms.uploadErr.html('');
    var appInfo = result.application || {};
    var formAppInfo = {
        name: appInfo.label ? (Array.isArray(appInfo.label) ? appInfo.label[0] : appInfo.label) : '',
        package: result.package,
        version: result.versionName,
        version_code: result.versionCode
    };
    
    // 省略其他操作代码...
}).catch(function (err) {
    uploadMod.doms.uploadErr.html('文件解析错误，请重新上传');
});
```

说明：
- 由于 app-info-parser 底层用了 **async** 语法，在 IE 下是不兼容的。在 firefox、chrome 下是正常的。
- 上传假 APK 文件，不能处理，js 脚本会报错：`File format is not recognized.`。目前想到的解决方案是 js 监听错误，然后进行处理。若有更好想法的，欢迎@我。在此提前感谢。

```
// console.error() 监控处理
consoleError = window.console.error;
window.console.error = function () {
    consoleError && consoleError.apply(window, arguments);
    for (var info in arguments) {
        if (arguments[info] == 'File format is not recognized.') {
            $('#app_parse').html('<p style="color:red;">由于您上传了非真正的 APK 文件，导致脚本解析出错，即将重新刷新页面，给您带来不好的体验，敬请原谅</p>');
            setTimeout(function () {
                history.go(0);
            }, 3000);
            return false;
        }
    }
};
```
为了避免页面其它错误，导致脚本无法运行，因此做了页面刷新。

**2. 拖拽上传，点击上传和拖拽上传绑定到一起**

在做这个功能前，想到拖拽上传可以利用 H5 的拖拽功能及原生 js 的 file 文件上传实现，但需要处理兼容性问题。后来想到系统中已经引入了 **jquery.fileupload** 库，于是特地翻阅了文档，支持拖拽上传。因此采用该库实现拖拽上传功能。

html 布局如下：
```
<div class="upload-area" id="upload_area">
    <i class="icon-upload"></i>
    <p class="upload-text">将安装包拖拽至此上传或 <em>选择文件</em></p>
    <p class="upload-tip">支持 APK 文件，最大不超过 300 MB</p>
    <input type="file" id="upload_input" name="file" accept="application/vnd.android.package-archive" data-size="300"/>
</div>
```

如何将 **拖拽、点击** 一起处理，用一个上传方法实现，而不是分开需要实现2遍？

想法是，点击外层容器，触发 input 点击事件。前提是需要实现 input 点击事件，并且阻止冒泡事件，因为外层也有点击事件。

```
$('body').on('click', '#upload_input', function (e) {
    e.stopPropagation();
    uploadMod.methods.fileUpload();
}).on('click drop dragenter dragover dragleave', '#upload_area', function(e) {
    e.preventDefault();
    uploadMod.doms.uploadErr.html('');
    
    switch (e.type) {
        case 'click':
            $('#upload_input').val(null);
            $('#upload_input').click();
            break;
        case 'drop':
            uploadMod.doms.uploadArea.removeClass('active');
            $('#upload_input').val(null);
            uploadMod.methods.fileUpload();
            break;
        case 'dragenter':
        case 'dragover':
            uploadMod.doms.uploadArea.addClass('active');
            break;
        case 'dragleave':
            uploadMod.doms.uploadArea.removeClass('active');
            break;
    }
})
```
实现了拖拽进入高亮、远离恢复。需要注意的是，`$('#upload_input')` 不能用缓存的变量。否则会导致二次点击上传失效，无法触发点击打开文件窗口。以及此时拖拽上传一个正确的文件，会触发 2 次文件上传。发送 2 次上传接口。感兴趣的朋友可以自己用缓存的试一下。

案例复现：
- 点击假的内容为空的 apk 文件，会提示：文件尺寸不对。
- 此时，第二次点击，无法触发 input 的点击事件。反复多次依然无效。
- 此时，通过拖拽上传，能够正常执行，但是会触发 2 次上传处理，解析 2 次文件，发送 2 次上传接口请求。

**3. 在上传之前不知道 APK 文件信息，需要执行上传操作过程中将解析的文件信息作为参数放到上传接口中**

之前做过的上传，是在上传前就已经知道在上传时需要提交的额外参数值。

```
$('#upload_input').fileupload({
    url: 'http://localhost:80/jq-drag-upload-apk-parse/upload.php',
    dataType: 'json',
    formData: params, // params 为 js 对象，是需要提交的参数
    multi: false,
    // 省略....
})
```

但现在，在上传前是不知道参数值的，需要在执行上传操作，拿到上传文件信息，并解析出上传文件的信息，然后将解析信息做为参数值放到上传请求中。那怎么做呢，研究了很久，才找到。

```
$('#upload_input').fileupload({
    url: 'http://localhost:80/jq-drag-upload-apk-parse/upload.php',
    dataType: 'json',
    formData: params, // params 为 js 对象，是需要提交的参数
    multi: false,
    add: function (e, data) {
        // 省略文件类型及大小校验
        // 省略 APK 文件解析及进度条等的 UI 初始化
        
        $(e.target).fileupload(
            'option',
            'formData',
            formAppInfo // APK 解析出的数据
        );
        data.submit();
    },
    // 省略....
})
```

**4. 上传过程中取消上传**

这个相对比较容易。利用上传回调中的 `data.abort()` 即可实现。需要处理的是，在 **add()** 方法里需要先在外层缓存一下 data，才方便对其的调用。

```
$('#upload_input').fileupload({
    url: 'http://localhost:80/jq-drag-upload-apk-parse/upload.php',
    dataType: 'json',
    formData: params, // params 为 js 对象，是需要提交的参数
    multi: false,
    add: function (e, data) {
        // 省略文件类型及大小校验
        // 省略 APK 文件解析及进度条等的 UI 初始化
        
        // 外层缓存，方便调取消上传
        uploadMod.uploadXHR = data;
        
        $(e.target).fileupload(
            'option',
            'formData',
            formAppInfo // APK 解析出的数据
        );
        data.submit();
    },
    fail: function(e, data) {
        if (data.errorThrown == 'abort') {
            uploadMod.doms.uploadErr.html('已取消上传，可重新上传');
        } else {
            uploadMod.doms.uploadErr.html('上传失败，请重新上传');
        }
    },
    // 省略....
})
```

```
$('body').on('click', '#upload_cancel', function () {
    uploadMod.uploadXHR.abort();
})
```

**5. 文件上传的主要代码**

```
fileCheck: function(e, data) {
    // 文件格式及文件大小校验
    var acceptFileTypes = uploadMod.doms.uploadInput.attr('accept');
    var maxSize = uploadMod.doms.uploadInput.data('size') * 1024 * 1024; // 单位mb，需要转换为b
    var fileTypeFlag = data.originalFiles.every(function(item) {
        return acceptFileTypes.indexOf(item.type) > -1;
    });
    if (!fileTypeFlag) {
        uploadMod.doms.uploadErr.html('请上传 APK 文件');
        return false;
    }
    var fileSizeFlag = data.originalFiles.every(function(item) {
        return item.size > 0 && item.size <= maxSize;
    });
    if (!fileSizeFlag) {
        data = {};
        uploadMod.doms.uploadErr.html('文件大小不正确');
        return false;
    }
    
    uploadMod.doms.progressWrap.show();
    var $appParse = uploadMod.doms.progressWrap.find('.app-parse'),
        $progressCon = uploadMod.doms.progressWrap.find('.con');
    $appParse.show();
    $progressCon.hide();

    // apk 文件解析
    var parser = new AppInfoParser(data.files[0]);
    parser.parse().then(function(result) {
        uploadMod.doms.uploadErr.html('');
        var appInfo = result.application || {};
        var formAppInfo = {
            name: appInfo.label ? (Array.isArray(appInfo.label) ? appInfo.label[0] : appInfo.label) : '',
            package: result.package,
            version: result.versionName,
            version_code: result.versionCode
        };

        // 进度条初始化
        $appParse.hide();
        $progressCon.show();
        if (result.icon) {
            uploadMod.doms.progressWrap.find('.icon-app').css('background-image', 'url("' + result.icon + '")');
        }
        uploadMod.doms.progressWrap.find('.name').html(formAppInfo.name);
        uploadMod.doms.progressWrap.find('.package').html(formAppInfo.package);
        uploadMod.doms.progressWrap.find('.version').html(formAppInfo.version);
        uploadMod.doms.progressWrap.find('.version-code').html(formAppInfo.version_code);
        uploadMod.doms.progressWrap.find('.progress').css('width', 0);
        uploadMod.doms.progressWrap.find('.num').html(0);
        uploadMod.doms.progressWrap.find('.size').html(0);

        // 设置上传接口参数
        uploadMod.uploadXHR = data;
        $(e.target).fileupload(
            'option',
            'formData',
            formAppInfo
        );
        data.submit();
    }).catch(function (err) {
        uploadMod.doms.progressWrap.hide();
        uploadMod.doms.uploadErr.html('文件解析错误，请重新上传');
        data.abort();
    });
    
    // console.error() 监控处理
    consoleError = window.console.error;
    window.console.error = function () {
        consoleError && consoleError.apply(window, arguments);
        for (var info in arguments) {
            if (arguments[info] == 'File format is not recognized.') {
                $('#app_parse').html('<p style="color:red;">由于您上传了非真正的 APK 文件，导致脚本解析出错，即将重新刷新页面，给您带来不好的体验，敬请原谅</p>');
                setTimeout(function () {
                    history.go(0);
                }, 3000);
                return false;
            }
        }
    };
},
fileUpload: function(el) {
    $('#upload_input').fileupload({
        url: 'http://localhost:80/jq-drag-upload-apk-parse/upload.php',
        dataType: 'json',
        multi: false,
        add: uploadMod.methods.fileCheck,
        paste: function () { return false; },
        done: function(e, data) { // 上传成功回调
            var result = data.result;
            if (result && result.flag && result.data) {
                uploadMod.doms.uploadErr.html(result.msg || '上传成功');
                uploadMod.data.selectedAPK = result.data;
                uploadMod.methods.renderHistory(result.data);
            } else {
                uploadMod.doms.progressWrap.hide();
                uploadMod.doms.uploadErr.html(result.msg || '上传失败');
            }
        },
        fail: function(e, data) {
            if (data.errorThrown == 'abort') {
                uploadMod.doms.uploadErr.html('已取消上传，可重新上传');
            } else {
                uploadMod.doms.uploadErr.html('上传失败，请重新上传');
            }
        },
        progressall: function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            uploadMod.doms.progressWrap.find('.progress').css('width', progress + '%');
            uploadMod.doms.progressWrap.find('.num').html(progress);
            uploadMod.doms.progressWrap.find('.size').html(bytesToSize(data.bitrate));

            function bytesToSize(bit) {
                if (bit === 0) return '0 B';
                var bytes = bit / 8;
                var k = 1024,
                    sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
                    i = Math.floor(Math.log(bytes) / Math.log(k));
             
               return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
            }
        }
    })
},
```

### php 环境简单搭建
- 下载 xampp 集成环境包进行安装
- 在 demo 项目解压拷贝到安装目录下的 **htdocs** 的目录下，我的目录是 `C:\xampp\htdocs\jq-drag-upload-apk-parse`
- 由于 php 上传有限制，需要改文件`C:\xampp\php\php.ini`，需要修改的点：
    - `max_execution_time = 0`，默认 30 秒，0 为无限制
    - `post_max_size = 500M`，默认 2M
    - `upload_max_filesize = 100M`，默认 8M
    - ps：参考[PHP上传大小限制修改](http://www.php.cn/php-weizijiaocheng-387028.html)
- 最后点击安装目录下的(`C:\xampp`)的 **xampp.control.exe** 打开界面，在打开界面中，将 **Apache** 对应的 **Actions** 开启
- 在浏览器窗口输入`http://localhost/jq-drag-upload-apk-parse/index.html`
- 即可完整查看 demo 效果
