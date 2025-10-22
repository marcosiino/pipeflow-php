<?php

namespace Marcosiino\Pipeflow\Stages\ExplodeString;

use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Exceptions\PipelineExecutionException;

class ExplodeStringStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $inputString = $this->stageConfiguration->getSettingValue("inputString", $context, true);
        $separator = $this->stageConfiguration->getSettingValue("separator", $context, true);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        if($separator == "") {
            throw new PipelineExecutionException("separator must be != empty string");
        }

        //Output
        $resultArray = explode($separator, $inputString);
        if(!is_array($resultArray)) {
            throw new PipelineExecutionException("String explosion failed");
        }

        $context->setParameter($resultTo, $resultArray);
        return $context;
    }
}