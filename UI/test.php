<?php
  require "../vendor/autoload.php";
  $dotenv = Dotenv\Dotenv::createImmutable("../");
  $dotenv->load();

  echo "Username: ".$_ENV["DB_USER"];
  echo "<br>";
  echo "Password: ".$_ENV["DB_PASSWORD"];?>
