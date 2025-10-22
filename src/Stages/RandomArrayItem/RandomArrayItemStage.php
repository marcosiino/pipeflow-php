<?php

namespace Marcosiino\Pipeflow\Stages\RandomArrayItem;

use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Exceptions\PipelineExecutionException;

class RandomArrayItemStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $arrayParameterName = $this->stageConfiguration->getSettingValue("arrayParameterName", $context, true);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        //Output
        $array = $context->getParameter($arrayParameterName);
        if(!is_array($array)) {
            throw new PipelineExecutionException("The context parameter specified in $arrayParameterName is not an array.");
        }

        $context->setParameter($resultTo, $array[array_rand($array)]);
        return $context;
    }
}