<?php
include '../vendor/autoload.php';

$id = !empty($_GET['id']) ? $_GET['id'] : null;
$progress = new \Zend\ProgressBar\Upload\SessionProgress();
$data = new \Zend\View\Model\JsonModel($progress->getProgress($id));

echo $data->serialize();