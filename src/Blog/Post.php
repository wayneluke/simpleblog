<?php

namespace App\Blog;

final class Post
{
    public function __construct(
        public string  $slug,
        public string  $title,
        public \DateTimeImmutable $date,
        public string  $category,
        public array   $tags,
        public string  $summary,
        public string  $html // already rendered from Markdown
    ) {}

    /** Convenience helpers */
    public function getYear(): string   { return $this->date->format('Y'); }
    public function getMonth(): string  { return $this->date->format('m'); }
}