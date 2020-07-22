<?php
/**
 * Handles the trait that can be assigned to models to make them disambiguatable
 *
 * PHP version 7
 *
 * @category Trait
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */

namespace Adoxography\Disambiguatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Adoxography\Disambiguatable\Disambiguation;

/**
 * Makes a model disambiguatable
 *
 * Models that use this trait must define a $disambiguatableFields property, which
 * contains at least one name of a column in the table the model references.
 * Whenever a model that is identical to another in respect to its
 * $disambiguatableFields is saved, a Disambiguation is saved to the database
 * referencing each duplicated model.
 *
 * The disambiguator property gives back a number that indicates which disambiguated
 * model the current model is. By default, it will give back null if there are no
 * duplicates, but will give back 0 instead if $alwaysDisambiguate is set to true.
 *
 * @category Trait
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */
trait Disambiguatable
{
    /**
     * Callback function called when a model that uses this trait is initialized
     *
     * @return void
     */
    public function initializeDisambiguatable(): void
    {
        if (!isset($this->disambiguatableFields)) {
            throw new \InvalidArgumentException(
                // phpcs:ignore
                'Models using DisambiguatableTrait must define disambiguatableFields.'
            );
        }

        $this->with[] = 'disambiguation';
    }

    /**
     * Callback function called by models that use this trait
     *
     * @return void
     */
    public static function bootDisambiguatable(): void
    {
        static::saved(
            function (Model $model) {
                static::_renumberDisambiguators($model);
            }
        );

        static::deleting(
            function (Model $model) {
                $model->disambiguation->delete();
            }
        );

        static::deleted(
            function (Model $model) {
                static::_renumberDisambiguators($model);
            }
        );
    }

    /**
     * Retrieves the disambiguator for the model
     *
     * Called when the `disambiguator` property is accessed
     *
     * @return ?int The model's disambiguator
     */
    public function getDisambiguatorAttribute(): ?int
    {
        return $this->disambiguation->disambiguator;
    }

    /**
     * Defines the relationship between the model and its disambiguation model
     *
     * @return Relation
     */
    public function disambiguation(): Relation
    {
        return $this->morphOne(Disambiguation::class, 'disambiguatable')
            ->withDefault(
                [
                    'disambiguatable_id' => null,
                    'disambiguator' => isset($this->alwaysDisambiguate) ? 0 : null
                ]
            );
    }

    /**
     * Determines if the disambiguator was explicitly set from the database or
     * was inferred
     *
     * @return bool true if the disambiguator was explicitly set from the database
     */
    public function disambiguationIsSet(): bool
    {
        return !is_null($this->disambiguation->disambiguatable_id);
    }

    /**
     * Retrieves all of the models that match the current model, with regards to its
     * $disambiguatableFields
     *
     * @return Collection
     */
    public function disambiguatableDuplicates(): Collection
    {
        $clauses = [];
        foreach ($this->disambiguatableFields as $field) {
            $clauses[$field] = $this->$field;
        }

        return $this->where($clauses)->get();
    }

    /**
     * Re-evaluates all of the models that match the $disambiguatableFields of
     * $model
     *
     * @param Model $model The model to base the re-evaluation on
     *
     * @return void
     */
    private static function _renumberDisambiguators(Model $model): void
    {
        $existing = $model->disambiguatableDuplicates();

        if ($existing->count() === 1) {
            // If there's only one match, it doesn't need to be disambiguated.
            // Delete it.
            $existing->first()->disambiguation->delete();
            return;
        }

        $existing->each(
            function ($item, $key) {
                if ($item->disambiguator !== $key) {
                    $method = $item->disambiguationIsSet() ? 'update' : 'create';

                    $item->disambiguation()->$method(
                        [
                            'disambiguatable_type' => get_class($item),
                            'disambiguatable_id' => $item->id,
                            'disambiguator' => $key
                        ]
                    );
                }
            }
        );
    }
}
