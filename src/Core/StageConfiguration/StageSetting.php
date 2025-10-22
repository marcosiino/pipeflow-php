<?php
namespace Pipeline\Core\StageConfiguration;

/**
 * Represents a setting parameter for a stage with a fixed value
 */

class StageSetting
{
    /**
     * The name of the setting
     * @var string
     */
    private string $name;
    /**
     * The value of the setting
     * @var mixed
     */
    private mixed $value;

    /**
     * @param string $name - The name of the setting
     * @param $value - The value of the setting
     */
    public function __construct(string $name, $value) {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Returns the name of the setting
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Returns the value of the setting
     *
     * @return mixed
     */
    public function getValue(): mixed {
        return $this->value;
    }
}