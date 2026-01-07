<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

    <title><?php echo $title ?? "Bookkeeping System"; ?></title>
    <link rel="shortcut icon" href="../images/logo.jpg" type="image/jpeg">
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $currentFile = basename($_SERVER['PHP_SELF']);
    if ($currentFile != "index.php") {
        $layout = "h-full grid grid-cols-[250px_1fr] grid-rows-[auto_1fr]";
    } else {
        $layout = "h-full bg-gray-100";
    }

    ?>

</head>

<body class="<?php echo $layout ?>">