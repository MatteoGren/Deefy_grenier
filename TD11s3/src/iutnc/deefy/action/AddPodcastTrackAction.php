<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;

class AddPodcastTrackAction extends Action {

    protected function get(): string {
        if (empty($_SESSION['playlists'])) {
            return "<p>Aucune playlist disponible. <a href='?action=add-playlist'>Créez-en une</a> d’abord.</p>";
        }

        // Liste déroulante des playlists existantes
        $options = "";
        foreach ($_SESSION['playlists'] as $name => $p) {
            $options .= "<option value='" . htmlspecialchars($name) . "'>$name</option>";
        }

        return <<<HTML
        <h2>Ajouter une piste</h2>
        <form method="post" enctype="multipart/form-data" action="?action=add-track">
            <label>Playlist :
                <select name="playlist_name" required>$options</select>
            </label><br>
            <label>Titre : <input type="text" name="track_name" required></label><br>
            <label>Auteur : <input type="text" name="author" required></label><br>
            <label>Durée (en secondes) : <input type="number" name="duration" min="0"></label><br>
            <label>Fichier audio (.mp3, optionnel) :
                <input type="file" name="userfile" accept=".mp3,audio/mpeg">
            </label><br><br>
            <button type="submit">Ajouter</button>
        </form>
        HTML;
    }

    protected function post(): string {
        $playlistName = filter_input(INPUT_POST, 'playlist_name', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($_SESSION['playlists'][$playlistName])) {
            return "<p>Erreur : playlist introuvable.</p>";
        }

        $trackName = filter_input(INPUT_POST, 'track_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS);
        $durationRaw = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_INT);
        $duration = (!empty($durationRaw) && $durationRaw > 0) ? (int)$durationRaw : 10;

        $track = new PodcastTrack($trackName, $author, $duration);
        $_SESSION['playlists'][$playlistName]->addTrack($track);

        $renderer = new AudioListRenderer($_SESSION['playlists'][$playlistName]);
        $html = $renderer->render();
        $html .= "<p><a href='?action=add-track'>Ajouter une autre piste</a></p>";

        return $html;
    }
}
