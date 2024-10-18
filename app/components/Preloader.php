<?php

namespace vehiclecheck\app\components;

use function vehiclecheck\service\php\utils\getAppFileUrl;

global $app_vehiclecheck;
global $_ENV;

$app_vehiclecheck['stylesheets'][] = function () {
    ?>
    <style>
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: #f7f7f7;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .preloader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .preloader .image-container {
            position: absolute;
            width: 300px;
            height: 300px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: opacity 0.5s, visibility 0.5s;
        }

        .preloader .image-container.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .preloader .image-ornament {
            position: absolute;
            width: 350px;
            height: 350px;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            animation: preloader-image-ornament-spin 10.8s infinite linear;
        }

        @keyframes preloader-image-ornament-spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg) scale(1);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg) scale(1);
            }
        }

        .preloader .image-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            margin-left: -50px;
            margin-top: -50px;
            z-index: 33;
            box-shadow: 0 0 10px black;
            border-radius: 20px;
            width: 100px;
        }

        .toast-container .toast {
            --bs-toast-bg: var(--bs-white);
        }

        .toast-container img {
            width: 32px;
            height: 32px;
        }

        #modal-operation-result {
            z-index: 1111;

            .smile {
                position: relative;
                height: 60px;
            }

            .face {
                position: absolute;
                width: 52px;
                height: 52px;
                background: #FCFCFC;
                border-radius: 50%;
                border: 1px solid #777777;
                left: calc(50% - 26px);
                z-index: 2;
            }

            .happy .face {
                animation: bounce .9s ease-in infinite;
            }

            .sad .face {
                animation: roll 3s ease-in-out infinite;
            }

            .eye {
                position: absolute;
                width: 5px;
                height: 5px;
                background: #777777;
                border-radius: 50%;
                top: 40%;
                left: 20%;
            }

            .right {
                left: 68%;
            }

            .mouth {
                position: absolute;
                top: 43%;
                left: 41%;
                width: 7px;
                height: 7px;
                border-radius: 50%;
            }

            .happy .mouth {
                border: 2px solid;
                border-color: transparent #777777 #777777 transparent;
                transform: rotate(45deg);
            }

            .sad .mouth {
                top: 49%;
                border: 2px solid;
                border-color: #777777 transparent transparent #777777;
                transform: rotate(45deg);
            }

            .shadow {
                position: absolute;
                width: 52px;
                height: 7.5px;
                opacity: 0.5;
                background: #777777;
                top: 48px;
                left: calc(50% - 26px);
                border-radius: 50%;
                z-index: 1;
            }

            .happy .shadow {
                animation: scale 1s ease-in infinite;
            }

            .sad .shadow {
                animation: move 3s ease-in-out infinite;
            }


        }

        @keyframes bounce {
            0% {
                transform: translateY(0) scaleX(1.1);
            }

            50% {
                transform: translateY(-10px) scaleY(1.1);
            }

            100% {
                transform: translateY(0) scaleX(1.1);
            }
        }

        @keyframes scale {
            50% {
                transform: scale(0.9);
            }
        }

        @keyframes roll {
            0% {
                transform: rotate(0deg);
                left: 35%;
            }

            50% {
                left: 60%;
                transform: rotate(168deg);
            }

            100% {
                transform: rotate(0deg);
                left: 35%;
            }
        }

        @keyframes move {
            0% {
                left: 35%;
            }

            50% {
                left: 60%;
            }

            100% {
                left: 35%;
            }
        }
    </style>
    <?php
};

if (!function_exists('Preloader')) {
    function Preloader()
    {
        ?>

        <div class="preloader">
            <div class="image-container">
                <img class="image-ornament" src="<?= getAppFileUrl('/public/images/company_ornament.png') ?>" alt="">
                <img class="image-icon" src="<?= getAppFileUrl('/public/images/company_icon_square.png') ?>" alt="">
            </div>
        </div>

        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="liveToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay='1000'>
                <div class="d-flex">
                    <div class="toast-body">Файлы успешно загружены</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-operation-result" tabindex="-1" aria-labelledby="operationResultModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header position-relative px-5 text-wrap">

                        <h1 class="modal-title fs-5 w-100 text-center text-break" id="operationResultModalLabel">
                        </h1>

                        <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">

                        <div class="smile">
                            <div class="face">
                                <div class="eye"></div>
                                <div class="eye right"></div>
                                <div class="mouth "></div>
                            </div>
                            <div class="shadow"></div>
                        </div>

                        <p class="text-body text-break">
                            <?= urldecode($_GET['operation_message']) ?>
                        </p>

                        <button type="button" class="btn btn-outline-secondary btn-alert">
                            Button
                        </button>


                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}

$app_vehiclecheck['scripts'][] = function () {
    ?>
    <script>
        if (!window.GLOBALS) {
            window.GLOBALS = {
                JS: {}
            };
        } else if (!window.GLOBALS.JS) {
            window.GLOBALS.JS = {};
        }

        function preloaderShow() {
            var preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.classList.remove('hidden');
            }

            var preloaderImageContainer = document.querySelector('.preloader .image-container');
            if (preloaderImageContainer) {
                preloaderImageContainer.classList.remove('hidden');
            }

        };

        function preloaderHide() {
            var preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.classList.add('hidden');
            }
        };

        const toastLiveExample = document.getElementById('liveToast');
        const toastBody = toastLiveExample.querySelector('.toast-body');

        function preloaderHideWithToast(message) {
            preloaderHide();
            toastBody.textContent = message;
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
            toastBootstrap.show();
        };

        const modalOperationResultElement = document.getElementById('modal-operation-result');
        const modalOperationResultTitleElement = modalOperationResultElement.querySelector('.modal-title');
        const modalOperationResultSmileElement = modalOperationResultElement.querySelector('.smile');
        const modalOperationResultBodyElement = modalOperationResultElement.querySelector('.text-body');
        const modalOperationResultButtonElement = modalOperationResultElement.querySelector('.btn-alert');

        const modalOperationResult = new bootstrap.Modal(modalOperationResultElement, {});

        var alert_callback_function = null;

        function preloaderHideWithAlert(status, title, message, callback_text = null, callback_function = null) {
            preloaderHide();

            modalOperationResultElement.classList.remove('bg-success-subtle', 'bg-danger-subtle');
            modalOperationResultTitleElement.classList.remove('text-danger', 'text-success');
            modalOperationResultSmileElement.classList.remove('sad', 'happy');

            switch (status) {
                case 'error':
                    modalOperationResultElement.classList.add('bg-danger-subtle');
                    modalOperationResultTitleElement.classList.add('text-danger');
                    modalOperationResultSmileElement.classList.add('sad');
                    break;
                case 'success':
                    modalOperationResultElement.classList.add('bg-success-subtle');
                    modalOperationResultTitleElement.classList.add('text-success');
                    modalOperationResultSmileElement.classList.add('happy');
                    break;
            }

            modalOperationResultTitleElement.textContent = title;
            modalOperationResultBodyElement.textContent = message;

            console.log(callback_text, callback_function);

            if( callback_text && callback_function ){
                modalOperationResultButtonElement.classList.remove('d-none');
                modalOperationResultButtonElement.textContent = callback_text;
                alert_callback_function = callback_function;
            }else{
                modalOperationResultButtonElement.classList.add('d-none');
                modalOperationResultButtonElement.textContent = "";
                alert_callback_function = null;
            }

            modalOperationResult.show();
        }

        modalOperationResultButtonElement.addEventListener('click', function () {
            if( alert_callback_function ){
                alert_callback_function();
            }
            modalOperationResult.hide();
        });

        window.GLOBALS.JS.preloaderShow = preloaderShow;
        window.GLOBALS.JS.preloaderHide = preloaderHide;
        window.GLOBALS.JS.preloaderHideWithToast = preloaderHideWithToast;
        window.GLOBALS.JS.preloaderHideWithAlert = preloaderHideWithAlert;
    </script>
    <?php
};
?>