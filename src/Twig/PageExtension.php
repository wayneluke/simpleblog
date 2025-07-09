<?php

namespace App\Twig;

use App\Page\PageRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PageExtension extends AbstractExtension
{
    public function __construct(private PageRepository $pages) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('header_pages', fn() => array_filter(
                $this->pages->all(),
                fn($p) => $p->showInHeader()
            )),
            new TwigFunction('footer_pages', fn() => array_filter(
                $this->pages->all(),
                fn($p) => $p->showInFooter()
            )),
        ];
    }
}