<?php

namespace App\Importer;

use App\Entity\Author;
use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\AuthorRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use SimpleXMLElement;
use Symfony\Component\Console\Style\SymfonyStyle;

class RSSExtractor
{

    public string $feedUrl;
    public string $feedAuthor;
    private SimpleXMLElement $xml;
    private AuthorRepository $authorRepository;
    private PostRepository $postRepository;
    private TagRepository $tagRepository;
    private SymfonyStyle $io;

    public function __construct(
        AuthorRepository $authorRepository,
        PostRepository   $postRepository,
        TagRepository    $tagRepository,
        SymfonyStyle     $io
    )
    {
        $this->authorRepository = $authorRepository;
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;
        $this->io = $io;
    }

    public function setFeed(string $author, string $url): void
    {
        $this->feedUrl = $url;
        $this->feedAuthor = $author;
    }

    public function getFeedAuthor(): string
    {
        return $this->feedAuthor;
    }

    public function getFeedUrl(): string
    {
        return $this->feedUrl;
    }

    public function extract(): void
    {
        $this->processData();
    }

    private function processData(): void
    {
        try {
            $this->xml = new SimpleXMLElement($this->getFeedUrl(), LIBXML_NOCDATA, true);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        if (!$this->xml->channel) return;

        foreach ($this->xml->channel->item as $xmlPost) {
            $this->createPost($xmlPost);
        }
    }

    private function getPostAuthor()
    {
        if (!$this->authorRepository->findByName($this->getFeedAuthor())) {
            $authorToCreate = new Author();
            $authorToCreate->setName($this->getFeedAuthor());
            $authorToCreate->setUrl($this->getFeedUrl());
            $authorToCreate->setThumbnail('https://st4.depositphotos.com/9998432/22597/v/450/depositphotos_225976914-stock-illustration-person-gray-photo-placeholder-man.jpg');
            $this->authorRepository->save($authorToCreate, true);
        }

        return $this->authorRepository->findByName($this->getFeedAuthor());
    }

    private function getTags($xmlPost): array
    {
        $tags = [];
        foreach ($xmlPost->category as $category) {
            $tagName = strtolower($category->__toString());
            if (!$this->tagRepository->findByName($tagName)) {
                $tagToCreate = new Tag();
                $tagToCreate->setName($tagName);
                $this->tagRepository->save($tagToCreate, true);
            }
            $tags[] = $this->tagRepository->findByName($tagName);
        }
        return $tags;
    }

    private function findThumbnail($xmlPost): string
    {
        if (isset($xmlPost->enclosure)) {
            $thumbnailUrl = $xmlPost->enclosure->attributes()['url']->__toString();
        } else {
            $thumbnailUrl = $this->findThumbnailInPostContent($xmlPost) ?: null;
        }

        if ($thumbnailUrl) return $thumbnailUrl;

        $tags = $this->getTags($xmlPost);
        $unsplashImageSearch = new UnsplashImageSearch(isset($tags[0]) ? $tags[0]->getName() : null);

        return  $unsplashImageSearch->getImage();
    }

    private function findThumbnailInPostContent($xmlPost): bool|string
    {
        preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $xmlPost->description->__toString(), $matches);

        if (count($matches) <= 0) return false;

        $src = $matches['src'];

        if (strpos($src, 'http')) return $src;

        if (str_starts_with($src, '/')) {
            $urlArray = parse_url($xmlPost->link->__toString(), PHP_URL_HOST);
            if (is_array($urlArray)) {
                return $urlArray['scheme'] . '://' . $urlArray['host'] . $src;
            }
        }

        return false;
    }

    private function createPost($xmlPost): void
    {
        $author = $this->getPostAuthor();
        $tags = $this->getTags($xmlPost);

        if (!$this->postRepository->findByTitle($xmlPost->title->__toString())) {
            /***
             * Create Post
             */
            $postToCreate = new Post();
            $postToCreate->setTitle($xmlPost->title->__toString());
            $postToCreate->setAuthor($author);
            $postToCreate->setDescription(strip_tags($xmlPost->description->__toString()));
            $postToCreate->setUrl($xmlPost->link->__toString());
            $postToCreate->setCreatedAt(new \DateTimeImmutable());

            /**
             * Add Thumbnail to Post
             */
            $thumbnailUrl = $this->findThumbnail($xmlPost);
            $postToCreate->setThumbnail($thumbnailUrl);


            /***
             * Add Tags to Post
             */
            foreach ($tags as $tag) {
                $postToCreate->addTag($tag);
            }

            /***
             * Save Post
             */
            $this->io->writeln('Saving post: ' . $postToCreate->getTitle() . ' ...');
            $this->postRepository->save($postToCreate, true);
            $this->io->writeln('Post: ' . $postToCreate->getTitle() . ' saved !');
        }

        $this->postRepository->findByTitle($xmlPost->title->__toString());
    }
}