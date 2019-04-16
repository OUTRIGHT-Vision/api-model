<?php
namespace OUTRIGHTVision\Relationships\Traits;

use OUTRIGHTVision\ApiModel;
use OUTRIGHTVision\Relationships\BelongsTo;
use OUTRIGHTVision\Relationships\HasMany;
use OUTRIGHTVision\Relationships\HasOne;

trait HasRelationships
{
    protected function hasMany(string $class, string $field): HasMany
    {
        // We include the name
        $this->relationships[] = debug_backtrace()[1]['function'];

        return new HasMany(collect($this->get($field . '.data', $this->get($field)))->map(function ($entity) use ($class) {
            return new $class($entity);
        }));
    }

    protected function hasOne(string $class, string $field): HasOne
    {
        // We include the name
        $this->relationships[] = debug_backtrace()[1]['function'];

        $result = new HasOne(new $class($this->get($field . '.data', $this->get($field))));
        
        return $result->setRelateClassQualifiedName($class);
    }

    protected function belongsTo(string $class, string $field): BelongsTo
    {
        // We include the name
        $this->relationships[] = debug_backtrace()[1]['function'];

        $result = new BelongsTo(new $class($this->get($field . '.data', $this->get($field))));

        return $result->setRelateClassQualifiedName($class);
    }
}
