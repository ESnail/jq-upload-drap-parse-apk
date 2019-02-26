# ���� jq ʵ����ק�ϴ� APK �ļ���js���� APK ��Ϣ

### ����ջ

- jquery
- �ļ��ϴ���jquery.fileupload��[github �ĵ�](https://github.com/blueimp/jQuery-File-Upload/wiki/API)
- apk �ļ�������app-info-parser��[github �ĵ�](https://github.com/chenquincy/app-info-parser)

�ο���[ǰ�˽���ipa��apk��װ����Ϣ ���� app-info-parser](https://www.jianshu.com/p/db6347d95947)

### ֧�ֹ���

- �������ק�ϴ� apk �ļ�
- У���ļ����ͼ��ļ���С
- js ���� apk �ļ���Ϣչʾ��ͨ���ϴ��ӿ��ύ�����
- ֧���ϴ�������ȡ���ϴ�
- ֧���ϴ��ɹ���ʾ�ϴ���Ϣ
- ֧�ֽ������ϴ����Ѻ���ʾ
- ֧�ִ���ʷ��¼���������ϴ��ļ�����ѡ��һ��
- ֧�ּ��ļ��������� .txt �ļ���Ϊ .apk �ļ�
- �ϴ�����ʵʱ���£��ٷֱȣ�B/s
- ��ק������ק��ʱ��������ʾ

### demo Ԥ��
**˵��**

�����ϴ��ӿ���Ҫ��˽ӿڵ�֧�֣�����û���þ�̬ҳ��չʾ�����Ľ�������ˣ�������Ÿ�Ԥ��ͼ��

![demo](https://github.com/ESnail/jq-upload-drap-parse-apk/raw/master/screenshot/jq-upload-drap-parse-apk.gif)

Ϊ�˱��� gif ͼ̫��ֻ¼���˵���ϴ��ɹ���������������û¼�������������� demo �����˻�����ģ���ϴ��ӿ�ʵ�֡�demo ���� php ����ģ��ʵ�����ϴ��ӿڡ�

### �ѵ�

- js ���� APK �ļ���Ϣ
- ��ק�ϴ�������ϴ�����ק�ϴ��󶨵�һ��
- ���ϴ�֮ǰ��֪�� APK �ļ���Ϣ����Ҫִ���ϴ����������н��������ļ���Ϣ��Ϊ�����ŵ��ϴ��ӿ���
- �ϴ�������ȡ���ϴ�
- ���ļ�����������js ��ؿ���̨����

### ʵ��

**1. js ���� APK �ļ���Ϣ**

�������ģ��˽⵽ **APK** �ļ��ı��ʾ���һ��**ѹ����**�����а���һ��XML�ļ����ʲ������ļ���javascript ���� APK �ļ���Ϣ��Ҫ���ľ����Ƚ�ѹ��Ȼ���ȡ������ص��ļ������ܵõ��ļ���Ϣ�ˡ�

�ѵ��ڽ�ѹ�ϣ��ο��Ļ�������Ҫ���� node ��������������ά����ϵͳ�ǻ��� jquery �����ġ��������ղ�����
[ǰ�˽���ipa��apk��װ����Ϣ ���� app-info-parser](https://www.jianshu.com/p/db6347d95947) ���ĵķ������ܺõĽ�������⡣�ڴ˷ǳ���л�����ߡ�

```
 // apk �ļ�����
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
    
    // ʡ��������������...
}).catch(function (err) {
    uploadMod.doms.uploadErr.html('�ļ����������������ϴ�');
});
```

˵����
- ���� app-info-parser �ײ����� **async** �﷨���� IE ���ǲ����ݵġ��� firefox��chrome ���������ġ�
- �ϴ��� APK �ļ������ܴ���js �ű��ᱨ��`File format is not recognized.`��Ŀǰ�뵽�Ľ�������� js ��������Ȼ����д������и����뷨�ģ���ӭ@�ҡ��ڴ���ǰ��л��

```
// console.error() ��ش���
consoleError = window.console.error;
window.console.error = function () {
    consoleError && consoleError.apply(window, arguments);
    for (var info in arguments) {
        if (arguments[info] == 'File format is not recognized.') {
            $('#app_parse').html('<p style="color:red;">�������ϴ��˷������� APK �ļ������½ű�����������������ˢ��ҳ�棬�����������õ����飬����ԭ��</p>');
            setTimeout(function () {
                history.go(0);
            }, 3000);
            return false;
        }
    }
};
```
Ϊ�˱���ҳ���������󣬵��½ű��޷����У��������ҳ��ˢ�¡�

**2. ��ק�ϴ�������ϴ�����ק�ϴ��󶨵�һ��**

�����������ǰ���뵽��ק�ϴ��������� H5 ����ק���ܼ�ԭ�� js �� file �ļ��ϴ�ʵ�֣�����Ҫ������������⡣�����뵽ϵͳ���Ѿ������� **jquery.fileupload** �⣬�����صط������ĵ���֧����ק�ϴ�����˲��øÿ�ʵ����ק�ϴ����ܡ�

html �������£�
```
<div class="upload-area" id="upload_area">
    <i class="icon-upload"></i>
    <p class="upload-text">����װ����ק�����ϴ��� <em>ѡ���ļ�</em></p>
    <p class="upload-tip">֧�� APK �ļ�����󲻳��� 300 MB</p>
    <input type="file" id="upload_input" name="file" accept="application/vnd.android.package-archive" data-size="300"/>
</div>
```

��ν� **��ק�����** һ������һ���ϴ�����ʵ�֣������Ƿֿ���Ҫʵ��2�飿

�뷨�ǣ����������������� input ����¼���ǰ������Ҫʵ�� input ����¼���������ֹð���¼�����Ϊ���Ҳ�е���¼���

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
ʵ������ק���������Զ��ָ�����Ҫע����ǣ�`$('#upload_input')` �����û���ı���������ᵼ�¶��ε���ϴ�ʧЧ���޷�����������ļ����ڡ��Լ���ʱ��ק�ϴ�һ����ȷ���ļ����ᴥ�� 2 ���ļ��ϴ������� 2 ���ϴ��ӿڡ�����Ȥ�����ѿ����Լ��û������һ�¡�

�������֣�
- ����ٵ�����Ϊ�յ� apk �ļ�������ʾ���ļ��ߴ粻�ԡ�
- ��ʱ���ڶ��ε�����޷����� input �ĵ���¼������������Ȼ��Ч��
- ��ʱ��ͨ����ק�ϴ����ܹ�����ִ�У����ǻᴥ�� 2 ���ϴ��������� 2 ���ļ������� 2 ���ϴ��ӿ�����

**3. ���ϴ�֮ǰ��֪�� APK �ļ���Ϣ����Ҫִ���ϴ����������н��������ļ���Ϣ��Ϊ�����ŵ��ϴ��ӿ���**

֮ǰ�������ϴ��������ϴ�ǰ���Ѿ�֪�����ϴ�ʱ��Ҫ�ύ�Ķ������ֵ��

```
$('#upload_input').fileupload({
    url: 'http://localhost:80/jq-drag-upload-apk-parse/upload.php',
    dataType: 'json',
    formData: params, // params Ϊ js ��������Ҫ�ύ�Ĳ���
    multi: false,
    // ʡ��....
})
```

�����ڣ����ϴ�ǰ�ǲ�֪������ֵ�ģ���Ҫ��ִ���ϴ��������õ��ϴ��ļ���Ϣ�����������ϴ��ļ�����Ϣ��Ȼ�󽫽�����Ϣ��Ϊ����ֵ�ŵ��ϴ������С�����ô���أ��о��˺ܾã����ҵ���

```
$('#upload_input').fileupload({
    url: 'http://localhost:80/jq-drag-upload-apk-parse/upload.php',
    dataType: 'json',
    formData: params, // params Ϊ js ��������Ҫ�ύ�Ĳ���
    multi: false,
    add: function (e, data) {
        // ʡ���ļ����ͼ���СУ��
        // ʡ�� APK �ļ��������������ȵ� UI ��ʼ��
        
        $(e.target).fileupload(
            'option',
            'formData',
            formAppInfo // APK ������������
        );
        data.submit();
    },
    // ʡ��....
})
```

**4. �ϴ�������ȡ���ϴ�**

�����ԱȽ����ס������ϴ��ص��е� `data.abort()` ����ʵ�֡���Ҫ������ǣ��� **add()** ��������Ҫ������㻺��һ�� data���ŷ������ĵ��á�

```
$('#upload_input').fileupload({
    url: 'http://localhost:80/jq-drag-upload-apk-parse/upload.php',
    dataType: 'json',
    formData: params, // params Ϊ js ��������Ҫ�ύ�Ĳ���
    multi: false,
    add: function (e, data) {
        // ʡ���ļ����ͼ���СУ��
        // ʡ�� APK �ļ��������������ȵ� UI ��ʼ��
        
        // ��㻺�棬�����ȡ���ϴ�
        uploadMod.uploadXHR = data;
        
        $(e.target).fileupload(
            'option',
            'formData',
            formAppInfo // APK ������������
        );
        data.submit();
    },
    fail: function(e, data) {
        if (data.errorThrown == 'abort') {
            uploadMod.doms.uploadErr.html('��ȡ���ϴ����������ϴ�');
        } else {
            uploadMod.doms.uploadErr.html('�ϴ�ʧ�ܣ��������ϴ�');
        }
    },
    // ʡ��....
})
```

```
$('body').on('click', '#upload_cancel', function () {
    uploadMod.uploadXHR.abort();
})
```

**5. �ļ��ϴ�����Ҫ����**

```
fileCheck: function(e, data) {
    // �ļ���ʽ���ļ���СУ��
    var acceptFileTypes = uploadMod.doms.uploadInput.attr('accept');
    var maxSize = uploadMod.doms.uploadInput.data('size') * 1024 * 1024; // ��λmb����Ҫת��Ϊb
    var fileTypeFlag = data.originalFiles.every(function(item) {
        return acceptFileTypes.indexOf(item.type) > -1;
    });
    if (!fileTypeFlag) {
        uploadMod.doms.uploadErr.html('���ϴ� APK �ļ�');
        return false;
    }
    var fileSizeFlag = data.originalFiles.every(function(item) {
        return item.size > 0 && item.size <= maxSize;
    });
    if (!fileSizeFlag) {
        data = {};
        uploadMod.doms.uploadErr.html('�ļ���С����ȷ');
        return false;
    }
    
    uploadMod.doms.progressWrap.show();
    var $appParse = uploadMod.doms.progressWrap.find('.app-parse'),
        $progressCon = uploadMod.doms.progressWrap.find('.con');
    $appParse.show();
    $progressCon.hide();

    // apk �ļ�����
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

        // ��������ʼ��
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

        // �����ϴ��ӿڲ���
        uploadMod.uploadXHR = data;
        $(e.target).fileupload(
            'option',
            'formData',
            formAppInfo
        );
        data.submit();
    }).catch(function (err) {
        uploadMod.doms.progressWrap.hide();
        uploadMod.doms.uploadErr.html('�ļ����������������ϴ�');
        data.abort();
    });
    
    // console.error() ��ش���
    consoleError = window.console.error;
    window.console.error = function () {
        consoleError && consoleError.apply(window, arguments);
        for (var info in arguments) {
            if (arguments[info] == 'File format is not recognized.') {
                $('#app_parse').html('<p style="color:red;">�������ϴ��˷������� APK �ļ������½ű�����������������ˢ��ҳ�棬�����������õ����飬����ԭ��</p>');
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
        done: function(e, data) { // �ϴ��ɹ��ص�
            var result = data.result;
            if (result && result.flag && result.data) {
                uploadMod.doms.uploadErr.html(result.msg || '�ϴ��ɹ�');
                uploadMod.data.selectedAPK = result.data;
                uploadMod.methods.renderHistory(result.data);
            } else {
                uploadMod.doms.progressWrap.hide();
                uploadMod.doms.uploadErr.html(result.msg || '�ϴ�ʧ��');
            }
        },
        fail: function(e, data) {
            if (data.errorThrown == 'abort') {
                uploadMod.doms.uploadErr.html('��ȡ���ϴ����������ϴ�');
            } else {
                uploadMod.doms.uploadErr.html('�ϴ�ʧ�ܣ��������ϴ�');
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

### php �����򵥴
- ���� xampp ���ɻ��������а�װ
- �� demo ��Ŀ��ѹ��������װĿ¼�µ� **htdocs** ��Ŀ¼�£��ҵ�Ŀ¼�� `C:\xampp\htdocs\jq-drag-upload-apk-parse`
- ���� php �ϴ������ƣ���Ҫ���ļ�`C:\xampp\php\php.ini`����Ҫ�޸ĵĵ㣺
    - `max_execution_time = 0`��Ĭ�� 30 �룬0 Ϊ������
    - `post_max_size = 500M`��Ĭ�� 2M
    - `upload_max_filesize = 100M`��Ĭ�� 8M
    - ps���ο�[PHP�ϴ���С�����޸�](http://www.php.cn/php-weizijiaocheng-387028.html)
- �������װĿ¼�µ�(`C:\xampp`)�� **xampp.control.exe** �򿪽��棬�ڴ򿪽����У��� **Apache** ��Ӧ�� **Actions** ����
- ���������������`http://localhost/jq-drag-upload-apk-parse/index.html`
- ���������鿴 demo Ч��