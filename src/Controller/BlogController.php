<?php

namespace App\Controller;

use App\Blog\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    public function __construct(private PostRepository $posts) {}

    #[Route('/', name: 'blog_index')]
    public function index() {
        // show only the newest 10 posts
        return $this->render('blog/index.html.twig', [
            'posts' => $this->posts->latest(10),
        ]);
    }

    #[Route('/posts', name: 'blog_all')]
    public function all() {
        // full archive
        return $this->render('blog/all.html.twig', [
            'posts' => $this->posts->all(),
        ]);
    }

    #[Route('/post/{slug}',     name: 'blog_post')]
    public function show(string $slug) {
        $post = $this->posts->find($slug) ?? throw $this->createNotFoundException();
        return $this->render('blog/post.html.twig', compact('post'));
    }

    #[Route('/category/{cat}',  name: 'blog_category')]
    public function category(string $cat) {
        return $this->render('blog/category.html.twig', [
            'category' => $cat,
            'posts'    => $this->posts->byCategory($cat),
        ]);
    }

    #[Route('/tag/{tag}',       name: 'blog_tag')]
    public function tag(string $tag) {
        return $this->render('blog/tag.html.twig', [
            'tag'   => $tag,
            'posts' => $this->posts->byTag($tag),
        ]);
    }
    
}