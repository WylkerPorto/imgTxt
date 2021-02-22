<?php
$erro = false;
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['texto'], $_FILES['imagem'], $_POST['cor'])) {
        if ($_FILES['imagem']['error']) {
            $erro = '<div id="erro">Erro ao subir imagem</div>';
            return ;
        } else {
            $img = imagecreatefromjpeg($_FILES['imagem']['tmp_name']); // criar imagem
            $fontColor = imagecolorallocate($img, 0x00, 0x00, 0x00); // cor do texto preto
            if ($_POST['cor'] == 1) {
                $fontColor = imagecolorallocate($img, 0xFF, 0xFF, 0xFF); // cor do texto branco
            }
            
            $font = 4;
            $h = imagefontheight($font);
            $fw = imagefontwidth($font);
            list($width) = getimagesize($_FILES['imagem']['tmp_name']);
            // var_dump($width);
            $txt = explode("\n", wordwrap($_POST['texto'], ($width / $fw) - 2, "\n"));
            // var_dump($txt);
            $y = 10;
            
            foreach ($txt as $text) {
                $x = 10;
                imagestring($img, $font, $x, $y, $text, $fontColor);
                $y += ($h + $font);
            }
            
            $file = 'down.jpg'; // nome do ficheiro
            header( 'Content-type: image/jpeg' );
            ImagePng ($img);
            ImageDestroy($img);
        }
    }
    else {
        $erro = '<div id="erro">Erro ao enviar imagem favor verificar se a imagem é do tipo jpeg/jpg</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image and Text Mixer</title>
<style type="text/css">
    * {
        margin: 0;
        padding: 0;
    }

    body {
        height: 100vh;
        width: 100vw;
        display: flex;
        flex-direction: column;
    }

    #erro {
        color: #ff0000;
        font-size: 20px;
        font-weight: 300;
        background: #ffc5c5;
        padding: 10px;
        margin-bottom: 15px;
    }

    #form {
        display: inline-flex;
        flex-direction: column;
        width: inherit;
        max-width: 500px;
        margin: 0 auto 15px;
        text-align: center;
    }

    #title {
        font-size: 30px;
        display: block;
        margin-bottom: 15px;
    }

    form > label {
        border: 1px solid #23125d;
        background: #918be8;
        padding: 10px 0;
        border-radius: 5px;
        font-size: 20px;
        color: #ffffff;
    }

    form > label:hover {
        border: 1px solid #918be8;
        background: #23125d;
        padding: 10px 0;
        border-radius: 5px;
        font-size: 20px;
        color: #ffffff;
    }

    #file {
        display: none;
    }

    #name {
        font-size: 20px;
        margin: 5px 0 15px;
        font-weight: 300;
        font-style: italic;
    }

    textarea {
        resize: none;
        height: 150px;
        margin-bottom: 15px;
        font-size: 15px;
        padding: 5px;
        border-radius: 5px;
    }

    button {
        border: 1px solid #bfbfbf;
        background: #dedede;
        padding: 10px 0px;
        border-radius: 5px;
        font-size: 20px;
        color: #000000;
    }
    
    button:hover {
        border: 1px solid #bfbfbf;
        background: #dedede;
        padding: 10px 0px;
        border-radius: 5px;
        font-size: 20px;
        color: #000000;
    }

    button[type="submit"] {
        border: 1px solid #2b8c37;
        background: #26bb23;
        padding: 10px 0px;
        border-radius: 5px;
        font-size: 20px;
        color: #ffffff;
    }
    
    button[type="submit"]:hover {
        border: 1px solid #26bb23;
        background: #2b8c37;
        padding: 10px 0px;
        border-radius: 5px;
        font-size: 20px;
        color: #ffffff;
    }

    #prevImg {
        width: 100%;
        height: auto;
        max-width: 500px;
        margin: 0 auto;
        border: 1px solid #ff0000;
    }

    #prev {
        text-align: center;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    #preview {
        margin: 0 auto;
        position: relative;
    }

    #preview span {
        display: block;
        position: absolute;
        color: #000000;
        top: 10px;
        left: 10px;
        overflow-wrap: anywhere;
        font-family: monospace;
        font-size: 14px;
    }

    form > div {
        margin-bottom: 30px;
        justify-content: space-between;
        display: flex;
        font-size: 20px;
    }

    form > div input {
        margin-right: 5px;
    }

    .white {
        color: #ffffff !important;
    }

    .black {
        color: #000000 !important;
    }
</style>
</head>
<body>
<?= $erro ? $erro : null; ?>
    <form method="POST" enctype="multipart/form-data" id="form">
        <span id="title">Image and Text Mixer</span>
        <label for="file">Upload</label>
        <input type="file" name="imagem" accept="image/jpeg, image/jpg" id="file" required>
        <span id="name"></span>
        <textarea name="texto" placeholder="texto" id="texto" required disabled></textarea>
        <div>
            <span>Cor da Fonte</span>
            <label class="container" for="preto">
                <input type="radio" name="cor" id="preto" value="0" checked="checked">Preto
            </label>

            <label class="container" for="branco">
                <input type="radio" name="cor" id="branco" value="1">Branco
            </label>
        </div>
        <button type="button" id="btn">Submit</button>
    </form>
    <span id="prev">Preview <br><small>(a relação fonte x imagem pode variar no resultado pós processamento)</small></span>
    <div id="preview"></div>
</body>
<script
      src="https://code.jquery.com/jquery-3.2.1.slim.js"
      integrity="sha256-tA8y0XqiwnpwmOIl3SGAcFl2RvxHjA8qp0+1uCGmRmg="
      crossorigin="anonymous"
    ></script>
<script>
$(function () {
    function filePreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#prevImg').remove();
                $('#prevTxt').remove();
                $('#preview').append('<span id="prevTxt"></span>');
                $('#prevTxt').after('<img src="'+ e.target.result +'" id="prevImg"/>');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#file').change(function () {
        let ext = this.value.split('.').pop()
        if (ext === 'jpg') {
            $('#name').textContent = this.value;
            $('#btn').attr('type', 'submit');
            $('#texto').attr('disabled', false);
            filePreview(this);
        } else {
            $('#btn').attr('type', 'button');
            $('#texto').attr('disabled', true);
            $('#texto').val('');
            let erro = document.createElement("div");
            let msg = document.createTextNode('Erro, favor verificar se a imagem é do tipo jpeg/jpg')
            erro.id = 'erro';
            erro.append(msg);
            $('#name').append(erro);
            $('#prevTxt').remove();
            $('#prevImg').remove();
        } 
    });

    $('#texto').on('input', function () {
        if ($('#prevTxt')) {
            $('#prevTxt').text(this.value);
        }
    });

    $('#preto').change( function() {
        if ($('#prevTxt')) {
            $('#prevTxt').removeClass('white').addClass('black');
        }
    });

    $('#branco').change( function() {
        if ($('#prevTxt')) {
            $('#prevTxt').removeClass('black').addClass('white');
        }
    });
});
</script>
</html>