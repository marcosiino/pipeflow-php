<?php
namespace Marcosiino\Pipeflow\Stages\For;

use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;

class ForStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Allows to implement a for loop in the pipeline flow. Must contain at least a &lt;do&gt;&lt;/do&gt; block with the stage(s) to execute for each item in the loop. For each iteration the context parameter indicated in indexParameterName will be set with the value of the current index being processed.";


        // Setup Parameters
        $setupParams = array(
            "from" => "The starting index of the loop (inclusive).",
            "to" => "The ending index of the loop (exclusive).",
            "step" => "The step/increment for each iteration (default is 1).",
            "indexParameterName" => "The name of the context parameter to set with the current index (default is 'currentIndex').",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
        );

        return new StageDescriptor("For", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        return new ForStage($configuration);
    }
}