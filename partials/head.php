<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Encodage des caratères -->
    <meta charset="UTF-8">

    <!-- Assurer la compatibilité de l'affichage avec Internet Explorer -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mise en place du minimun de responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Titre affiché dans l'onglet associé à la page  -->
    <title>Cinema <?= isset($title) ? " - $title" : ""; ?></title>

    <!-- Chargement des balises meta utiles au SEO -->
    <meta name="robots" content="index, follow">
    <meta name="description" content="test">

    <!-- La favicon (image affichée dans l'onglet de la page) -->
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="mask-icon" href="favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">


    <!-- Lien de connexion à l'interface de google font pour récupérer la font family "Kalam" -->
    <link rel="preconnect" href="https://fonts.googleapis.com"> 
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> 
    <link href="https://fonts.googleapis.com/css2?family=Kalam&display=swap" rel="stylesheet">

    <!-- Line de la feuille de style -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>