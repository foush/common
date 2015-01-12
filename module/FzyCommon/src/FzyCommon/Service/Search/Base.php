<?php
namespace FzyCommon\Service\Search;

use FzyCommon\Exception\Search\InvalidResultOffset;
use FzyCommon\Exception\Search\NoResultsToGet;
use FzyCommon\Exception\Search\NotFound;
use FzyCommon\Service\Base as BaseService;
use FzyCommon\Util\Page;
use FzyCommon\Util\Params;
use FzyCommon\Entity\BaseInterface;

abstract class Base extends BaseService implements ResultProviderInterface
{
    /**
     * @var int
     */
    protected $total;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var array
     */
    protected $results;

    protected $drawNumber;

    /**
     * Resets any params or results currently in this object
     *
     * @return $this
     */
    public function reset()
    {
        unset($this->results);
        unset($this->drawNumber);

        return $this;
    }

    /**
     * Main lifecycle of this service;
     * Using the passed in $params value
     * @param  Params $params
     * @param  bool   $asEntity - keep result as entity
     * @return $this
     */
    public function search(Params $params, $asEntity = false)
    {
        // hook to prepare for search
        $this->prepareSearch($params);

        // set limit/offset
        $this->setMetaData($params);

        if ($this->isSingular($params)) {
            try {
                $result = array($this->identitySearch($params));
                $this->setTotal(1);
            } catch (NotFound $e) {
                // nothing found
                $result = array();
                $this->setTotal(0);
            }
        } else {
            $result = $this->querySearch($params);
        }

        $this->preProcess($params, $result);
        $processed = array();
        foreach ($result as $entity) {
            $processed[] = $this->process($entity, $params, $result, $asEntity);
        }

        $this->postProcess($params, $result, $processed);
        $this->results = $processed;

        return $this;
    }

    /**
     * @param  Param                                $params
     * @return \FzyCommon\Entity\BaseInterface
     * @throws \FzyCommon\Exception\Search\NotFound
     */
    public function identitySearch(Params $params)
    {
        if ($params->get($this->getIdParam()) == null) {
            throw new NotFound('Unable to locate this entity');
        }

        return $this->find($params->get($this->getIdParam()));
    }

    /**
     * This function is a hook which allows any inheriting class
     * to perform necessary setup functions before the singular/query
     * search is performed
     * @param Params $params
     */
    protected function prepareSearch(Params $params)
    {
    }

    /**
     * Returns whether, based on the Param values
     * the
     * @param  Params $params
     * @return bool
     */
    protected function isSingular(Params $params)
    {
        return $params->has($this->getIdParam());
    }

    /**
     * This function should return the value of
     * the param name used to identify this search class' repository
     *
     * Eg: if this is a User subclass, $this->getIdParam() ought to return 'userId'
     * so the param array can check if 'userId' was set and therefore
     * indicate a lookup rather than a general search
     *
     * @return mixed
     */
    abstract protected function getIdParam();

    /**
     * Performs a query based on the params for a collection
     * of objects to be returned. This function ought to
     * set the $total value
     * @param  Param              $params
     * @return array|\Traversable
     */
    abstract protected function querySearch(Params $params);

    /**
     * Sets the limit/offset metadata
     * @param  Params $params
     * @return $this
     */
    protected function setMetaData(Params $params)
    {
        $this->setLimit(Page::limit($params));
        $this->setOffset(Page::offset($params));
        $this->drawNumber = $params->get('draw');

        return $this;
    }

    /**
     * This function is a hook which allows any inheriting class
     * to perform necessary setup functions after the singular/query
     * search is performed, and before the results are processed
     * individually
     * @param Param              $params
     * @param array|\Traversable $result
     */
    protected function preProcess(Params $params, $result)
    {
    }

    /**
     * This is invoked on every result of the singular/query
     * search for uniformly transform the result set
     * @param $entity
     * @param  Param              $params
     * @param  array|\Traversable $results
     * @param  bool               $asEntity - Keep entity as entity
     * @return $entity
     */
    protected function process($entity, Params $params, $results, $asEntity = false)
    {
        return $asEntity ? $entity : (string) $entity;
    }

    /**
     * This function is a hook which allows any inheriting class
     * to perform necessary teardown/cleanup functions after the
     * singular/query search results have been processed
     * @param Param              $params
     * @param array|\Traversable $result
     * @param array              $processed
     */
    protected function postProcess(Params $params, $result, array $processed)
    {
    }

    /**
     * Finds an element in this domain which has the specified ID
     *
     * @param $id
     * @return BaseInterface
     * @throws \FzyCommon\Exception\Search\NotFound
     */
    abstract public function find($id);

    /**
     * Get the resulting set that matches the search
     * @return array|\Traversable
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Convenience method for retrieving a single result (defaults to return the first)
     * @param  int                                             $offset
     * @return mixed
     * @throws \FzyCommon\Exception\Search\NoResultsToGet
     * @throws \FzyCommon\Exception\Search\InvalidResultOffset
     */
    public function getResult($offset = 0)
    {
        if (!is_array($this->results)) {
            throw new NoResultsToGet('No results have been generated');
        }
        if (!isset($this->results[$offset])) {
            throw new InvalidResultOffset("Unable to get result at offset '$offset'");
        }

        return $this->results[$offset];
    }

    /**
     * Get the current page's limit
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the current page's offset
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Returns the reported total number of results available
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Returns an identifying name for this type of search
     * (so pages with multiple paginated data sets can generate events
     * about this data set being updated/modified)
     * @return string
     */
    abstract public function getResultTag();

    /**
     * For use with datatables
     * @return int
     */
    public function getDrawNumber()
    {
        return $this->drawNumber;
    }
}
