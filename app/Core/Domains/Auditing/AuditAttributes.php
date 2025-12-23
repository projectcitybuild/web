<?php

namespace App\Core\Domains\Auditing;

use App\Core\Domains\Auditing\Changes\ArrayChange;
use App\Core\Domains\Auditing\Changes\BooleanChange;
use App\Core\Domains\Auditing\Changes\Change;
use App\Core\Domains\Auditing\Changes\MultilineChange;
use App\Core\Domains\Auditing\Changes\RelationshipChange;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use Closure;
use Illuminate\Support\Collection;

class AuditAttributes
{
    /**
     * @var Collection<string, Change>
     */
    private Collection $attributes;

    private function __construct()
    {
        $this->attributes = collect();
    }

    /**
     * Construct a new attribute builder
     */
    public static function build(): AuditAttributes
    {
        return new AuditAttributes;
    }

    /**
     * Add multiple attributes
     *
     * @param  Closure  $typeGenerator  a closure which generates new class instances
     * @param  string  ...$attributes  one or more attribute names
     */
    private function addAttributes(Closure $typeGenerator, string ...$attributes): void
    {
        foreach ($attributes as $attribute) {
            $this->attributes->put($attribute, $typeGenerator());
        }
    }

    /**
     * Add attributes which will be displayed unprocessed
     *
     * @return $this
     */
    public function add(string ...$attributes): self
    {
        $this->addAttributes(fn () => new Change, ...$attributes);

        return $this;
    }

    /**
     * Add a foreign key attribute to the audit
     *
     * @param  string  $attribute  attribute name
     * @param  class-string<LinkableAuditModel>  $model
     * @return $this
     */
    public function addRelationship(string $attribute, string $model): self
    {
        $this->addAttributes(fn () => new RelationshipChange($model), $attribute);

        return $this;
    }

    /**
     * Add array attributes to the audit
     *
     * @return $this
     */
    public function addArray(string ...$attributes): self
    {
        $this->addAttributes(fn () => new ArrayChange, ...$attributes);

        return $this;
    }

    /**
     * Add boolean attributes to the audit
     *
     * @return $this
     */
    public function addBoolean(string ...$attributes): self
    {
        $this->addAttributes(fn () => new BooleanChange, ...$attributes);

        return $this;
    }

    /**
     * Add long text attributes to the audit
     *
     * @return $this
     */
    public function addMultiline(string ...$attributes): self
    {
        $this->addAttributes(fn () => new MultilineChange, ...$attributes);

        return $this;
    }

    /**
     * Get an array of attribute names
     */
    public function getAttributeNames(): array
    {
        return $this->attributes->keys()->all();
    }

    /**
     * Get the Change instance associated with an attribute.
     * If this attribute hasn't been set, returns a basic Change.
     */
    public function getChangeType(string $attribute): Change
    {
        return $this->attributes->get($attribute) ?? new Change;
    }
}
