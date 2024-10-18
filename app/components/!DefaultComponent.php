<?php

namespace vehiclecheck\app\components;

global $app_vehiclecheck;

$app_vehiclecheck['stylesheets'][] = function () {
    ?>
    <style>
    </style>
    <?php
};

if (!function_exists('DefaultComponent')) {
    function DefaultComponent()
    {
        ?>
        <div>

        </div>
        <?php
    }
}

$app_vehiclecheck['scripts'][] = function () {
    ?>
    <script>
    </script>
    <?php
};