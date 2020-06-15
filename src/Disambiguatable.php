<?php

namespace Adoxography\Disambiguatable;

use Illuminate\Database\Eloquent\Model;
use Adoxography\Disambiguatable\Disambiguation;

trait Disambiguatable
{
    public function initializeDisambiguatable()
    {
        if (!isset($this->disambiguatableFields)) {
            throw new \InvalidArgumentException('Models using DisambiguatableTrait must define disambiguatableFields.');
        }

        $this->with[] = 'disambiguation';
    }

    public static function bootDisambiguatable()
    {
        static::saved(function (Model $model) {
            static::renumberDisambiguators($model);
        });

        static::deleting(function (Model $model) {
            $model->disambiguation->delete();
        });

        static::deleted(function (Model $model) {
            static::renumberDisambiguators($model);
        });
    }

    public function getDisambiguatorAttribute()
    {
        return $this->disambiguation->disambiguator;
    }

    public function disambiguation()
    {
        return $this->morphOne(Disambiguation::class, 'disambiguatable')->withDefault([
            'disambiguatable_id' => null,
            'disambiguator' => null
        ]);
    }

    public function disambiguationIsSet(): bool
    {
        return !is_null($this->disambiguation->disambiguatable_id);
    }

    private static function renumberDisambiguators(Model $model)
    {
        $existing = $model->disambiguatableDuplicates();

        if ($existing->count() === 1) {
            // If there's only one match, it doesn't need to be disambiguated. Delete it.
            $existing->first()->disambiguation->delete();
            return;
        }

        $existing->each(function ($item, $key) {
            if ($item->disambiguator !== $key) {
                $method = $item->disambiguationIsSet() ? 'update' : 'create';

                $item->disambiguation()->$method([
                    'disambiguatable_type' => get_class($item),
                    'disambiguatable_id' => $item->id,
                    'disambiguator' => $key
                ]);
            }
        });
    }

    private function disambiguatableDuplicates()
    {
        $clauses = [];
        foreach ($this->disambiguatableFields as $field) {
            $clauses[$field] = $this->$field;
        }

        return $this->where($clauses)->get();
    }
}
