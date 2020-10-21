<?php

namespace Rokka\Client\Core;

abstract class AbstractStack
{
    /**
     * @var string|null Name of the stack
     */
    public $name;

    /**
     * @var StackOperation[] Collection of stack operations that this stack has
     */
    public $stackOperations;

    /**
     * @var array<bool|float|int|string> Array of stack options that this stack has
     */
    public $stackOptions;

    /**
     * @var array<bool|float|int|string> Array of stack variables that this stack has
     */
    public $stackVariables = [];

    /**
     * @param string|null $name
     */
    public function __construct($name = null, array $stackOperations = [], array $stackOptions = [], array $stackVariables = [])
    {
        $this->name = $name;
        $this->stackOperations = $stackOperations;
        $this->stackOptions = $stackOptions;
        $this->stackVariables = $stackVariables;
    }

    /**
     * Get name of stack for url.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @since 1.1.0
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return StackOperation[]
     */
    public function getStackOperations()
    {
        return $this->stackOperations;
    }

    /**
     * Returns all operations matching name.
     *
     * @since 1.2.0
     *
     * @param string $name operation name
     *
     * @return StackOperation[]
     */
    public function getStackOperationsByName($name)
    {
        $stackOperations = [];
        foreach ($this->stackOperations as $stackOperation) {
            if ($stackOperation->name === $name) {
                $stackOperations[] = $stackOperation;
            }
        }

        return $stackOperations;
    }

    /**
     * @since 1.1.0
     *
     * @param StackOperation[] $operations
     *
     * @return self
     */
    public function setStackOperations(array $operations)
    {
        $this->stackOperations = [];
        foreach ($operations as $operation) {
            $this->addStackOperation($operation);
        }

        return $this;
    }

    /**
     * Adds a StackOperation to the list of existing Stack Operations.
     *
     * @since 1.1.0
     *
     * @return self
     */
    public function addStackOperation(StackOperation $stackOperation)
    {
        $this->stackOperations[] = $stackOperation;

        return $this;
    }

    /**
     * @return array<bool|float|int|string>
     */
    public function getStackOptions()
    {
        return $this->stackOptions;
    }

    /**
     * @since 1.1.0
     *
     * @param array<bool|float|int|string> $options
     *
     * @return self
     */
    public function setStackOptions(array $options)
    {
        $this->stackOptions = $options;

        return $this;
    }

    /**
     * @since 1.10.0
     *
     * @return array<bool|float|int|string>
     */
    public function getStackVariables()
    {
        return $this->stackVariables;
    }

    /**
     * @since 1.10.0
     *
     * @param array<bool|float|int|string> $variables
     *
     * @return self
     */
    public function setStackVariables(array $variables)
    {
        $this->stackVariables = $variables;

        return $this;
    }

    /**
     * Sets a single Stack option to the list of existing Stack options.
     *
     * @since 1.1.0
     *
     * @param string                $key
     * @param bool|float|int|string $value
     *
     * @return self
     */
    public function addStackOption($key, $value)
    {
        $this->stackOptions[$key] = $value;

        return $this;
    }
}
