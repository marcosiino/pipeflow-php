<?php

namespace Marcosiino\Pipeflow\Stages\RandomArrayItem;

use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Exceptions\StageConfigurationException;

class RandomArrayItemStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Pick and return a random array item from the specified array context parameter.";

        // Setup Parameters
        $setupParams = array(
            "arrayParameterName" => "(required) The name of the array context parameter.",
            "resultTo" => "The name of the context parameter where the random picked element is saved.",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "A random item from the specified array, which is saved into the resultTo context parameter.",
        );

        return new StageDescriptor("RandomArrayItem", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new RandomArrayItemStage($configuration);
    }
}