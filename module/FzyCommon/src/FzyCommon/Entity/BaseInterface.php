<?php

namespace FzyCommon\Entity;

interface BaseInterface extends \JsonSerializable
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d';

    /**
     * Get entity id.
     * @return int
     */
    public function id();

    /**
     * Turn this entity into an associative array
     * @param  boolean $extended
     * @return array
     */
    public function flatten();

    /**
     * Returns whether this is a null object
     * @return boolean
     */
    public function isNull();

    /**
     * Calls flatten on each of the entities in a collection. If $indexById is true
     * the returned array is a key/value associative array with the entity ids as the key
     * @param $collection
     * @param  bool  $extended
     * @param  bool  $indexById
     * @return array
     */
    public function flatCollection($collection, $extended = false, $indexById = false);

    /**
     * Helper method to allow entities to set $this->property = $entity->asDoctrineProperty()
     * which will translate setting a null entity to setting a null value
     * @return \FzyCommon\Entity\Base|null
     */
    public function asDoctrineProperty();

    /**
     * Adds $this to collection
     * @param  \Doctrine\Common\Collections\Collection $collection
     * @param  bool                                    $allowDuplicates - (default false) set to true if this should be added to the collection regardless
     * @return mixed
     */
    public function addSelfTo(\Doctrine\Common\Collections\Collection $collection, $allowDuplicates = false);

    /**
     * Helper method to allow entities to return
     * $this->nullGet($this->property)
     * and have the entity never return an actual null
     * The second parameter is optional and allows overriding of
     * the null object instantiated by naming convention (classname + 'Null')
     *
     * @param  \FzyCommon\Entity\BaseInterface $entity
     * @param  \FzyCommon\Entity\BaseNull|null $nullObject
     * @return \FzyCommon\Entity\BaseInterface
     */
    public function nullGet(BaseInterface $entity = null, BaseNull $nullObject = null);

    /**
     * Used to verify the value is valid to be assigned to timestamp property.
     * Acceptable: either \DateTime object or string which can be parsed to a \DateTime value.
     * An \InvalidArgumentException is thrown if the passed value does not meet that criteria
     * @param $ts
     * @return \DateTime
     * @throws \InvalidArgumentException
     */
    public function tsSet($ts, $createIfEmpty = true);

    /**
     * Used to ensure a \DateTime is returned. If the given property is null, a new \DateTime
     * object is returned.
     * @param  \DateTime $ts The property which may contain a \DateTime value
     * @return \DateTime
     */
    public function tsGet(\DateTime $tsProperty = null);

    /**
     * Returns a formatted form of the datetime property, if the property is not null
     * @param  \DateTime   $tsProperty
     * @param $format
     * @return string|null
     */
    public function tsGetFormatted(\DateTime $tsProperty = null, $format = self::DEFAULT_DATE_FORMAT, $timezone = null);

    /**
     * Form Tag set on this entity for this request
     * @return mixed
     */
    public function getFormTag();

    /**
     * Retrieve tag set on this entity
     * @param $tag
     * @return mixed
     */
    public function setFormTag($tag);
}
