<?php
use assayerpro\sitemap\Sitemap;

class SitemapTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSitemap()
    {
        Yii::$app->cache->flush();
        $sitemap = new Sitemap([
            'urls' => [
                [
                    'loc' => ['/news/default/index'],
                    'changefreq' => \assayerpro\sitemap\Sitemap::DAILY,
                    'priority' => 0.8,
                    'news' => [
                        'publication'   => [
                            'name'          => 'Example Blog',
                            'language'      => 'en',
                        ],
                        'access'            => 'Subscription',
                        'genres'            => 'Blog, UserGenerated',
                        'publication_date'  => '2015-11-04T19:27:01TZD',
                        'title'             => 'Example Title',
                        'keywords'          => 'example, keywords, comma-separated',
                        'stock_tickers'     => 'NASDAQ:A, NASDAQ:B',
                    ],
                    'images' => [
                        [
                            'loc'           => 'http://example.com/image.jpg',
                            'caption'       => 'This is an example of a caption of an image',
                            'geo_location'  => 'City, State',
                            'title'         => 'Example image',
                            'license'       => 'http://example.com/license',
                        ],
                    ],
                ],
                [
                    'loc' => ['/main/default/index'],
                ],
            ]
        ]);
$expectedXml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"><url><loc>http://wwww.example.com/news</loc><changefreq>daily</changefreq><priority>0.8</priority><news:news><news:publication><news:name>Example Blog</news:name><news:language>en</news:language></news:publication><news:access>Subscription</news:access><news:genres>Blog, UserGenerated</news:genres><news:publication_date>2015-11-04T19:27:01TZD</news:publication_date><news:title>Example Title</news:title><news:keywords>example, keywords, comma-separated</news:keywords><news:stock_tickers>NASDAQ:A, NASDAQ:B</news:stock_tickers></news:news><image:image><image:loc>http://example.com/image.jpg</image:loc><image:caption>This is an example of a caption of an image</image:caption><image:geo_location>City, State</image:geo_location><image:title>Example image</image:title><image:license>http://example.com/license</image:license></image:image></url><url><loc>http://wwww.example.com/</loc></url></urlset>
EOF;
        $this->assertEquals($expectedXml, $sitemap->render()[0]['xml']);
    }
    public function testSitemapIndex()
    {
        Yii::$app->cache->flush();
        $sitemap = new Sitemap([
            'maxSectionUrl' => 1,
            'urls' => [
                [
                    'loc' => ['/news/default/index'],
                    'changefreq' => \assayerpro\sitemap\Sitemap::DAILY,
                    'priority' => 0.8,
                ],
                [
                    'loc' => ['/main/default/index'],
                ]
            ]
        ]);
        $render = $sitemap->render();
        $this->assertEquals('/sitemap.xml', $render[0]['file']);
$expectedXml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>http://wwww.example.com/sitemap-1.xml</loc>
        <lastmod>2015-11-04T13:31:41+00:00</lastmod>
    </sitemap>
    <sitemap>
        <loc>http://wwww.example.com/sitemap-2.xml</loc>
        <lastmod>2015-11-04T13:31:41+00:00</lastmod>
    </sitemap>
</sitemapindex>
EOF;
        $expected = new DOMDocument;
        $expected->loadXML($expectedXml);
        $actual = new DOMDocument;
        $actual->loadXML($render[0]['xml']);
        $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild);
    }

    public function testSitemapCache()
    {
        Yii::$app->cache->flush();
        $sitemap = new Sitemap([
            'urls' => [
                [ 'loc' => '/'],
                [ 'loc' => '/api'],
            ]
        ]);
        $expectedXML = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"><url><loc>http://wwww.example.com/</loc></url><url><loc>http://wwww.example.com/api</loc></url></urlset>
EOF;
        $this->assertEquals($expectedXML,$sitemap->render()[0]['xml']);
        $sitemap->urls = [];
        $this->assertEquals($expectedXML,$sitemap->render()[0]['xml']);
    }
    public function testSitemapDatetow3c()
    {
        $this->assertEquals('2015-01-01T00:00:00+00:00', Sitemap::dateToW3C("01-01-2015"));
        $this->assertEquals('2015-11-04T14:52:47+00:00', Sitemap::dateToW3C(1446648767));
        $this->assertEquals('2015-11-04T14:53:57+00:00', Sitemap::dateToW3C("Wed Nov 4 17:53:57 MSK 2015"));
    }
}