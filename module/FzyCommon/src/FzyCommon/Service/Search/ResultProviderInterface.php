<?php
namespace FzyCommon\Service\Search;

interface ResultProviderInterface
{
    /**
     * Get the resulting set that matches the search
     * @return array|\Traversable
     */
    public function getResults();

    /**
     * Get the current page's limit
     * @return int
     */
    public function getLimit();

    /**
     * Get the current page's offset
     * @return int
     */
    public function getOffset();

    /**
     * Returns the reported total number of results available
     * @return int
     */
    public function getTotal();

    /**
     * Returns an identifying name for this type of search
     * (so pages with multiple paginated data sets can generate events
     * about this data set being updated/modified)
     * @return string
     */
    public function getResultTag();

    /**
     * For use with datatables
     * @return int
     */
    public function getDrawNumber();
}
