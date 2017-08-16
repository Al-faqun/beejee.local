<!DOCTYPE html>
<html>
<head>
    <title>Image resize client side</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/list.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
    <div id="form_div">
        <form action="test.php" method="post" id="upload_form" >
            <input type="file" id="input" name="image">
            <canvas id="canvas" width="320" height="240"></canvas>
            <input type="hidden" id="imageblob" name="imageblob" value="">
            <button type="button" onclick="preview()">Предпросмотр</button>
            <button type="button" onclick="upload()">Отослать</button>
        </form>
    </div>
    <script>
        function preview() {
            // select from an input element
            var file = document.getElementById('input').files[0];
            //check mime
            if (file.type.match(/image.(png|jpg|jpeg|gif)/)) {
                //get into img
                var img = document.createElement("img");
                var URL = window.URL || window.webkitURL;
                img.src = URL.createObjectURL(file);
                //be careful! resizing before loading of picture won't work on Chrome
                img.onload = function () {
                    //resize
                    var MAX_WIDTH = 320;
                    var MAX_HEIGHT = 240;
                    var width = img.width;
                    var height = img.height;
                    //resize so that BOTH dimensions always equals or less than max
                    //to do so, we need to know, which dim is bigger according to it's maximum
                    var a = width / MAX_WIDTH, b = height / MAX_HEIGHT;
                    if (a >= b) {
                        width = width/a;
                        height = height/a;
                    } else {
                        width = width/b;
                        height = height/b;
                    }
                    //draw in canvas
                    var canvas = document.getElementById('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    var ctx = canvas.getContext("2d"), dumpimg = new Image();
                    ctx.drawImage(img, 0, 0, width, height);
                };
            } else {
                alert('Прикрепленный файл должен быть картинкой: png, jpg, gif.')
            }
        }
        
        function upload() {
            preview();
            //save to hidden input
            var hiddenBlob = document.getElementById('imageblob');
            hiddenBlob.value = canvas.toDataURL("image/png");
            // select from an input element
            var file = document.getElementById('input').files[0];
            //check mime
            if (file.type.match(/image.(png|jpg|jpeg|gif)/)) {
                var form = document.getElementById("upload_form");
                form.submit();
            }
        }
    </script>
</body>
</html>
<?php
require dirname(__DIR__) . '/vendor/autoload.php';
var_dump($_FILES);
var_dump($_POST);
