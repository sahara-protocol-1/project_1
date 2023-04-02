<?php

function dd($a) {
    echo "<pre>";
    var_dump($a);
    exit;
}


dd($_GET);
