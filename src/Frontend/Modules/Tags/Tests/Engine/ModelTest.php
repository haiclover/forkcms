<?php

namespace Frontend\Modules\Tags\Tests\Engine;

use Backend\Modules\Tags\DataFixtures\LoadTagsModulesTags;
use Backend\Modules\Tags\DataFixtures\LoadTagsTags;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Language\Locale;
use Frontend\Modules\Search\Engine\Model as SearchModel;
use Frontend\Modules\Pages\Engine\Model as PagesModel;
use Frontend\Modules\Tags\Engine\Model as TagsModel;
use Frontend\Core\Tests\FrontendWebTestCase;

final class ModelTest extends FrontendWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $client = self::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadTagsTags::class,
                LoadTagsModulesTags::class,
            ]
        );
    }

    public function testCallFromInterfaceOnModuleThatDoesNotImplementIt(): void
    {
        $module = 'Search';
        $this->expectException(FrontendException::class);
        $this->expectExceptionMessage(
            'To use the tags module you need
            to implement the FrontendTagsInterface
            in the model of your module
            (' . $module . ').'
        );
        TagsModel::callFromInterface($module, SearchModel::class, 'getIdForTags', null);
    }

    public function testCallFromInterfaceOnModuleThatDoesImplementIt(): void
    {
        $module = 'Pages';
        $pages = TagsModel::callFromInterface($module, PagesModel::class, 'getForTags', [1]);

        self::assertSame($pages[0]['title'], 'Home');
    }

    public function testGettingATagWithTheDefaultLocale(): void
    {
        $url = 'test';
        $tag = TagsModel::get($url);
        $this->assertTag($tag);
        self::assertSame($tag['url'], $url);
    }

    public function testGettingATagWithASpecificLocale(): void
    {
        $url = 'test';
        $tag = TagsModel::get($url, Locale::fromString('en'));
        $this->assertTag($tag);
        self::assertSame($tag['url'], $url);
        self::assertSame($tag['language'], 'en');
    }

    public function testGetAllTags(): void
    {
        $this->assertTag(TagsModel::getAll()[0], ['url', 'name', 'number']);
    }

    public function testGetMostUsed(): void
    {
        self::assertSame(TagsModel::getMostUsed(0), 'Most used limit isn\'t respected');
        $mostUsedTags = TagsModel::getMostUsed(2);
        $this->assertTag($mostUsedTags[0], ['url', 'name', 'number']);
        $this->assertTag($mostUsedTags[1], ['url', 'name', 'number']);
        self::assertTrue($mostUsedTags[0]['number'] >= $mostUsedTags[1]['number'], 'Tags not sorted by usage');
    }

    public function testGetForItemWithDefaultLocale(): void
    {
        $tags = TagsModel::getForItem('Pages', 1);
        $this->assertTag($tags[0], ['name', 'full_url', 'url']);
    }

    public function testGetForItemWithSpecificLocale(): void
    {
        $tags = TagsModel::getForItem('Pages', 1, Locale::fromString('en'));
        $this->assertTag($tags[0], ['name', 'full_url', 'url']);
    }

    public function testGetForMultipleItemsWithDefaultLocale(): void
    {
        $tags = TagsModel::getForMultipleItems('Pages', [1, 2]);
        self::assertArrayHasKey(1, $tags);
        self::assertArrayHasKey(2, $tags);
        $this->assertTag($tags[1][0], ['name', 'other_id', 'url', 'full_url']);
        $this->assertTag($tags[2][0], ['name', 'other_id', 'url', 'full_url']);
    }

    public function testGetForMultipleItemsSpecificLocale(): void
    {
        $tags = TagsModel::getForMultipleItems('Pages', [1, 2], Locale::fromString('en'));
        self::assertArrayHasKey(1, $tags);
        self::assertArrayHasKey(2, $tags);
        $this->assertTag($tags[1][0], ['name', 'other_id', 'url', 'full_url']);
        $this->assertTag($tags[2][0], ['name', 'other_id', 'url', 'full_url']);
    }

    public function testGetIdByUrl(): void
    {
        self::assertSame(1, TagsModel::getIdByUrl('test'));
        self::assertSame(2, TagsModel::getIdByUrl('most-used'));
    }

    public function testGetModulesForTag(): void
    {
        $modules = TagsModel::getModulesForTag(1);
        self::assertSame('Faq', $modules[0]);
        self::assertCount(2, $modules);
        self::assertCount(1, TagsModel::getModulesForTag(2));
    }

    public function testGetName(): void
    {
        self::assertSame('test', TagsModel::getName(1));
    }

    public function testGetRelatedItemsByTags(): void
    {
        $ids = TagsModel::getRelatedItemsByTags(1, 'Pages', 'Faq');
        self::assertSame('1', $ids[0]);
    }

    public function testGetItemsForTag(): void
    {
        $items = TagsModel::getItemsForTag(1);
        self::assertCount(2, $items);
        $this->assertModuleTags($items[1]);
        self::assertSame('Pages', $items[1]['name']);
        self::assertSame('Home', $items[1]['items'][0]['title']);
    }

    public function testGetItemsForTagAndModule(): void
    {
        $items = TagsModel::getItemsForTagAndModule(1, 'Pages');

        $this->assertModuleTags($items);
        self::assertSame('Pages', $items['name']);
        self::assertSame('Home', $items['items'][0]['title']);
    }

    public function testGetAllForTag(): void
    {
        self::assertSame(TagsModel::getAllForTag('tests', Locale::frontendLanguage()));
        $items = TagsModel::getAllForTag('test');
        self::assertSame('Faq', $items[0]['module']);
        self::assertSame('1', $items[0]['other_id']);
    }

    private function assertTag(array $tag, array $keys = ['id', 'language', 'name', 'number', 'url']): void
    {
        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $tag);
        }
    }

    private function assertModuleTags($items): void
    {
        self::assertArrayHasKey('name', $items);
        self::assertArrayHasKey('label', $items);
        self::assertArrayHasKey('items', $items);
        $this->assertTag($items['items'][0], ['id', 'title', 'full_url']);
    }
}
