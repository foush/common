<?php

namespace FzyCommon\Service\Search;

use FzyCommon\Service\Base as BaseService;

/**
 * Class SearchResult
 * @package FzyCommon\Service\Search
 * Service Key: result
 */
class Result extends BaseService
{
    public function generatePageResult(ResultProviderInterface $provider)
    {
        return array(
            'data' => $provider->getResults(),
            'meta' => array(
                'total' => $provider->getTotal(),
                'limit' => $provider->getLimit(),
                'offset' => $provider->getOffset(),
                'tag' => $provider->getResultTag(),
            ),
        );
    }

    public function __invoke(ResultProviderInterface $provider)
    {
        return $this->generatePageResult($provider);
    }
}
