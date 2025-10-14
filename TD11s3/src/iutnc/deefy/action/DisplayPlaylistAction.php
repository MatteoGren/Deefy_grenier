<?php

namespace iutnc\deefy\action;

class DisplayPlaylistAction extends Action {

    protected function get(): string {
        if (!isset($_SESSION['playlist']) || empty($_SESSION['playlist'])) {
            return "<p>Aucune playlist n’est disponible pour le moment.</p>";
        }

        $playlistName = $_SESSION['playlist_name'] ?? "Playlist sans nom";
        $playlist = $_SESSION['playlist'];

        $html = "<h2>Votre Playlist : {$playlistName}</h2><ul>";
        foreach ($playlist as $track) {
            $html .= "<li>{$track}</li>";
        }
        $html .= "</ul>";

        return $html;
    }

    protected function post(): string {
        return $this->get();
    }
}
