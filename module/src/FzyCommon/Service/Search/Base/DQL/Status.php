<?php
namespace FzyCommon\Service\Search\Base\DQL;

use FzyCommon\Service\Search\Base\DQL as SearchService;
use Doctrine\ORM\QueryBuilder;
use FzyCommon\Util\Params;

/**
 * Class Adjuster
 * @package FzyCommon\Service\Search\Base\DQL
 * Service Key: statuses
 */
class Status extends SearchService
{
    /**
     * Default settlement status is 7 "New"
     */
    const DEFAULT_ENTITY_ID = 7;

    /**
     * @var int
     */
    protected $defaultEntityId = self::DEFAULT_ENTITY_ID;

    /**
     *
     * @return \FzyCommon\Entity\BaseInterface
     */
    public function getDefaultEntity()
    {
       return $this->identitySearch(Params::create(array($this->getIdParam() => $this->getDefaultEntityId())));
    }

    /**
     * If there is some ordering that needs to be applied, do it here
     * @param Param        $params
     * @param QueryBuilder $qb
     */
    protected function addOrdering(Params $params, QueryBuilder $qb)
    {
        $this->orderByName($params, $qb);
    }

    /**
     * If there is some ordering that needs to be applied, do it here
     * @param Param        $params
     * @param QueryBuilder $qb
     */
    protected function orderByName(Params $params, QueryBuilder $qb)
    {
        if ($params->has('orderby')) {
            // TODO: Implement orderby
        } else {
            $qb->addOrderBy($this->alias('name'), 'ASC');
        }

        return $this;
    }

    /**
     * Add where clauses to the query
     * @param Param        $params
     * @param QueryBuilder $qb
     */
    protected function addFilters(Params $params, QueryBuilder $qb)
    {
        parent::addFilters($params, $qb);
        $this->filterByName($params, $qb);
        $this->filterById($params, $qb);
    }

    /**
     * @param  Param        $params
     * @param  QueryBuilder $qb
     * @return $this
     */
    protected function filterByName(Params $params, QueryBuilder $qb)
    {
        if ($params->has('name')) {
            $qb->andWhere($qb->expr()->like($this->alias('name'), ':name'))->setParameter('name', '%' . $params->get('name') . '%');
        }

        if ($params->has('query')) {
            $qb->andWhere($qb->expr()->like($this->alias('name'), ':name'))->setParameter('name', '%' . $params->get('query') . '%');
        }

        return $this;
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
    protected function getIdParam()
    {
        return 'status';
    }

    /**
     * This function is used by the class to get
     * the entity's repository to be returned
     * @return mixed
     */
    protected function getRepository()
    {
        return 'FzyCommon\Entity\Base\Status';
    }

    /**
     * Returns an identifying name for this type of search
     * (so pages with multiple paginated data sets can generate events
     * about this data set being updated/modified)
     * @return string
     */
    public function getResultTag()
    {
        return 'status';
    }

    /**
     * Alias for the primary repository in the DQL statement
     * @return string
     */
    public function getRepositoryAlias()
    {
        return 's';
    }

    /**
     * @param int $defaultEntityId
     */
    public function setDefaultEntityId($defaultEntityId)
    {
        $this->defaultEntityId = $defaultEntityId;
    }

    /**
     * @return int
     */
    public function getDefaultEntityId()
    {
        return $this->defaultEntityId;
    }
}
