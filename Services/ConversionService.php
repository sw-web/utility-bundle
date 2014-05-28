<?php
namespace Sw\UtilityBundle\Services;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Conversion service
 *
 * This service converts classes annotated with StandardObject to stdCLass/array
 *
 * @package Sw\UtilityBundle
 * @author  Cyril Blanco <cyril.blanco@sword-group.com>
 */
class ConversionService
{
    /**
     * @const string
     */
    const ANNOTATION_CLASS = 'Sw\\UtilityBundle\\Annotation\\StandardObject';

    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->reader = new AnnotationReader();
    }

    /**
     * Convert an annotated class to a stdClass
     *
     * @param object $originalObject Object
     *
     * @throws \RuntimeException
     * @return \stdClass
     */
    public function convert($originalObject)
    {
        $convertedObject = new \stdClass;
        $reflectionObject = new \ReflectionObject($originalObject);

        // For each class methods...
        foreach ($reflectionObject->getMethods() as $reflectionMethod) {

            // ...fetch the @StandardObject annotation from the annotation reader
            $annotation = $this->reader->getMethodAnnotation($reflectionMethod, self::ANNOTATION_CLASS);
            if (null !== $annotation) {
                $propertyName = $annotation->getPropertyName();

                // Retrieve the value for the property, by making a call to the method
                $value = $reflectionMethod->invoke($originalObject);

                // Try to convert the value to the configured type
                $type = $annotation->getDataType();

                if (false === settype($value, $type)) {
                    throw new \RuntimeException(sprintf('Could not convert value to type "%s"', $value));
                }

                $convertedObject->$propertyName = $value;
            }
        }

        return $convertedObject;
    }

    /**
     * Convert an array of objects to an array of stdClass
     *
     * @param array $originalObjects Array of objects
     *
     * @return array
     */
    public function convertAll(array $originalObjects)
    {
        $converted = array();
        foreach ($originalObjects as $originalObject) {
            $converted[] = $this->convert($originalObject);
        }

        return $converted;
    }

    /**
     * Convert a stdClass to an array
     *
     * @param \stdClass $originalObject Object
     *
     * @return array
     */
    public function convertToArray($originalObject)
    {
        return json_decode(json_encode($this->convert($originalObject)), true);
    }

    /**
     * Convert an array of objects to an array of array
     *
     * @param array $originalObjects Array of objects
     *
     * @return array
     */
    public function convertAllToArray(array $originalObjects)
    {
        $converted = array();
        foreach ($originalObjects as $originalObject) {
            $converted[] = $this->convertToArray($originalObject);
        }

        return $converted;
    }
}
