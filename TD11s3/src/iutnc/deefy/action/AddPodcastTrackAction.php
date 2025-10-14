<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;

class AddPodcastTrackAction extends Action {

    protected function get(): string {
        return <<<HTML
        <h2>Ajouter une piste à la playlist</h2>
        <form method="post" enctype="multipart/form-data" action="?action=add-track">
            <label>Titre : <input type="text" name="track_name" required></label><br>
            <label>Auteur : <input type="text" name="author" required></label><br>
            <label>Durée (en secondes, optionnelle) : <input type="number" name="duration" min="0"></label><br>
            <label>Fichier audio (.mp3, optionnel) :
                <input type="file" name="userfile" accept=".mp3,audio/mpeg">
            </label><br><br>
            <button type="submit">Ajouter</button>
        </form>
        HTML;
    }

    protected function post(): string {
        if (!isset($_SESSION['playlist'])) {
            return "<p>Erreur : aucune playlist n’existe.</p>";
        }

        $trackName = filter_input(INPUT_POST, 'track_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($trackName) || empty($author)) {
            return "<p>Erreur : tous les champs obligatoires doivent être remplis.</p>" . $this->get();
        }

        // Récupère la durée (optionnelle) — sanitize + fallback à 10 si absent / invalide
        $durationRaw = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_INT);
        $duration = 10; // valeur par défaut
        if ($durationRaw !== null && $durationRaw !== false && $durationRaw !== '') {
            $duration = (int)$durationRaw;
            if ($duration < 0) { $duration = 10; }
        }

        $filePath = null;

        if (isset($_FILES['userfile']) && $_FILES['userfile']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['userfile'];
            $ext = strtolower(substr($file['name'], -4));

            if ($ext === '.mp3' && $file['type'] === 'audio/mpeg') {
                $targetDir = __DIR__ . '/../../../audio/';
                if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

                $targetFile = $targetDir . uniqid('track_', true) . '.mp3';
                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    $filePath = $targetFile;
                }
            }
            // si le fichier est invalide, on l'ignore (pas d'erreur bloquante)
        }

        // Création de la piste avec la durée fournie (ou la valeur par défaut)
        $track = new PodcastTrack($trackName, $author, $duration);

        // Si tu veux stocker le chemin du fichier dans l'objet, il faut ajouter une propriété/setter dans PodcastTrack.
        // Ex : $track->setFilePath($filePath);

        $_SESSION['playlist']->addTrack($track);

        $renderer = new AudioListRenderer($_SESSION['playlist']);
        $html = $renderer->render();
        $html .= '<p><a href="?action=add-track">Ajouter encore une piste</a></p>';

        return $html;
    }
}
