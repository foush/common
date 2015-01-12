<?php

namespace FzyCommon\Entity;

class BaseNull implements BaseInterface
{
    public function id()
    {
        return;
    }

    public function isNull()
    {
        return true;
    }

    /**
     * Turn this entity into an associative array
     * @param  boolean $extended
     * @return array
     */
    public function flatten()
    {
        return array('id' => null);
    }

    /**
     * Calls flatten on each of the entities in a collection. If $indexById is true
     * the returned array is a key/value associative array with the entity ids as the key
     * @param $collection
     * @param  bool  $extended
     * @param  bool  $indexById
     * @return array
     */
    public function flatCollection($collection, $extended = false, $indexById = false)
    {
        return array();
    }

    /**
     * Helper method to allow entities to set $this->property = $entity->asDoctrineProperty()
     * which will translate setting a null entity to setting a null value
     * @return \FzyCommon\Entity\Base|null
     */
    public function asDoctrineProperty()
    {
        return;
    }

    /**
     * Adds $this to collection
     * @param  \Doctrine\Common\Collections\Collection $collection
     * @return mixed
     */
    public function addSelfTo(\Doctrine\Common\Collections\Collection $collection, $allowDuplicates = false)
    {
        return $this;
    }

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
    public function nullGet(BaseInterface $entity = null, BaseNull $nullObject = null)
    {
        return new BaseNull();
    }

    /**
     * Used to verify the value is valid to be assigned to timestamp property.
     * Acceptable: either \DateTime object or string which can be parsed to a \DateTime value.
     * An \InvalidArgumentException is thrown if the passed value does not meet that criteria
     * @param $ts
     * @return \DateTime
     * @throws \InvalidArgumentException
     */
    public function tsSet($ts, $createIfEmpty = true)
    {
        return new \DateTime();
    }

    /**
     * Used to ensure a \DateTime is returned. If the given property is null, a new \DateTime
     * object is returned.
     * @param $ts
     * @return mixed
     */
    public function tsGet(\DateTime $tsProperty = null)
    {
        return new \DateTime();
    }

    public function tsGetFormatted(\DateTime $tsProperty = null, $format = self::DEFAULT_DATE_FORMAT, $timezone = null)
    {
        return;
    }

    public function __toString()
    {
        return '{}';
    }

    /**
     * Form Tag set on this entity for this request
     * @return mixed
     */
    public function getFormTag()
    {
        return;
    }

    /**
     * Retrieve tag set on this entity
     * @param $tag
     * @return mixed
     */
    public function setFormTag($tag)
    {
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return json_encode(array());
    }
}
