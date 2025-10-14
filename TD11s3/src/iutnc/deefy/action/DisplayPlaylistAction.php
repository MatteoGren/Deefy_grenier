<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;

class DisplayPlaylistAction extends Action {

    protected function get(): string {
        if (empty($_SESSION['playlists'])) {
            return "<p>Aucune playlist n’est disponible pour le moment.</p>";
        }

        $html = "<h2>Vos Playlists</h2>";

        foreach ($_SESSION['playlists'] as $name => $playlist) {
            $renderer = new AudioListRenderer($playlist);
            $html .= "<div style='margin-bottom:20px; border:1px solid #ccc; padding:10px; border-radius:10px;'>";
            $html .= $renderer->render();
            $html .= "<p><a href='?action=add-track&playlist=" . urlencode($name) . "'>Ajouter une piste</a></p>";
            $html .= "</div>";
        }

        return $html;
    }

    protected function post(): string {
        return $this->get();
    }
}
