<?php
namespace Marcosiino\Pipeflow\Stages\ArrayCount;

use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;

class ArrayCountStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Counts the items in the specified array context parameter.";

        // Setup Parameters
        $setupParams = array(
            "arrayParameterName" => "(required) The name of the context parameter which contains the array of which items will be counted.",
            "resultTo" => "(required) The output context parameter where the item count is saved",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "The array items count is saved into the context parameter specified in resultTo setting.",
        );

        return new StageDescriptor("ArrayCount", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new ArrayCountStage($configuration);
    }
}