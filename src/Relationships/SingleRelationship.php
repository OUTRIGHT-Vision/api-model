<?php

namespace OUTRIGHTVision\Relationships;

use OUTRIGHTVision\ApiModel;

class SingleRelationship extends ApiModel
{
    protected $relatedClassQualifiedName = ApiModel::class;

    public function setRelateClassQualifiedName(string $class)
    {
        $this->relatedClassQualifiedName = $class;

        return $this;
    }

    public function getRelatedClassQualifiedName(): string
    {
        return $this->relatedClassQualifiedName;
    }
}
