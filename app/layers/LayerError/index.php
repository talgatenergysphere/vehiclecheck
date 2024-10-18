<?php

/**
 * --------------------------------------------------------------------------------------------------------------------
 */

namespace vehiclecheck\app\layers;

/**-------------------------------------------------------------------------------------------------------------------*/

defined('IS_DEV');

/**-------------------------------------------------------------------------------------------------------------------*/

if (!function_exists('LayerError')) {
    function LayerError($message)
    {
        $version = $_ENV['VHCL_APPLICATION_VERSION'] ?? '';

        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>
                Контроль автомобилей <?= $version . (IS_DEV ? "(dev)" : null) ?>
            </title>

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" />
        </head>

        <body>

            <div
                class="alert alert-danger"
                role="alert"
            >
                <h4 class="alert-heading">Контроль автомобилей <?= $version . (IS_DEV ? "(dev)" : null) ?></h4>
                <p>Ошибка ответа от сервера</p>
                <hr />
                <p class="mb-0"><?=$message?></p>
            </div>
            
            <?php if( !empty($_GET['ERROR']) ):?>
                <script>
                    // Удаляем гет параметр с ошибкой
                    const url = window.location.href;
                    const urlObj = new URL(url);
                    urlObj.searchParams.delete('ERROR');
                    const newUrl = urlObj.toString();
                    window.history.replaceState({ path: newUrl }, '', newUrl);
                </script>
            <?php endif;?>

        </body>
        <?php
    }
}