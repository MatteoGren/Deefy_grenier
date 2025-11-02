<?php
namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\{DefaultAction,
    AddPlaylistAction,
    AddPodcastTrackAction,
    AddUserAction,
    DisplayMyPlaylistsAction,
    SignInAction,
    SignOutAction,
    DisplayPlaylistAction};

class Dispatcher {

    private string $action;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void {
        switch ($this->action) {
            case 'display-playlist':
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
            case 'signin':
                $act = new SignInAction();
                break;
            case 'signout':
                $act = new SignOutAction();
                break;
            case 'my-playlists':
                $act = new DisplayMyPlaylistsAction();
                break;
            case 'init-db':
                include __DIR__ . '/../../../../SQL/db_init.php';
                break;
            case 'default':
            default:
                $act = new DefaultAction();
                break;
        }

        if (!isset($act)) {
            return;
        }

        $this->renderPage($act->getResult());
    }

    private function renderPage(string $html): void {
        $user = $_SESSION['user'] ?? null;



        // Menu dynamique selon la connexion
        if ($user) {
            $nav = <<<HTML
                <a href="?action=default">Accueil</a> |
                <a href="?action=init-db"> Initialiser la BD</a> |
                <span>Bienvenue {$user['email']}</span> |
                <a href="?action=signout">Déconnexion</a> |
                <a href="?action=add-playlist">Créer une playlist</a> |
                <a href="?action=add-track">ajouter une track</a> |
                <a href="?action=display-playlist">rechercher une playlist</a>|
                <a href='?action=my-playlists'>Mes playlists</a>
                
            HTML;
        } else {
            $nav = <<<HTML
                <a href="?action=default">Accueil</a> |
                <a href="?action=init-db"> Initialiser la BD</a> |
                <a href="?action=signin">Connexion</a> |
                <a href="?action=add-user">Inscription</a> |
                <a href="?action=add-playlist">Créer une playlist</a> |
                <a href="?action=add-track">ajouter une track</a> |
                <a href="?action=display-playlist">rechercher une playlist</a>|
                <a href='?action=my-playlists'>Mes playlists</a>
            HTML;
        }

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
                $nav
            </nav>
            <hr>
            $html
        </body>
        </html>
        PAGE;
    }
}
