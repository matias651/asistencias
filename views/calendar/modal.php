<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Example</title>
    <!-- Asegúrate de incluir las librerías necesarias -->
    <link rel="stylesheet" href="path/to/your/css/library.css">
    <script src="path/to/your/js/library.js"></script>
    <style>
        .modal {
            display: none; /* Oculta el modal por defecto */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4); /* Fondo oscuro */
        }
        .modal__content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<a href="javascript:;" data-toggle="modal" data-target="#basic-modal-preview" class="button inline-block bg-theme-1 text-white">Show Modal</a>

<div class="intro-y box">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
        <h2 class="font-medium text-base mr-auto">
            Blank Modal
        </h2>
        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
            <div class="mr-3">Show code</div>
            <input data-target="#blank-modal" class="show-code input input--switch border" type="checkbox">
        </div>
    </div>
    <div class="p-5" id="blank-modal">
        <div class="preview">
            <div class="text-center">
                <a href="javascript:;" data-toggle="modal" data-target="#basic-modal-preview" class="button inline-block bg-theme-1 text-white">Show Modal</a>
            </div>
            <div class="modal" id="basic-modal-preview">
                <div class="modal__content p-10 text-center">
                    <span class="close" data-dismiss="modal">&times;</span>
                    This is totally awesome blank modal!
                </div>
            </div>
        </div>
        <div class="source-code hidden">
            <button data-target="#copy-blank-modal" class="copy-code button button--sm border flex items-center text-gray-700">
                <i data-feather="file" class="w-4 h-4 mr-2"></i> Copy code
            </button>
           
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('[data-toggle="modal"]').forEach(function (element) {
        element.addEventListener('click', function () {
            var target = document.querySelector(this.getAttribute('data-target'));
            if (target) {
                target.style.display = 'block';
            }
        });
    });

    document.querySelectorAll('[data-dismiss="modal"]').forEach(function (element) {
        element.addEventListener('click', function () {
            var modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });

    window.addEventListener('click', function (event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });
</script>

</body>
</html>
