<?php

namespace CodeZone\Bible\Services\BibleBrains\Api;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\Illuminate\Http\Client\Factory as Http;
use CodeZone\Bible\Illuminate\Support\Arr;
use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\PhpOption\Option;
use CodeZone\Bible\Services\BibleBrains\Reference;
use CodeZone\Bible\Services\Options;
use function CodeZone\Bible\CodeZone\Router\container;
use function CodeZone\Bible\collect;

class ApiKeys extends ApiService {
    protected $endpoint = 'keys';

    public function init( Http $http = null ) {
        $http       = $http ?? \CodeZone\Bible\container()->make( Http::class );
        $this->http = $http->biblePluginSite();
    }
}
