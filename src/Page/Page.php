<?php

namespace App\Page;

final class Page
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $html,
        public bool   $isRedirect = false,
        public ?string $redirectUrl = null,
        public ?string $nav = null, // new
    ) {}

    public function showInHeader(): bool
    {
        return in_array($this->nav, ['header', 'both'], true);
    }

    public function showInFooter(): bool
    {
        return in_array($this->nav, ['footer', 'both'], true);
    }
}