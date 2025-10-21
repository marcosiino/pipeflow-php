<?php
namespace marcosiino\pipeflow\Stages\SetValue;
use marcosiino\pipeflow\Interfaces\AbstractPipelineStage;

class SetValueStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $parameterName = $this->stageConfiguration->getSettingValue("parameterName", $context, true);
        $parameterValue = $this->stageConfiguration->getSettingValue("parameterValue", $context, true);

        //Output
        $context->setParameter($parameterName, $parameterValue);
        return $context;
    }
}