<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\{DefaultAction, DisplayPlaylistAction, AddPlaylistAction, AddPodcastTrackAction, AddUserAction};

class Dispatcher {

    private string $action;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
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
            case 'add-user':
                $act = new AddUserAction();
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
            <style>
                body { font-family: Arial, sans-serif; margin: 2em; background: #fafafa; }
                nav a { margin-right: 1em; text-decoration: none; color: #0077cc; }
                nav a:hover { text-decoration: underline; }
            </style>
        </head>
        <body>
            <h1>🎧 DeefyApp</h1>
            <nav>
                <a href="?action=default">Accueil</a> |
                <a href="?action=add-user">Inscription</a> |
                <a href="?action=add-playlist">Créer une playlist</a> |
                <a href="?action=playlist">Voir la playlist</a>
            </nav>
            <hr>
            $html
        </body>
        </html>
        PAGE;
    }
}
