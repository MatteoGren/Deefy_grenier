<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;

class Dispatcher {

    private string $action;

    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void {
        switch ($this->action) {
            case 'playlist':
                $act = new DisplayPlaylistAction();
                break;
            case 'add-playlist':
                $act = new AddPlaylistAction();
                break;
            case 'add-track':
                $act = new AddPodcastTrackAction();
                break;
            case 'default':
            default:
                $act = new DefaultAction();
                break;
        }

        $this->renderPage($act->getResult());
    }

    private function renderPage(string $html): void {
        echo <<<PAGE
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>DeefyApp</title>
        </head>
        <body>
            <nav>
                <a href="index.php?action=default">Accueil</a> |
                <a href="index.php?action=playlist">Voir la playlist</a> |
                <a href="index.php?action=add-playlist">Créer une playlist</a> |
                <a href="index.php?action=add-track">Ajouter un track</a>  
            </nav>
            <hr>
            $html
        </body>
        </html>
        PAGE;
    }
}
