<?php

namespace iutnc\deefy\action;

class AddUserAction extends Action {

    protected function get(): string {
        return <<<FORM
        <h2>Inscription utilisateur</h2>
        <form method="post" action="?action=add-user">
            <label>Nom : <input type="text" name="name" required></label><br>
            <label>Email : <input type="email" name="email" required></label><br>
            <label>Âge : <input type="number" name="age" min="0" required></label><br>
            <button type="submit">Inscription</button>
        </form>
        FORM;
    }

    protected function post(): string {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);

        if (empty($name) || empty($email) || empty($age)) {
            return "<p>Erreur : tous les champs sont requis.</p>" . $this->get();
        }

        return <<<HTML
        <p>Nom : {$name}</p>
        <p>Email : {$email}</p>
        <p>Âge : {$age} ans</p>
        <p><a href="?action=default">Retour à l'accueil</a></p>
        HTML;
    }
}
