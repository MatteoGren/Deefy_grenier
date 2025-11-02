<?php
namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action
{
    protected function get(): string
    {

        // Vérifie que l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            return "<p>Vous devez être connecté pour voir vos playlists.</p>";
        }

        return <<<HTML
<h2>Créer une playlist</h2>
<form method="post" action="?action=add-playlist">
    <label>Nom de la playlist : <input type="text" name="name" required></label><br>
    <button type="submit">Créer</button>
</form>
HTML;
    }

    protected function post(): string
    {
        $user = $_SESSION['user'] ?? null;
        $userId = $user['id'];

        $repo = DeefyRepository::getInstance();

        $playlistName = trim($_POST['name'] ?? '');
        if ($playlistName === '') {
            return "<p>Le nom de la playlist est requis.</p>";
        }

        //ligne qui crée la playlist dans la table playlist
        $playlistId = $repo->savePlaylist($playlistName);

        //ligne qui permet d'ajoute la relation user → playlist dans la table user2playlist de la BD
        $repo->linkPlaylistToUser($userId, $playlistId);

        return "<p>Playlist créée avec succès !</p>
        <p><a href='?action=add-track'>ajouter une track</a></p>";
    }


}
