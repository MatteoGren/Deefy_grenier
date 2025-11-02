<?php
namespace iutnc\deefy\action;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\audio\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\auth\Authz;

class AddPodcastTrackAction extends Action
{
    protected function get(): string
    {

        // Vérifie que l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            return "<p>Vous devez être connecté pour voir vos playlists.</p>";
        }


        $repo = DeefyRepository::getInstance();

        // Récupère toutes les playlists pour l'affichage
        $playlists = $repo->findAllPlaylists();
        if (empty($playlists)) {
            return "<p>Aucune playlist disponible. Créez-en une d'abord.</p>";
        }

        $options = "";
        foreach ($playlists as $pl) {
            $options .= "<option value='" . htmlspecialchars($pl['id']) . "'>{$pl['nom']}</option>";
        }

        return <<<HTML
<h2>Ajouter une piste</h2>
<form method="post" enctype="multipart/form-data" action="?action=add-track">
    <label>Playlist : <select name="playlist_id" required>$options</select></label><br>
    <label>Titre : <input type="text" name="track_name" required></label><br>
    <label>Auteur : <input type="text" name="author" required></label><br>
    <label>Durée (en secondes) : <input type="number" name="duration" min="0"></label><br>
    <button type="submit">Ajouter</button>
</form>
HTML;
    }

    protected function post(): string
    {

        $playlistId = (int)($_POST['playlist_id'] ?? 0);

        //ligne de vérification du propriétaire
        try {
            Authz::checkPlaylistOwner($playlistId);
        } catch (AuthnException $e) {
            return "<p>Accès refusé : {$e->getMessage()}</p>";
        }

        $repo = DeefyRepository::getInstance();
        $trackName  = trim($_POST['track_name'] ?? '');
        $author     = trim($_POST['author'] ?? '');
        $duration   = (int)($_POST['duration'] ?? 10);

        // Création et ajout de la piste
        $track = new PodcastTrack($trackName, $author, $duration);
        $playlist = $repo->findPlaylistById($playlistId);
        $playlist->addTrack($track);

        $trackId = $repo->saveTrack($trackName, 'Podcast', $duration, $author);
        $repo->addTrackToPlaylist($playlistId, $trackId);

        $renderer = new AudioListRenderer($playlist);
        $html = "<p>Piste ajoutée avec succès !</p>" . $renderer->render();
        $html .= "<p><a href='?action=add-track'>Ajouter une autre piste</a></p>";

        return $html;
    }
}
