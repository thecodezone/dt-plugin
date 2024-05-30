<?php

namespace Tests;

use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Api\Languages;
use function CodeZone\Bible\container;

require "vendor-scoped/autoload.php";

/**
 * Class BibleBrainsSettingsTest
 *
 * This class is responsible for testing the BibleBrains settings page.
 *
 * @test
 */
class LanguageTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_fetch_a_language()
    {
        $response = $this->get('api/languages/6414');
        $this->assertEquals(200, $response->status());
        $result = json_decode($response->getContent(), true);
        $this->assertEquals('6414', $result['data']['id']);
    }

    /**
     * Test that the BibleBrains settings page loads.
     * @test
     */
    public function it_can_fetch_language_options()
    {
        $response = $this->get('api/languages/options', [ 'limit' => 2 ]);
        $this->assertEquals(200, $response->status());
        $result = json_decode($response->getContent(), true);
        $this->assertEquals(2, count($result['data']));
        foreach (json_decode($response->getContent(), true)['data'] as $language) {
            $this->assertArrayHasKey('value', $language);
            $this->assertArrayHasKey('itemText', $language);
        }
    }

    /**
     * Test that the BibleBrains settings page loads.
     * @test
     */
    public function it_can_search()
    {
        $languages = container()->make(Languages::class);
        $result    = $languages->search('Spanish');
        $this->assertGreaterThan(0, count($result['data']));
    }
}
