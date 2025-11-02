<?php
namespace iutnc\deefy\action;

class SignOutAction extends Action
{
    protected function get(): string
    {
        session_destroy();
        return "<p>Vous êtes déconnecté.</p><p><a href='?action=signin'>Se reconnecter</a></p>";
    }

    protected function post(): string
    {
        return $this->get();
    }
}
