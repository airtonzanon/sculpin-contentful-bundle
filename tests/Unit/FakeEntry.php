<?php
declare(strict_types=1);

namespace AirtonZanon\SculpinContentfulBundle\Tests\Unit;

use Contentful\Core\Resource\EntryInterface;
use Contentful\Delivery\Resource\LocalizedResource;
use Contentful\Delivery\SystemProperties\Entry as SystemProperties;

class FakeEntry extends LocalizedResource implements EntryInterface, \ArrayAccess
{
    protected array $fields;

    protected SystemProperties $sys;

    public function __construct(array $fields, array $sys)
    {
        $this->sys = new SystemProperties($sys);
        $this->fields = $fields;

        parent::__construct($fields);
    }

    public function getSystemProperties(): SystemProperties
    {
        return $this->sys;
    }

    public function jsonSerialize(): array
    {
        $fields = new \stdClass();
        foreach ($this->fields as $name => $value) {
            $fields->$name = $value;
        }

        return [
            'sys' => $this->sys,
            'fields' => $fields,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name)
    {
        return $this->fields[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($name)
    {
        throw new \LogicException('Entry class does not support this.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($name, $value)
    {
        throw new \LogicException('Entry class does not support this.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($name)
    {
        throw new \LogicException('Entry class does not support this.');
    }
}
