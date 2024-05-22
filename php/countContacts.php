<?php

include_once 'Controller.php';

$controller = new Controller();
echo json_encode($controller->getCountContacts());