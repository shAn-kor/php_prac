<?php

require_once __DIR__ . '/../autoload.php';

use Presentation\BoardController;

$controller = new BoardController();
$controller->handleRequest();