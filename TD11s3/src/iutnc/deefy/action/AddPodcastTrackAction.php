<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\audio\PodcastTrack;

class AddPodcastTrackAction extends Action {

    protected function get(): string {
        return <<<FORM
        <h2>Ajouter un track à la playlist</h2>
        <form method="post" action="?action=add-track">
            <label>Nom du track : <input type="text" name="track_name" required> </label>
            <label>Auteur : <input type="text" name="track_name" required> </label><br>
            <button type="submit">Ajouter</button>
        </form>
        FORM;
    }

    protected function post(): string
    {
        $rawName = filter_input(INPUT_POST, 'track_name', FILTER_UNSAFE_RAW);
        $trackName = trim(strip_tags($rawName));

        if (empty($trackName)) {
            return "<p>Erreur : le nom de la piste est vide.</p>" . $this->get();
        }

        if (!isset($_SESSION['playlist'])) {
            return "<p>Erreur : aucune playlist n'existe.</p>" . $this->get();
        }

        // Instancie une nouvelle PodcastTrack
        $track = new PodcastTrack($trackName, 0);
        $_SESSION['playlist']->addTrack($track);

        // Affiche la playlist avec AudioListRenderer
        $renderer = new AudioListRenderer($_SESSION['playlist']);
        $html = $renderer->render();
        $html .= '<p><a href="?action=add-track">Ajouter encore une piste</a></p>';

        return $html;
    }
}
