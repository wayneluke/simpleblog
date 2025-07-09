<?php

// src/Controller/PageController.php
namespace App\Controller;

use App\Page\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Catch-all for static pages:
 *   /about
 *   /privacy-policy
 *   /terms-of-use
 *   /disclaimer
 *   /github  (→ redirects externally)
 *
 * NOTE: This route appears *after* the blog/category/tag routes
 *       so there’s no collision with /post/{slug}, /category/{cat}, etc.
 */
class PageController extends AbstractController
{
    public function __construct(private PageRepository $pages) {}

    #[Route(
        '/{slug}',
        name: 'page_show',
        // block reserved paths we already use elsewhere
        requirements: ['slug' => '^(?!posts$|post$|category$|tag$).+']
    )]
    public function show(string $slug)
    {
        $page = $this->pages->find($slug)
             ?? throw $this->createNotFoundException();

        if ($page->isRedirect) {
            return new RedirectResponse($page->redirectUrl, 302);
        }

        return $this->render('page/show.html.twig', ['page' => $page]);
    }
}