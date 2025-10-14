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
            return "<p>Erreur le nom de la playlist n'est pas valide </p>" . $this->get();
        }

        $playlist = new PlayList($name);
        $_SESSION['playlist'] = $playlist;

        // Rendu HTML via le renderer
        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();

        // Ajout du lien pour ajouter une piste
        $html .= '<p><a href="?action=add-track">Ajouter une piste</a></p>';

        return $html;

    }
}
