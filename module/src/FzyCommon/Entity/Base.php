<?php

namespace FzyCommon\Entity;

use FzyCommon\Entity\Base\S3FileInterface;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @ORM\MappedSuperclass
 */
abstract class Base implements BaseInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Annotation\Exclude()
     *
     * @var int
     */
    protected $id;


    /**
     * Ephemeral value set per-request. Not to be stored in the db
     * @var
     */
    private $formTag;

    public function __construct()
    {

    }

    /**
     * Turn this entity into an associative array
     * @param  boolean $extended
     * @return array
     */
    public function flatten()
    {
        $result = array('id' => $this->id());
        if ($this instanceof S3FileInterface) {
            $result[S3FileInterface::S3_KEY] = array(
                S3FileInterface::S3_KEYS_INDEX => $this->getS3Keys(),
                S3FileInterface::S3_URLS_INDEX => $this->getS3UrlKeys(),
            );
        }

        return $result;
    }

    public function id()
    {
        return $this->id;
    }

    /**
     * Returns whether this is a null object
     * @return bool
     */
    public function isNull()
    {
        return false;
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
        $results = array();
        foreach ($collection as $entity) {
            if (!$entity instanceof BaseInterface) {
                $results[] = $entity;
                continue;
            }
            $value = $extended ? $entity->flatten() : $entity->id();
            if ($indexById) {
                $results[$entity->id()] = $value;
            } else {
                $results[] = $value;
            }
        }

        return $results;
    }

    /**
     * Helper method to allow entities to set $this->property = $entity->asDoctrineProperty()
     * which will translate setting a null entity to setting a null value
     * @return \App\Entity\Base|null
     */
    public function asDoctrineProperty()
    {
        return $this;
    }

    /**
     * Adds $this to collection
     * @param  \Doctrine\Common\Collections\Collection $collection
     * @return mixed
     */
    public function addSelfTo(\Doctrine\Common\Collections\Collection $collection)
    {
        $collection->add($this);

        return $this;
    }

    /**
     * Helper method to allow entities to return
     * $this->nullGet('Property\Class\Name', $this->property)
     * and have the entity never return an actual null
     * @param  type                              $className
     * @param  \FzyCommon\Entity\BaseInterface $entity
     * @return \FzyCommon\Entity\BaseInterface
     */
    public function nullGet($className, BaseInterface $entity = null)
    {
        $nullClass = $className . 'Null';
        if ($entity === null && class_exists($nullClass)) {
            return new $nullClass();
        }

        return $entity;
    }

    /**
     * Used to verify the value is valid to be assigned to timestamp property.
     * Acceptable: either \DateTime object or string which can be parsed to a \DateTime value.
     * An \InvalidArgumentException is thrown if the passed value does not meet that criteria
     * @param $ts
     * @param  string | boolean          $timezone - Pass in a \DateTimeZone timezone string or false
     * @return \DateTime
     * @throws \InvalidArgumentException
     */
    public function tsSet($ts, $createIfEmpty = true, $timezone = false)
    {
        if ($ts instanceof \DateTime) {
	        return $this->setDtTz($ts, $timezone);
        } else if (is_string($ts)) {
	        return $this->setDtTz(new \DateTime($ts), $timezone);
        } else if (empty($ts)) {
	        return $createIfEmpty ? $this->setDtTz(new \DateTime(), $timezone) : null;
        }

        throw new \InvalidArgumentException("The passed value '".var_export($ts, true)."' is not a valid timestamp.");
    }

	/**
	 * @param \DateTime $dt
	 * @param $timezone
	 *
	 * @return \DateTime
	 */
	protected function setDtTz(\DateTime $dt, $timezone)
	{
		if ($timezone) {
			$dt->setTimezone(new \DateTimeZone($timezone));
		}
		return $dt;
	}

    /**
     * Used to ensure a \DateTime is returned. If the given property is null, a new \DateTime
     * object is returned.
     * @param  \DateTime $ts The property which may contain a \DateTime value
     * @return \DateTime
     */
    public function tsGet(\DateTime $tsProperty = null)
    {
        if (empty($tsProperty)) {
            $tsProperty = new \DateTime();
        }

        return $tsProperty;
    }

    /**
     * Returns a formatted form of the datetime property, if the property is not null
     *
     * NOTE: the datetime is cloned and converted to EDT timezone since all doctrine
     * data is UTC (coming from RDS)
     * @param  \DateTime        $tsProperty
     * @param $format
     * @param  string $timezone   - Pass in a timezone string or false
     * @return string
     */
    public function tsGetFormatted(\DateTime $tsProperty = null, $format = self::DEFAULT_DATE_FORMAT, $timezone = null)
    {
        if ($tsProperty === null) {
            return '';
        }
        $shiftedTs = clone $tsProperty;
	    return $this->setDtTz($shiftedTs, $timezone)->format($format);
    }

    public function __toString()
    {
        return json_encode($this->flatten());
    }

    /**
     * Form Tag set on this entity for this request
     * @return mixed
     */
    public function getFormTag()
    {
        return $this->formTag;
    }

    /**
     * Retrieve tag set on this entity
     * @param $tag
     * @return mixed
     */
    public function setFormTag($tag)
    {
        $this->formTag = $tag;

        return $this;
    }

}
