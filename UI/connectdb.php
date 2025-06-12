<?php
require __DIR__.'\..\vendor\autoload.php';
  $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
  $dotenv->load();

  // build the DSN including SSL settings
  $conn = "mysql:";
  $conn .= "host=" . $_ENV["DB_HOST"];
  $conn .= ";port=" . $_ENV["DB_PORT"];
  $conn .= ";dbname=". $_ENV["DB_NAME"];
  $conn .= ";sslmode=verify-ca;sslrootcert=ca.pem";

  try {
    $pdo = new PDO($conn, $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }

?>
