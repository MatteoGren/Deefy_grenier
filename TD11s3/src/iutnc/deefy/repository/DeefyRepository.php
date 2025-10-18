<?php

namespace iutnc\deefy\repository;

use PDO;
use iutnc\deefy\audio\Playlist;

class DeefyRepository
{


    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file)
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }
        self::$config = ['dsn' => $conf['driver'] . ":host=" . $conf['host'] . ";dbname=" . $conf['database'], 'user' => $conf['username'], 'pass' => $conf['password'] ];
    }





    // =====================
    // EXERCICE 3 : Méthodes demandées
    // =====================

    /**
     * 1 Récupérer la liste des playlists (sans pistes)
     */
    public function findAllPlaylists(): array {
        $stmt = $this->pdo->query("SELECT * FROM playlist");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 2 Sauvegarder une playlist vide
     */
    public function savePlaylist(string $nom): int {
        $stmt = $this->pdo->prepare("INSERT INTO playlist (nom) VALUES (:nom)");
        $stmt->execute([':nom' => $nom]);
        return (int) $this->pdo->lastInsertId();
    }


    /**
     * 3 Sauvegarder une piste
     */
    public function saveTrack(string $titre, string $genre , int $duree ): int {
        $stmt = $this->pdo->prepare("
        INSERT INTO track (titre, genre, duree) 
        VALUES (:titre, :genre, :duree)
    ");
        $stmt->execute([
            'titre' => $titre,
            'genre' => $genre,
            'duree' => $duree
        ]);
        return (int)$this->pdo->lastInsertId(); // retourne l'id de la piste créée
    }


    /**
     * 4 Ajouter une piste existante à une playlist existante
     */




}

