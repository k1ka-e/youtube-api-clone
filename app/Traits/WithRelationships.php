<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait WithRelationships
{

    public function scopeWithRelationships($query, $relationships)
    {
        return $query->with($this->validRelationship($relationships));
    }

    public function loadRelationships($relationships)
    {
        return $this->load($this->validRelationship($relationships));
    }

    public function validRelationship($relationships)
    {
       return collect($relationships)
            ->map(fn(string $relationships): array => explode('.', $relationships))
            ->filter(fn(array $relationships): bool => (new static)->hasRelationships($relationships))
            ->map(fn(array $relationships): string => implode('.', $relationships))
            ->all();
    }

    public function hasRelationships($relationships)
    {
        return (bool)collect($relationships)
            ->reduce(fn($model, $relationship) => optional($model)->hasRelationship($relationship), $this);
    }

    public function hasRelationship(string $relationship)
    {
        return $this->isValidRelationship($relationship) ? $this->$relationship()->getRelated() : null;
    }

    public function isValidRelationship(string $relationship)
    {
        return method_exists($this, $relationship) && in_array($relationship, static::$relationships ?? []);
    }
}
