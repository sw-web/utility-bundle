<?php
namespace Sw\UtilityBundle\Annotation;

/**
 * Standard object annotation
 * This annotation is used to configure the conversion to stdClass/array
 * of an entity
 *
 * @Annotation
 *
 * @package Sw\UtilityBundle
 * @author  Cyril Blanco <cyril.blanco@sword-group.com>
 */
class StandardObject
{
    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var string
     */
    private $dataType = 'string';

    /**
     * Class constructor
     *
     * @param array $options Options
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct($options)
    {
        if (isset($options['value'])) {
            $options['propertyName'] = $options['value'];
            unset($options['value']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    /**
     * Get property name
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Get data type
     *
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }
}
