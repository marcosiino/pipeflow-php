<?php
namespace marcosiino\pipeflow\Stages\SetValueStage;
use marcosiino\pipeflow\Interfaces\AbstractStageFactory;
use marcosiino\pipeflow\Utils\Helpers;
use marcosiino\pipeflow\Core\StageConfiguration\StageConfiguration;

class SetValueStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Sets the specified value into a context's parameter with the specified name.";

        // Setup Parameters
        $setupParams = array(
            "parameterName" => "The name of the parameter to which the fixed value is assigned.",
            "parameterValue" => "The fixed value to assign to the specified parameter.",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "A parameter with the name specified using the *parameterName* setup parameter, with the fixed value provided in the *parameterValue* setup parameter.",
        );

        return new StageDescriptor("SetValue", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new SetValueStage($configuration);
    }
}