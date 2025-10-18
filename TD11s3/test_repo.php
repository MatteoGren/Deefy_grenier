<?php

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\audio\AudioTrack;
use iutnc\deefy\audio\Playlist;

require_once 'vendor/autoload.php';

DeefyRepository::setConfig('db.config.ini');

// Récupérer l'instance
$r = DeefyRepository::getInstance();

// 1. Lister toutes les playlists
$playlists = $r->findAllPlaylists(); // retourne un tableau d'associatifs

echo "<h2>Playlists disponibles :</h2>";
foreach ($playlists as $pl){
    echo "Nom : " . htmlspecialchars($pl['nom']) . "<br>";
}

// 2. Créer une nouvelle playlist
$newId = $r->savePlaylist("Ma playlist test");
echo "<p>Nouvelle playlist ajoutée avec ID = $newId</p>";

// 3. Ajouter une piste à la playlist créée
$idTrack = $r->saveTrack("Nouvelle chanson", "rock", 240);
echo "Track créée avec l'ID $idTrack";


