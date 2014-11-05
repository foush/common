<?php
namespace FzyCommon\Service\Search\Base;

use FzyCommon\Exception\Search\InvalidResultOffset;
use FzyCommon\Service\Search\Base;
use FzyCommon\Util\Params;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FzyCommon\Exception\Search\NotFound;
use FzyCommon\Service\Filter\DQL\Filter;
use FzyCommon\Entity\BaseInterface as EntityInterface;

/**
 * Class DQL
 * @package FzyCommon\Service\Search\Base
 * Base class for DQL based searches
 */
abstract class DQL extends Base
{
	/**
	 * This function is used by the class to get
	 * the entity's repository to be returned
	 * @return mixed
	 */
	abstract protected function getRepository();

	/**
	 * Alias for the primary repository in the DQL statement
	 * @return string
	 */
	abstract public function getRepositoryAlias();

	/**
     * Map of what tables have been joined in this query already
     * @var array
     */
    protected $joinMap = array();

    /**
     * This is invoked on every result of the singular/query
     * search for uniformly transform the result set
     * @param $entity
     * @param  Param              $params
     * @param  array|\Traversable $results
     * @param  bool               $asEntity - Keep entity as entity
     * @return array
     */
    protected function process($entity, Params $params, $results, $asEntity = false)
    {
        return $asEntity ? $entity : $entity->flatten();
    }

    /**
     * Performs a query base don the params for a collection
     * of objects to be returned. This function ought to
     * set the $limit, $offset, and $total values
     * @param  Params $params
     * @return array
     */
    protected function querySearch(Params $params)
    {
        $qb = $this->em()->createQueryBuilder();

        $this->setupQueryBuilder($params, $qb);

        $this->addFilters($params, $qb); // add filters to the query
        $this->addOrdering($params, $qb); // add ordering constraints
        $this->addOffset($params, $qb); // add offset constraints
        $this->addLimit($params, $qb); // add limit constraint

        return $this->getQBResult($params, $this->queryHook($params, $qb));
    }

    /**
     * Returns an array or other iterable object containing results
     * @param  Param           $params
     * @param  Query           $query
     * @return Paginator|array
     */
    protected function getQBResult(Params $params, Query $query)
    {
        $paginated = new Paginator($query);
        $paginated->setUseOutputWalkers(false);
        $this->setTotal($paginated->count());

        return $paginated;
    }

    /**
     * Hook to allow subclasses to modify the final query
     * being passed into the pagination object
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @return Query
     */
    protected function queryHook(Params $params, QueryBuilder $qb)
    {
        return $qb->getQuery();
    }

    /**
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @return $this
     */
    protected function setupQueryBuilder(Params $params, QueryBuilder $qb)
    {
        $this->addSelect($params, $qb);
        $this->addFrom($params, $qb);

        return $this;
    }

    /**
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @return $this
     */
    protected function addSelect(Params $params, QueryBuilder $qb)
    {
        $qb->select($this->getRepositoryAlias());

        return $this;
    }

    /**
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @return $this
     */
    protected function addFrom(Params $params, QueryBuilder $qb)
    {
        $qb->from($this->getRepository(), $this->getRepositoryAlias());

        return $this;
    }

    /**
     * Add where clauses to the query
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @return $this
     */
    protected function addFilters(Params $params, QueryBuilder $qb)
    {
        return $this;
    }


    /**
     * If there is some ordering that needs to be applied, do it here
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @return $this
     */
    protected function addOrdering(Params $params, QueryBuilder $qb)
    {
        $orderDir = "ASC";
        if ($params->has('orderDir') && (strtoupper($params->get('orderDir')) == "DESC")) {
            $orderDir = "DESC";
        }
        if ($params->has('orderBy') && $params->get('orderBy') != "") {
            $qb->orderBy($this->alias($params->get('orderBy')), $orderDir);
        }

        return $this;
    }

    /**
     * Set the offset for the query results
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @return $this
     */
    protected function addOffset(Params $params, QueryBuilder $qb)
    {
        if ($this->getOffset() && $this->getOffset() > 0) {
            $qb->setFirstResult($this->getOffset());
        }

        return $this;
    }

    /**
     * Set the limit for the query results
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @return $this
     */
    protected function addLimit(Params $params, QueryBuilder $qb)
    {
        if ($this->getLimit() && $this->getLimit() > 0) {
            $qb->setMaxResults($this->getLimit());
        }

        return $this;
    }

    /**
     * Finds an element in this domain which has the specified ID
     *
     * @param $id
     * @throws NotFound
     * @return \FzyCommon\Entity\BaseInterface
     */
    public function find($id)
    {
        $params = Params::create(array($this->getIdParam() => $id, 'limit' => 1));
	    try {
		    return $this->reset()->search( $params, true )->getResult();
	    } catch (InvalidResultOffset $e) {
		    $class = $this->getRepository() . 'Null';
		    if (!class_exists($class)) {
			    throw new \RuntimeException('Unable to instantiate null class "'.$class.'"');
		    }
		    $entity = new $class();
		    return $entity;
	    }
    }

    /**
     * Convenience method to add an AND WHERE clause in a common format.
     * If $queryParameterName is unspecified, $requestParameterName is used for both
     *
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @param $requestParameterName
     * @param  null         $queryParameterName
     * @return $this
     */
    protected function quickParamFilter(Params $params, QueryBuilder $qb, $requestParameterName, $queryParameterName = null)
    {
        if ($queryParameterName === null) {
            $queryParameterName = $requestParameterName;
        }
        if ($params->has($requestParameterName)) {
            $qb->andWhere($this->alias($queryParameterName) . ' = :' . $queryParameterName)->setParameter($queryParameterName, $params->get($requestParameterName));
        }

        return $this;
    }

    /**
     * Convenience function so we don't have dots running around everywhere
     * @param $propertyName
     * @return string
     */
    public function alias($propertyName)
    {
        return $this->getRepositoryAlias() . '.' . $propertyName;
    }

    /**
     * Resets the state of this update service
     * @return $this
     */
    public function reset()
    {
        $this->joinMap = array();
        return parent::reset();
    }

    /**
     * Allows you to track which tables you have already joined on this query
     *
     * @param QueryBuilder $qb
     * @param $property
     * @param $joinedAlias
     * @param bool         $autoAlias
     *
     * @return $this
     */
    public function safeJoin(QueryBuilder $qb, $property, $joinedAlias, $autoAlias = true)
    {
        if ($autoAlias) {
            $property = $this->alias($property);
        }
        if (!isset($this->joinMap[$property])) {
            $qb->join($property, $joinedAlias);
            $this->joinMap[$property] = $joinedAlias;
        }

        return $this;
    }

}
