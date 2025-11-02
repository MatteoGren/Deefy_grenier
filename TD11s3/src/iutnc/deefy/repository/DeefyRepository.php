<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\AudioTrack;
use iutnc\deefy\audio\Playlist;
use iutnc\deefy\audio\PodcastTrack;
use PDO;

class DeefyRepository
{


    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf){

        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file)
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("erreur lors de la lecture du fichier de configuration");
        }
        self::$config = ['dsn' => $conf['driver'] . ":host=" . $conf['host'] . ";dbname=" . $conf['database'], 'user' => $conf['username'], 'pass' => $conf['password']];
    }

    public function getPDO(): PDO {
        return $this->pdo;
    }



    public function findAllPlaylists(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM playlist");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public function savePlaylist(string $nom): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO playlist (nom) VALUES (:nom)");

        $stmt->execute([':nom' => $nom]);
        return (int)$this->pdo->lastInsertId();
    }


    public function saveTrack(string $titre, string $genre, int $duree, ?string $author = null): int
    {
        if ($author !== null) {
            // Podcast
            $type = 'P';
            $stmt = $this->pdo->prepare("INSERT INTO track (titre, genre, duree, type, auteur_podcast) VALUES (:titre, :genre, :duree, :type, :author)");

            $stmt->execute([
                ':titre' => $titre,
                ':genre' => $genre,
                ':duree' => $duree,
                ':type' => $type,
                ':author' => $author
            ]);
        } else {
            // Audio classique
            $type = 'A';
            $stmt = $this->pdo->prepare("INSERT INTO track (titre, genre, duree, type) VALUES (:titre, :genre, :duree, :type)");

            $stmt->execute([
                ':titre' => $titre,
                ':genre' => $genre,
                ':duree' => $duree,
                ':type' => $type
            ]);
        }

        return (int)$this->pdo->lastInsertId();
    }





    public function addTrackToPlaylist(int $idPlaylist, int $idTrack, ?int $noPiste = null): void
    {
        // Si la position n'est pas donnée, on la détermine automatiquement
        if ($noPiste === null) {
            $stmt = $this->pdo->prepare("SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1 AS nextPos FROM playlist2track WHERE id_pl = :id_pl");

            $stmt->execute([':id_pl' => $idPlaylist]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $noPiste = $row ? (int)$row['nextPos'] : 1;
        }

        // Insertion dans la table playlist2track
        $stmt = $this->pdo->prepare(" INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:id_pl, :id_track, :no_piste)");

        $stmt->execute([
            ':id_pl' => $idPlaylist,
            ':id_track' => $idTrack,
            ':no_piste' => $noPiste
        ]);
    }



    public function findPlaylistById(int $id): ?Playlist {
        // Récupération de la playlist
        $sql = "SELECT * FROM playlist WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);


        $playlist = new Playlist($data['nom']);
        $playlist->setId((int)$data['id']);

        // Récupération des pistes associées
        $sqlTracks = "SELECT t.* FROM track t JOIN playlist2track p2t ON t.id = p2t.id_track WHERE p2t.id_pl = :id ORDER BY p2t.no_piste_dans_liste ";

        $stmt = $this->pdo->prepare($sqlTracks);
        $stmt->execute([':id' => $id]);
        $tracks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($tracks as $t) {
            if ($t['type'] === 'A') {
                // AudioTrack
                $track = new AudioTrack(
                    $t['titre'],
                    (int)$t['duree']
                );
            } else {

                //ligne de vérification pour savoir si l'auteur du podcast est vide ou non
                $author = !empty($t['auteur_podcast']) ? $t['auteur_podcast'] : 'Inconnu';
                $track = new PodcastTrack(
                    $t['titre'],
                    $author,
                    (int)$t['duree']
                );
            }
            $playlist->addTrack($track);
        }


        return $playlist;
    }


    public function findPlaylistsByUser(int $userId): array
    {
        $sql = "SELECT p.* FROM playlist p JOIN user2playlist u2p ON p.id = u2p.id_pl WHERE u2p.id_user = :id_user";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_user' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }




    public function linkPlaylistToUser(int $userId, int $playlistId): void
    {
        $sql = "INSERT INTO user2playlist (id_user, id_pl) VALUES (:user_id, :playlist_id)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':playlist_id' => $playlistId
        ]);
    }




}

