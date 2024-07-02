<?php
// Configuração CORS
session_start();
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: *');
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');
date_default_timezone_set("America/Sao_Paulo");
