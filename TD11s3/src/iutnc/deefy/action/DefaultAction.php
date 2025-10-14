<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action {

    protected function get(): string {
        return <<<HTML
        <h1>Bienvenue sur DeefyApp</h1>
        HTML;
    }

    protected function post(): string {
        return $this->get();
    }
}
