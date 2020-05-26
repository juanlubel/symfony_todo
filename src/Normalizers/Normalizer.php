<?php

namespace App\Normalizers;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Normalizer
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
    )
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->serializer = new Serializer(
            [new ObjectNormalizer($classMetadataFactory)],
            [new JsonEncoder()]
        );
    }

    public function normalize($data)
    {
        if (is_array($data)) {
            $class_type = $this->name_space_explode($data[0]);
        } else {
            $class_type = $this->name_space_explode($data);
        }
        // on entity Group are defined whit annotations.
        // Serializer only show attributes whit class name on @Groups
        return $this->serializer->normalize($data, 'json', ['groups'=>[$class_type]]);
    }

    public function name_space_explode($name_space)
    {
        $array = explode("\\", get_class($name_space));
        return end($array);
    }
}