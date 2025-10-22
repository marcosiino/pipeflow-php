<?php

namespace Marcosiino\Pipeflow\Stages\JSONEncode;

use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Exceptions\PipelineExecutionException;

class JSONEncodeStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $associativeArrayParamName = $this->stageConfiguration->getSettingValue("associativeArray", $context, true);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        $associativeArray = $context->getParameter($associativeArrayParamName);
        if(!isset($associativeArray)) {
            throw new PipelineExecutionException("The specified context parameter `$associativeArrayParamName` does not exists in the context");
        }
        if(!is_array($associativeArray)) {
            throw new PipelineExecutionException("The specified context parameter `$associativeArrayParamName` is not an array.");
        }

        //Output
        $encodedJSON = json_encode($associativeArray);
        if($encodedJSON == false) {
            throw new PipelineExecutionException("JSON Encoding failed");
        }

        $context->setParameter($resultTo, $encodedJSON);
        return $context;
    }
}