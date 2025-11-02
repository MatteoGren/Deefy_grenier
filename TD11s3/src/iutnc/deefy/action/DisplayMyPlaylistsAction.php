<?php
namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class DisplayMyPlaylistsAction extends Action
{
    protected function get(): string
    {
        // Vérifie que l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            return "<p>Vous devez être connecté pour voir vos playlists.</p>";
        }

        $user = $_SESSION['user'];
        $userId = is_object($user) ? $user->getId() : $user['id'];

        $repo = DeefyRepository::getInstance();
        $playlists = $repo->findPlaylistsByUser($userId);

        if (empty($playlists)) {
            return "<p>Vous n'avez créé aucune playlist pour le moment.</p>";
        }

        $html = "<h2>Mes playlists</h2><ul>";
        foreach ($playlists as $pl) {
            $id = htmlspecialchars($pl['id']);
            $name = htmlspecialchars($pl['nom']);
            $html .= "<li>ID: $id — Nom: $name</li>";
        }
        $html .= "</ul>";

        return $html;
    }

    protected function post(): string
    {
        return $this->get();
    }
}
