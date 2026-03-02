<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.xml file';

    public function handle(): void
    {
        $sitemap = Sitemap::create();

        // Static pages
        $sitemap->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('weekly'));
        $sitemap->add(Url::create('/angebot')->setPriority(0.9)->setChangeFrequency('weekly'));
        $sitemap->add(Url::create('/referenzen')->setPriority(0.7)->setChangeFrequency('monthly'));
        $sitemap->add(Url::create('/ueber-uns')->setPriority(0.6)->setChangeFrequency('monthly'));
        $sitemap->add(Url::create('/aktuelles')->setPriority(0.8)->setChangeFrequency('weekly'));
        $sitemap->add(Url::create('/kontakt')->setPriority(0.7)->setChangeFrequency('monthly'));

        // Services
        Service::published()->ordered()->each(function (Service $service) use ($sitemap) {
            $sitemap->add(
                Url::create("/angebot/{$service->slug}")
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($service->updated_at)
            );
        });

        // Blog posts
        Post::published()->latest()->each(function (Post $post) use ($sitemap) {
            $sitemap->add(
                Url::create("/aktuelles/{$post->slug}")
                    ->setPriority(0.7)
                    ->setChangeFrequency('monthly')
                    ->setLastModificationDate($post->updated_at)
            );
        });

        // Generic pages (Impressum, Datenschutz, etc.)
        Page::published()->each(function (Page $page) use ($sitemap) {
            if (in_array($page->slug, ['ueber-uns'])) {
                return;
            }
            $sitemap->add(
                Url::create("/{$page->slug}")
                    ->setPriority(0.4)
                    ->setChangeFrequency('monthly')
                    ->setLastModificationDate($page->updated_at)
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));
        $this->info('Sitemap generated at public/sitemap.xml');
    }
}
