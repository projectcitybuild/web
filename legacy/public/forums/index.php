<?php

$url = $_SERVER['REQUEST_URI'];
$query = parse_url($url, PHP_URL_QUERY);
$parameters = [];

parse_str($query, $parameters);

// topic parameter is required for
// redirection
if (array_key_exists('topic', $parameters) === false) {
    http_response_code(404);
    exit();
}

$topic = $parameters['topic'];
$matches = [];

if (preg_match('/^([0-9]+)$/', $topic, $matches)) {
    $topic = $matches[1].'.0';
} elseif (preg_match('/^([0-9]+)\.[0-9]+$/', $topic, $matches)) {
    $topic = $matches[1].'.0';
} elseif (preg_match('/^([0-9]+)\.msg[0-9]+$/', $topic, $matches)) {
    $topic = $topic;
} else {
    $topic = $topic;
}

header('location: https://forums.projectcitybuild.com/archive/index.php?topic='.$topic);
