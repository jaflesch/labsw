<?php

$config = new stdclass();

$config->db = new stdclass();
$config->db->host = "localhost";
$config->db->user = "root";
$config->db->pass = "";
$config->db->name = "eeefis";

$config->news = new stdclass();
$config->news->rows = 10;

$config->debug = true;
$config->session_name = "site";
$config->default_controller = "home";
$config->default_action = "index";

$config->base_path = dirname(__FILE__);

$config->main_host = "localhost/";

$config->email_contato = "contato@localhost.com.br";

$config->default_idioma = "PT";

$config->recaptcha = new stdclass();
$config->recaptcha->private_key = "6LdDPP0SAAAAADD5OdRaY9enySh3w_FMCjRQXff4";
$config->recaptcha->public_key = "6LdDPP0SAAAAAM8p6_pUNcoq1vHc9ywj0h91-7AQ";

// Twig paths
$twig = new stdclass();
$twig->root = "/eeefis";
$twig->css = $twig->root.'/assets/css';
$twig->js = $twig->root.'/assets/js';
$twig->img = $twig->root.'/assets/img';
$twig->font = $twig->root.'/assets/font';

// Metadata
$metadata = new stdclass();
$metadata->app = "Eco Produtiva";
$metadata->keywords = "Ozônio, tratamento, água, efluente, reuso, Redução de Custos, Ozonização Catalítica";
$metadata->description = "Com a tecnologia da Ozonização Catalítica Avançada da EcoProdutiva, é possível tratar águas e efluentes sem gerar lodo e sem usar produtos químicos.";

// Timezone
date_default_timezone_set('America/Sao_Paulo');


// EEEFis constants (edit that file!)
require_once("data/_macros.php");