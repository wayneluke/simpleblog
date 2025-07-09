<?php

// src/Page/PageRepository.php
namespace App\Page;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Yaml\Yaml;
use Cocur\Slugify\Slugify;

final class PageRepository
{
    private string $path;
    private Slugify $slugger;
    private CommonMarkConverter $md;

    public function __construct(string $pagesDir)
    {
        $this->path    = rtrim($pagesDir, '/');
        $this->slugger = new Slugify();
        $this->md      = new CommonMarkConverter();
    }

    /** @return Page[] */
    public function all(): array
    {
        $pages = [];
        foreach (glob($this->path.'/*.md') as $file) {
            $pages[] = $this->map($file);
        }
        // alphabetical by slug for menus
        usort($pages, fn(Page $a, Page $b) => $a->slug <=> $b->slug);
        return $pages;
    }

    public function find(string $slug): ?Page
    {
        foreach ($this->all() as $page) {
            if ($page->slug === $slug) {
                return $page;
            }
        }
        return null;
    }

    private function map(string $file): Page
    {
        $raw = file_get_contents($file);
        [$fm, $md] = $this->split($raw);
        $data = Yaml::parse($fm);

        $slug = $this->slugger->slugify(pathinfo($file, PATHINFO_FILENAME));
        $isRedirect = isset($data['redirect']);
        $redirectUrl = $data['redirect'] ?? null;
        $html = $isRedirect ? '' : $this->md->convert($md)->getContent();

        return new Page(
            slug: $slug,
            title: $data['title'] ?? ucfirst($slug),
            html: $html,
            isRedirect: $isRedirect,
            redirectUrl: $redirectUrl,
            nav: $data['nav'] ?? null 
        );
    }

    private function split(string $raw): array
    {
        if (!preg_match('/^---\s*(.*?)^---\s*(.*)$/ms', $raw, $m)) {
            throw new \RuntimeException('Missing front-matter');
        }
        return [$m[1], $m[2]];
    }
}