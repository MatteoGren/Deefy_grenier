<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\Playlist;
use iutnc\deefy\render\AudioListRenderer;

class AddPlaylistAction extends Action {

    protected function get(): string {
        return <<<FORM
        <h2>Créer une nouvelle playlist</h2>
        <form method="post" action="?action=add-playlist">
            <label>Nom de la playlist :
                <input type="text" name="playlist_name" required>
            </label><br>
            <button type="submit">Créer</button>
        </form>
        FORM;
    }

    protected function post(): string {
        $rawName = filter_input(INPUT_POST, 'playlist_name', FILTER_UNSAFE_RAW);
        $name = trim(strip_tags($rawName));

        if (!preg_match('/^[\p{L}\p{N}\'\-\.\(\) ]+$/u', $name)) {
            return "<p>Erreur : le nom de la playlist n'est pas valide.</p>" . $this->get();
        }

        // Initialise le tableau des playlists si besoin
        if (!isset($_SESSION['playlists'])) {
            $_SESSION['playlists'] = [];
        }

        // Vérifie si la playlist existe déjà
        if (isset($_SESSION['playlists'][$name])) {
            return "<p>Une playlist du même nom existe déjà.</p>" . $this->get();
        }

        // Crée et ajoute la nouvelle playlist
        $playlist = new Playlist($name);
        $_SESSION['playlists'][$name] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();

        // Lien pour ajouter une piste à cette playlist
        $html .= "<p><a href='?action=add-track&playlist=" . urlencode($name) . "'>Ajouter une piste à cette playlist</a></p>";

        return $html;
    }
}
