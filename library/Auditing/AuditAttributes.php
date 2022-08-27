<?php

namespace Library\Auditing;

use Closure;
use Illuminate\Support\Collection;
use Library\Auditing\Changes\ArrayChange;
use Library\Auditing\Changes\BooleanChange;
use Library\Auditing\Changes\Change;
use Library\Auditing\Changes\MultilineChange;
use Library\Auditing\Changes\RelationshipChange;
use Library\Auditing\Contracts\LinkableAuditModel;

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
     *
     * @return AuditAttributes
     */
    public static function build(): AuditAttributes
    {
        return new AuditAttributes();
    }

    /**
     * Add multiple attributes
     *
     * @param  Closure  $typeGenerator a closure which generates new class instances
     * @param  string  ...$attributes one or more attribute names
     * @return void
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
     * @param  string  ...$attributes
     * @return $this
     */
    public function add(string ...$attributes): self
    {
        $this->addAttributes(fn () => new Change(), ...$attributes);

        return $this;
    }

    /**
     * Add a foreign key attribute to the audit
     *
     * @param  string  $attribute attribute name
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
     * @param  string  ...$attributes
     * @return $this
     */
    public function addArray(string ...$attributes): self
    {
        $this->addAttributes(fn () => new ArrayChange(), ...$attributes);

        return $this;
    }

    /**
     * Add boolean attributes to the audit
     *
     * @param  string  ...$attributes
     * @return $this
     */
    public function addBoolean(string ...$attributes): self
    {
        $this->addAttributes(fn () => new BooleanChange(), ...$attributes);

        return $this;
    }

    /**
     * Add long text attributes to the audit
     *
     * @param  string  ...$attributes
     * @return $this
     */
    public function addMultiline(string ...$attributes): self
    {
        $this->addAttributes(fn () => new MultilineChange(), ...$attributes);

        return $this;
    }

    /**
     * Get an array of attribute names
     *
     * @return array
     */
    public function getAttributeNames(): array
    {
        return $this->attributes->keys()->all();
    }

    /**
     * Get the Change instance associated with an attribute
     *
     * @param  string  $attribute
     * @return Change
     */
    public function getChangeType(string $attribute): Change
    {
        return $this->attributes->get($attribute);
    }
}
