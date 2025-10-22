<?php

namespace Marcosiino\Pipeflow\Stages\SumOperation;

use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Exceptions\StageConfigurationException;

class SumOperationStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Sums (scalars), merges (arrays or array+scalar), or concatenates (strings or strings+scalars) two context's parameters and stores the result into the specified context's parameter. Details about the operation: If both are scalars numbers, a simple sum between two scalars is performed, but if one of those is a string and the other one is a scalar, a concatenation is performed. If both are arrays, the two arrays are merged together. If one of the operand is an array and the other is a scalar, the scalar operand is added to the array.";

        // Setup Parameters
        $setupParams = array(
            "operandA" => "The name of the context's parameter to be used as the first operand of the operation",
            "operandB" => "The name of the context's parameter to be used as the second operand of the operation",
            "resultTo" => "The name of the context's parameter to be used to store the result of the operation",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "SUM_RESULT" => "The result of the operation. The returned type depends on the given operands types, for example, if one of them is an array, an array type is returned. If resultTo is set, the result of the operation is written in the context parameter specified in that setting instead.",
        );

        return new StageDescriptor("SumOperation", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new SumOperationStage($configuration);
    }
}