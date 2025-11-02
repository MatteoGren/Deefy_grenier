<?php
namespace iutnc\deefy\auth;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException;
use PDO;

class AuthnProvider {

    public static function signin(string $email, string $passwd): array
    {
        $repo = DeefyRepository::getInstance();
        $pdo = $repo->getPDO();

        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($passwd, $user['passwd'])) {
            throw new AuthnException("Identifiants incorrect ou mot de passe incorrect.");
        }

        return [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
    }

    public static function getSignedInUser(): array
    {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Utilisateur non connecté");
        }
        return $_SESSION['user'];
    }

    private static function checkPasswordStrength(string $pass, int $minLength = 10): bool {
        $length = strlen($pass) >= $minLength;
        $digit = preg_match('#\d#', $pass);
        $special = preg_match('#\W#', $pass);
        $lower = preg_match('#[a-z]#', $pass);
        $upper = preg_match('#[A-Z]#', $pass);
        return $length && $digit && $special && $lower && $upper;
    }

    public static function register(string $email, string $passwd, string $passwd2): void
    {
        if ($passwd !== $passwd2) {
            throw new AuthnException("Les deux mots de passe ne correspondent pas.");
        }

        if (!self::checkPasswordStrength($passwd)) {
            throw new AuthnException("Le mot de passe doit contenir au moins 10 caractères, avec une majuscule, une minuscule, un chiffre et un caractère spécial.");
        }

        $repo = DeefyRepository::getInstance();
        $pdo = $repo->getPDO();

        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            throw new AuthnException("Un compte existe déjà avec cet email.");
        }

        $hash = password_hash($passwd, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("INSERT INTO User (email, passwd, role) VALUES (:email, :passwd, 1)");
        $stmt->execute([':email' => $email, ':passwd' => $hash]);
    }
}
