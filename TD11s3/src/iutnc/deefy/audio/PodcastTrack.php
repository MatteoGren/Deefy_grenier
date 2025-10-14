<?php

namespace iutnc\deefy\audio;

class PodcastTrack extends AudioTrack {
    public string $author;
    public function __construct(protected string $title, string $author)
    {
        parent::__construct($title, 10);
        $this->author = $author;
    }
}