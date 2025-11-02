<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SignInAction extends Action
{
    protected function get(): string
    {
        return <<<HTML
        <h2>Connexion</h2>
        <form method="post" action="?action=signin">
            <label>Email : <input type="email" name="email" required></label><br>
            <label>Mot de passe : <input type="password" name="passwd" required></label><br>
            <button type="submit">Se connecter</button>
        </form>
        HTML;
    }

    protected function post(): string
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $passwd = filter_input(INPUT_POST, 'passwd', FILTER_UNSAFE_RAW);

        try {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $user = AuthnProvider::signin($email, $passwd);

            //Stockage dans la session
            $_SESSION['user'] = $user;
            $_SESSION['user_id'] = $user['id'];

            return "<p>Connexion réussie. Bienvenue, {$user['email']} !</p>
                <p><a href='?action=default'>Retour à l'accueil</a></p>";
        } catch (AuthnException $e) {
            return "<p>Erreur : {$e->getMessage()}</p>" . $this->get();
        }
    }

}
