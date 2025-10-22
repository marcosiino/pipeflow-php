<?php
namespace Marcosiino\Pipeflow\Stages\ArrayPath;

use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;

class ArrayPathStageFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new ArrayPathStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Get an item from an array given the item path within the array in the format of a dot-separated string of keys. For example: 'key1.key2.key3' to access array['key1']['key2']['key3'].";
        $setupParameters = array(
            "array" => "The array.",
            "path" => "A dot-separated list of keys within the array which represents the path to reach the item.",
            "defaultValue" => "(Optional) If given path doesn't not exists (no item at the specified path), this value will be returned. If this parameter is not set, an error is thrown when executing the pipeline, if the specified path does not exists within the array.",
            "resultTo" => "The name of the context parameter where the item value is saved.",
        );

        $contextInputs = array();
        $contextOutputs = array(
            "" => "The parameter specified in the resultTo setting will be saved as the item retrieved from the array.",
        );

        return new StageDescriptor("ArrayPath", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}