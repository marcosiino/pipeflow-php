<?php

namespace Pipeline\Core\StageConfiguration;

class ReferenceStageSetting
{
    private ReferenceStageSettingType $type;
    private string $name;
    private string $referencedContextParam;
    private int|String|null $referencedKeyPath;

    public function __construct(ReferenceStageSettingType $type, string $name, string $referencedContextParam, int|String|null $referencedKeyPath = null) {
        $this->type = $type;
        $this->name = $name;
        $this->referencedContextParam = $referencedContextParam;
        $this->referencedKeyPath = $referencedKeyPath;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getReferencedContextParam(): string {
        return $this->referencedContextParam;
    }

    /**
     * Returns the value of the referenced context parameter from the context passed as argument
     *
     * @return mixed|null
     * @throws PipelineExecutionException
     */
    public function getValue(PipelineContext $context): mixed {
        $contextParam = $context->getParameter($this->referencedContextParam);
        if(is_null($contextParam)) {
            return null; // Returns null if the context parameter doesn't exists
        }

        switch($this->type) {
            case ReferenceStageSettingType::plain:
                return $contextParam;
            case ReferenceStageSettingType::keypath:
                if(!is_array($contextParam)) {
                    throw new PipelineExecutionException("ReferenceStageSetting with name `$this->name` has a `keypath` reference to the context parameter named `$this->referencedContextParam` but it is not an array.");
                }

                if(is_null($this->referencedKeyPath)) {
                    throw new PipelineExecutionException("ReferenceStageSetting with name `$this->name` has an `keypath` reference to the context parameter named `$this->referencedContextParam` but the keypath is null.");
                }

                $value = Helpers::getArrayItemAtPath($contextParam, $this->referencedKeyPath);
                if(!is_null($value)) {
                    return $value;
                }
                else {
                    throw new PipelineExecutionException("ReferenceStageSetting with name `$this->name` is referencing a keypath which doesn't exists on context parameter named: `$this->referencedContextParam`. Keypath: `$this->referencedKeyPath` doesn't exists");
                }
            case ReferenceStageSettingType::last:
                if(!is_array($contextParam)) {
                    throw new PipelineExecutionException("ReferenceStageSetting with name `$this->name` has a `keypath` reference to the context parameter named `$this->referencedContextParam` but it is not an array.");
                }
                return end($contextParam);
        }

        return null;
    }
}