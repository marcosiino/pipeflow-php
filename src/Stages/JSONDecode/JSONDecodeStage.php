<?php

namespace Marcosiino\Pipeflow\Stages\JSONDecode;

use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Exceptions\PipelineExecutionException;

class JSONDecodeStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $jsonString = $this->stageConfiguration->getSettingValue("jsonString", $context, true);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        //Output
        $resultArray = json_decode($jsonString, true);
        if(!is_array($resultArray)) {
            throw new PipelineExecutionException("JSON Decode failed");
        }

        $context->setParameter($resultTo, $resultArray);
        return $context;
    }
}