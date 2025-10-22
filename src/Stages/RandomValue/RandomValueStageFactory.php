<?php
namespace Marcosiino\Pipeflow\Stages\RandomValue;

use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Exceptions\StageConfigurationException;

class RandomValueStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Generates a random number.";

        // Setup Parameters
        $setupParams = array(
            "parameterName" => "(required) The name of the context parameter where the generated random value is saved.",
            "minValue" => "The minimum random value (included).",
            "maxValue" => "The maximum random value (not included).",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "A random value which is saved into the context (the context parameter name is specified in parameterName).",
        );

        return new StageDescriptor("RandomValue", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new RandomValueStage($configuration);
    }
}