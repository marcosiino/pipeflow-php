<?php

namespace Marcosiino\Pipeflow\Stages\JSONDecode;

use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Exceptions\StageConfigurationException;

class JSONDecodeStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Decodes a JSON encoded string into an associative array saved as context parameter.";

        // Setup Parameters
        $setupParams = array(
            "jsonString" => "(required) The string containing the JSON to decode.",
            "resultTo" => "(required, array) The output context parameter where the decoded associative array is saved",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "The json decoded as associative array saved in the context parameter specified in resultTo.",
        );

        return new StageDescriptor("JSONDecode", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new JSONDecodeStage($configuration);
    }
}