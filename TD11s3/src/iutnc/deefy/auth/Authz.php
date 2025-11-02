<?php
namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class Authz
{
    private iutnc\deefy\auth\User $authenticated_user;

    public function __construct(\app\auth\User $user)
    {
        $this->authenticated_user = $user;
    }


    public static function checkRole(int $expectedRole) {
        $user = AuthnProvider::getSignedInUser();
        if ($user->getRole() != $expectedRole) {
            throw new AuthnException("Rôle insuffisant");
        }
    }



    public static function checkPlaylistOwner(int $playlistId): void
    {
        $user = AuthnProvider::getSignedInUser();
        $userId = $user['id'];
        $userRole = $user['role'];

        $repo = DeefyRepository::getInstance();
        $stmt = $repo->getPDO()->prepare(
            "SELECT id_user FROM user2playlist WHERE id_pl = :id_pl"
        );
        $stmt->execute([':id_pl' => $playlistId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $ownerId = $result ? (int)$result['id_user'] : null;

        if ($ownerId !== $userId && $userRole != 100) {
            throw new AuthnException(" vous n'êtes pas propriétaire de cette playlist");
        }
    }



}
