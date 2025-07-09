<?php

namespace App\Twig;

use App\Blog\PostRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BlogExtension extends AbstractExtension
{
    public function __construct(private PostRepository $posts) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('all_categories', fn () => $this->posts->allCategories()),
            new TwigFunction('all_tags', fn () => $this->posts->allTags()),
            new TwigFunction('popular_tags', fn () => $this->posts->popularTags()),
        ];
    }
}