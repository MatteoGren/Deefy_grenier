<?php
namespace iutnc\deefy\audio;

class PodcastTrack extends AudioTrack {
    public string $author;

    // duration paramétrable (default 10 si non fourni)
    public function __construct(string $title, string $author, int $duration = 10)
    {
        parent::__construct($title, $duration);
        $this->author = $author;
    }
}
