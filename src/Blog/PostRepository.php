<?php

namespace App\Blog;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Yaml\Yaml;
use Cocur\Slugify\Slugify;

final class PostRepository
{
    private string $path;
    private Slugify $slugger;
    private CommonMarkConverter $md;

    public function __construct(string $postsDir)
    {
        $this->path    = rtrim($postsDir, '/');
        $this->slugger = new Slugify();
        $this->md      = new CommonMarkConverter();
    }

    /** @return Post[] newest first */
    public function all(): array
    {
        $posts = [];
        foreach (glob($this->path.'/*.md') as $file) {
            $posts[] = $this->map($file);
        }
        usort($posts, fn(Post $a, Post $b) => $b->date <=> $a->date);
        return $posts;
    }

    public function find(string $slug): ?Post
    {
        foreach ($this->all() as $post) {
            if ($post->slug === $slug) {
                return $post;
            }
        }
        return null;
    }

    /** @return Post[] */
    public function byCategory(string $category): array
    {
        return array_values(array_filter($this->all(),
            fn(Post $p) => $p->category === $category));
    }

    /** @return Post[] */
    public function byTag(string $tag): array
    {
        return array_values(array_filter($this->all(),
            fn(Post $p) => in_array($tag, $p->tags, true)));
    }

    private function map(string $file): Post
    {
        $raw = file_get_contents($file);
        [$fm, $md] = $this->split($raw);

        $data   = Yaml::parse($fm);
        $slug   = $this->slugger->slugify($data['title']);
        $html   = $this->md->convert($md)->getContent();
        // DateTimeImmutable::createFromFormat("Y-m-d", "2015-09-34");
        $date   = new \DateTimeImmutable($data['date']);

        return new Post(
            slug:     $slug,
            title:    $data['title'],
            date:     $date,
            category: $data['category'],
            tags:     $data['tags'] ?? [],
            summary:  $data['summary'] ?? '',
            html:     $html
        );
    }

    /** Split "---\nyaml\n---\nmarkdown" */
    private function split(string $raw): array
    {
        if (!preg_match('/^---\s*(.*?)^---\s*(.*)$/ms', $raw, $m)) {
            throw new \RuntimeException('Missing front-matter');
        }
        return [$m[1], $m[2]];
    }
    
    /** 
     * Return the latest posts. 
     *
     * @param int $max Maximum number of posts to return. Default 10.
     * 
     * @return Post[] newest-first, limited 
     */
    public function latest(int $max = 10): array
    {
        return \array_slice($this->all(), 0, $max);
    }

    /** 
     * Returns a list of all categories defined across posts.
     *
     * @return string[] Unique categories (alphabetical) 
     */
    public function allCategories(): array
    {
        $cats = array_map(
            static fn(Post $p) => $p->category,
            $this->all()
        );

        $cats = array_unique($cats);
        sort($cats, SORT_NATURAL | SORT_FLAG_CASE);

        return $cats;
    }

    /**
     * Returns a alphabetically sorted list of all unique tags.
     *
     * @return string[] Unique tags (alphabetical) 
     */
    public function allTags(): array
    {
        $tags = [];

        foreach ($this->all() as $post) {
            $tags = array_merge($tags, $post->tags);
        }

        $tags = array_unique($tags);
        sort($tags, SORT_NATURAL | SORT_FLAG_CASE);

        return $tags;
    }
    
    /**
     * Return tags ordered by how often they appear across all posts.
     *
     * @param int|null $limit  Pass e.g. 20 to trim the list to “top 20 tags”.
     *
     * @return array<string,int>  [ 'symfony' => 12, 'php' => 9, 'markdown' => 4, … ]
     */
    public function popularTags(int $limit = null): array
    {
        // Flatten all tag arrays → one big list
        $allTags = [];
        foreach ($this->all() as $post) {
            $allTags = array_merge($allTags, $post->tags);
        }

        // Count frequency: ['symfony' => 12, 'php' => 9, …]
        $counts = array_count_values($allTags);

        // Sort: DESC by usage, ASC by tag name as tiebreaker
        uasort($counts, static function ($aCount, $bCount) use ($counts) {
            if ($aCount === $bCount) {
                // When counts equal, compare tag strings (keys) alphabetically
                return 0;
            }
            return $bCount <=> $aCount; // higher usage first
        });

        // Trim if a limit is requested
        if ($limit !== null) {
            $counts = array_slice($counts, 0, $limit, true);
        }

        return $counts;
    }    
}