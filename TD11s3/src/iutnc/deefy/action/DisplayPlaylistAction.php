<?php
namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\AudioListRenderer;

class DisplayPlaylistAction extends Action
{
    protected function get(): string
    {
        $repo = DeefyRepository::getInstance();

        $allPlaylists = $repo->findAllPlaylists();
        $playlistsHtml = "<h2>Liste de toutes les playlists</h2><ul>";
        foreach ($allPlaylists as $pl) {
            $playlistsHtml .= "<li>ID {$pl['id']} : " . htmlspecialchars($pl['nom']) . "</li>";
        }
        $playlistsHtml .= "</ul>";



        //formulaire pour saisir l'ID de la playlist à afficher
        $form = <<<HTML
<h2>Voir une playlist en particulier</h2>
<form method="get" action="">
    <input type="hidden" name="action" value="display-playlist">
    <label>ID de la playlist : <input type="number" name="id" min="1" required></label>
    <button type="submit">Afficher</button>
</form>
HTML;

        //ligne pour afficher la playlist si un ID est fournie
        $id = $_GET['id'] ?? null;
        $result = "";

        if ($id) {
            $playlist = $repo->findPlaylistById((int)$id);

            if ($playlist) {
                $renderer = new AudioListRenderer($playlist);
                $result = "<div style='margin-top:20px;'>" . $renderer->render() . "</div>";
            } else {
                $result = "<p>Erreur : Playlist introuvable.</p>";
            }
        }

        //ligne pour retourner le tout
        return $playlistsHtml . $form . $result;
    }

    protected function post(): string
    {
        return $this->get();
    }
}
