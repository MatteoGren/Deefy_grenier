<?php

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\audio\AudioTrack;
use iutnc\deefy\audio\Playlist;

require_once 'vendor/autoload.php';

DeefyRepository::setConfig('db.config.ini');

// Récupérer l'instance
$r = DeefyRepository::getInstance();



//Test des fonction de la classe DeefyRepository


//Lister toutes les playlists
$playlists = $r->findAllPlaylists(); // retourne un tableau d'associatifs

echo "<h2>Playlists disponibles :</h2>";
foreach ($playlists as $pl){
    echo "Nom : " . htmlspecialchars($pl['nom']) . "<br>";
}


//Créer une nouvelle playlist
$newId = $r->savePlaylist("Ma playlist test");
echo "<p>Nouvelle playlist ajoutée avec ID = $newId</p>";


//Ajouter une piste à la playlist créée
$idTrack = $r->saveTrack("Nouvelle chanson", "rock", 240);
echo "Track créée avec l'ID $idTrack";


//Ajouter une piste existante à une playlist existante
$r->addTrackToPlaylist($newId, $idTrack);
echo "<p>Track $idTrack ajoutée à la playlist $newId</p>";



