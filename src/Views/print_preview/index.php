<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script type="text/javascript" src="/assets/js/qr_code/jquery.min.js"></script>
    <script type="text/javascript" src="/assets/js/qr_code/qrcode.js"></script>
    <title>Impressão de Etiquetas</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                width: 100%;
            }

            .label {
                width: <?=$print->width?>mm;
                height: <?=$print->height?>mm;
                padding: <?=$print->spacing?>mm;
                border: 1px solid #000;
                page-break-inside: avoid;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .label .text {
                font-size: 12pt;
            }

            .no-print {
                display: none;
            }

            #qrcode {
                margin-left: auto;
            }
        }

        .hidden {
            display: none;
        }

        .label {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="label">
    <div class="text">
        <p>Produto: Vidro Temperado</p>
        <p>Dimensões: 30x40cm</p>
        <p>Data: 01/01/2023</p>
    </div>
    <div>
    <div style="width: 100%"><p>Data: 01/01/2023</p></div>
    <div id="qrcode" style="height:<?= $print->height-10 ?>mm;"></div>
    <div style="width: 100%"><p>Data: 01/01/2023</p></div>
    </div>
</div>

<button id="printButton" onclick="f_print()">
    Imprimir Etiqueta
</button>

<script>
    function f_print() {
        let btn = document.getElementById('printButton');
        btn.classList.add('hidden'); // Adiciona a classe 'hidden' para esconder o botão
        window.print();
        btn.classList.remove('hidden'); // Remove a classe 'hidden' para mostrar o botão novamente
    }

    var qrcode = new QRCode(document.getElementById("qrcode"), {
        width: 100,
        height: 80,
        useSVG: true
    });

    function makeCode() {
        qrcode.makeCode('<?= $print->name ?>');
    }

    makeCode();
</script>
</body>
</html>
