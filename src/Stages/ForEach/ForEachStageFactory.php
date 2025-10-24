<?php
namespace Marcosiino\Pipeflow\Stages\ForEach;

use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;

class ForEachStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Allows to implement a for-each loop in the pipeline flow. Must contain at least a &lt;do&gt;&lt;/do&gt; block with the stage(s) to execute for each item in the collection. For each iteration a currentItem context parameter will be set with the value of the current item being processed, plus currentItem_index will contain the current index.";


        // Setup Parameters
        $setupParams = array(
            "collection" => "An array or a context parameter reference to an array to iterate over.",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
        );

        return new StageDescriptor("ForEach", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        return new ForEachStage($configuration);
    }
}