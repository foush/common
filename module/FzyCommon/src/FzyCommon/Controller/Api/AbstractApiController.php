<?php

namespace FzyCommon\Controller\Api;

use FzyCommon\Service\Search\Base as SearchService;
use FzyCommon\Service\Update\Base as UpdateService;
use FzyCommon\Util\Params;
use Zend\View\Model\JsonModel;

/**
 * Class AbstractController
 * @package FzyCommon\Controller\Api
 */
abstract class AbstractApiController extends \FzyCommon\Controller\AbstractController
{
    protected function search(Params $params, SearchService $searchService)
    {
        return new JsonModel($this->fzySearchResult($searchService->search($params)));
    }

    protected function update(Params $params, UpdateService $updateService)
    {
        $updateService->update($params);

        return new JsonModel($this->fzyUpdateResult($updateService));
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $params = $this->getParamsFromRequest();

        return $this->search($params, $this->getSearchService($params));
    }

    /**
     * @return mixed
     */
    public function updateAction()
    {
        /* @var $params \FzyCommon\Util\Params */
        $params = $this->getParamsFromRequest();

        return $this->update($params, $this->getUpdateService($params));
    }
}
