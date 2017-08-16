function previewTask(previewClass, fileInputID, imgFromCanvasID, previewImgID, formID, emailID, textID, hiddenBlobID) {
    //this callback is executed after asynch call inside previewImage()
    var after = function (storageImg) {
        //create preview of post
        var preview = document.getElementsByClassName(previewClass)[0];
        //copy resized image to preview
        var imgPreview = document.getElementById(previewImgID);
        imgPreview.src = storageImg.src;
        //copy email and text of post
        var emailValue = document.getElementById(emailID).value;
        var textValue = document.getElementById(textID).value;
        //replace dummy values
        preview.innerHTML = preview.innerHTML.replace('%js_replace_email%', emailValue);
        preview.innerHTML = preview.innerHTML.replace('%js_replace_text%', textValue);
        //make form invisible
        var form = document.getElementById(formID);
        form.style.display = 'none';
        //display preview
        preview.style.display = 'block';
    };
    //load preview of picture
    previewImage(fileInputID, imgFromCanvasID, hiddenBlobID, after);

}

function previewImage(fileInputID, imgFromCanvasID, hiddenBlobID, after) {
    // select from an input element
    var file = document.getElementById(fileInputID).files[0];
    if (file === undefined) {
        alert('Прикрепленный файл должен быть картинкой: png, jpg, gif.')
    } else {
        //check mime
        if (file.type.match(/image.(png|jpg|jpeg|gif)/)) {
            //get into img
            var ret;
            var tempImg = document.createElement('img');
            var storageImg = document.getElementById(imgFromCanvasID);
            var URL = window.URL || window.webkitURL;
            tempImg.src = URL.createObjectURL(file);
            tempImg.onload = function () {
                //resize
                var MAX_WIDTH = 320;
                var MAX_HEIGHT = 240;
                var width = tempImg.width;
                var height = tempImg.height;
                //resize so that BOTH dimensions always equals or less than max
                //to do so, we need to know, which dim is bigger according to it's maximum
                var a = width / MAX_WIDTH, b = height / MAX_HEIGHT;
                if (a >= b) {
                    width = width / a;
                    height = height / a;
                } else {
                    width = width / b;
                    height = height / b;
                }
                //draw in canvas
                var canvas = document.createElement("canvas");
                //class for later replace
                canvas.width = width;
                canvas.height = height;
                var ctx = canvas.getContext("2d");
                ctx.drawImage(tempImg, 0, 0, width, height);
                var imageBase64 = canvas.toDataURL("image/png");
                var hiddenBlob = document.getElementById(hiddenBlobID);
                storageImg.src = imageBase64;
                hiddenBlob.value = imageBase64;
                after(storageImg);
            };

        } else {
            alert('Прикрепленный файл должен быть картинкой: png, jpg, gif.')
        }
    }
}

function closePreview(previewClass, formID) {
    var preview = document.getElementsByClassName(previewClass)[0];
    var form = document.getElementById(formID);
    form.style.display = 'block';
    //display preview
    preview.style.display = 'none';
}

function upload(formID, fileInputID, imgFromCanvasID, hiddenBlobID) {
    //this callback is executed after asynch call inside previewImage()
    var after = function (storageImg) {
        // select from an input element
        var file = document.getElementById(fileInputID).files[0];
        //check mime
        if (file !== undefined) {
            if (file.type.match(/image.(png|jpg|jpeg|gif)/)) {
                var form = document.getElementById(formID);
                form.submit();
            }
        }
    };
    previewImage(fileInputID, imgFromCanvasID, hiddenBlobID, after);
}