<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;

class DisplayPlaylistAction extends Action {

    protected function get(): string {
        if (empty($_SESSION['playlist'])) {
            return "<p>Aucune playlist n’est disponible pour le moment.</p>";
        }

        // Récupération de la playlist depuis la session
        $playlist = $_SESSION['playlist'];

        // Utilisation du renderer pour afficher proprement
        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();

        // Ajoute un lien pour retourner ou ajouter une piste
        $html .= '<p><a href="?action=add-track">Ajouter une piste</a></p>';

        return $html;
    }

    protected function post(): string {
        return $this->get();
    }
}
