<?php

namespace App\Command;

use App\Repository\AuthorRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use App\Importer\RSSExtractor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand(
    name: 'import:rss',
    description: 'Import Posts from RSS feeds',
)]
class ImportRssCommand extends Command
{
    static array $feedUrls = [
        'BMD' => 'https://www.blogdumoderateur.com/feed/',
        'Alex so yes' => 'https://alexsoyes.com/feed',
        'Grafikart' => 'http://feeds.feedburner.com/Grafikart',
        'La grotte du barbu' => 'https://www.grottedubarbu.fr/rss/',
        'Code Heroes' => 'https://www.codeheroes.fr/feed/',
        'Shevarezo' => 'https://blog.shevarezo.fr/feeds/latest_posts.xml',
        'Je suis un dev' => 'https://www.jesuisundev.com/feed/',
        'Marie Comet' => 'https://mariecomet.fr/feed/',
        'Dunglas' => 'https://feeds.feedburner.com/dunglas',
        'Maxpou' => 'https://www.maxpou.fr/rss-fr.xml',
        'CssTricks' => 'https://css-tricks.com/feed/',
        'Jérémy Decool' => 'https://www.jdecool.fr/feed/feed.xml',
        'Creative Juiz' => 'http://feeds.feedburner.com/creativejuiz',
        'MNapoli' => 'https://mnapoli.fr/atom.xml',
        'Vincent Bernat' => 'https://vincent.bernat.ch/fr/blog/atom.xml',
        'VinceOps.me' => 'https://vincent-g.fr/rss.xml',
        'Geeek' => 'https://www.geeek.org/rss/',
        'Buzut' => 'https://buzut.net/atom.xml',
        'Taadem' => 'https://blog.taadeem.net/rss.xml',
        'Blog Eleven' => 'https://blog.eleven-labs.com/feed.xml',
        'Blog Ippon' => 'https://blog.ippon.fr/rss/',
        'Blog du Webdesign' => 'https://www.blogduwebdesign.com/feed/',
        'Blog Nouvelles Technologies' => 'https://www.blog-nouvelles-technologies.fr/feed/',
        'Putaind de code' => 'https://putaindecode.io/api/articles/feeds/desc/feed.xml',
        'Freelance Publik' => 'https://talks.freelancerepublik.com/feed/',
        'Git Connected' => 'https://levelup.gitconnected.com/feed',
    ];

    private AuthorRepository $authorRepository;
    private PostRepository $postRepository;
    private TagRepository $tagRepository;

    public function __construct(
        AuthorRepository $authorRepository,
        PostRepository   $postRepository,
        TagRepository    $tagRepository)
    {
        $this->authorRepository = $authorRepository;
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $extractor = new RSSExtractor($this->authorRepository, $this->postRepository, $this->tagRepository, $io);

        foreach (self::$feedUrls as $author => $feedUrl) {
            $extractor->setFeed($author, $feedUrl);
            $extractor->extract();
        }

        $io->success('All feeds have been imported !');

        return Command::SUCCESS;
    }
}
