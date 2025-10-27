<?php

namespace Marcosiino\Pipeflow\Stages\SumOperation;

use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;

class SumOperationStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct(StageConfiguration $stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        $operandA = $this->stageConfiguration->getSettingValue("operandA", $context, true);
        $operandB = $this->stageConfiguration->getSettingValue("operandB", $context, true);
        $resultParameter = $this->stageConfiguration->getSettingValue("resultTo", $context, false, "SUM_RESULT");

        // --- 1) Both operands are NOT arrays: handle numeric vs string ---
        if (!is_array($operandA) && !is_array($operandB)) {

            // If *both* operands are numeric-like (int/float or numeric strings) => perform numeric sum
            if ($this->isNumericLike($operandA) && $this->isNumericLike($operandB)) {
                // Explicit numeric cast: in PHP, $x + 0 safely forces numeric type
                $a = $operandA + 0;
                $b = $operandB + 0;

                // If both are int-like you may return int, otherwise float (optional)
                $context->setParameter($resultParameter, $a + $b);
            } else {
                // Otherwise, fallback: string concatenation (intended behavior)
                $context->setParameter($resultParameter, (string)$operandA . (string)$operandB);
            }
        }

        // --- 2) Both operands are arrays: merge preserving numeric keys at the end ---
        else if (is_array($operandA) && is_array($operandB)) {
            $context->setParameter($resultParameter, array_merge($operandA, $operandB));
        }

        // --- 3) One operand is an array and the other is scalar: push scalar to array ---
        else if (is_array($operandA) && !is_array($operandB)) {
            $result = $operandA;
            $result[] = $operandB;
            $context->setParameter($resultParameter, $result);
        } else if (is_array($operandB) && !is_array($operandA)) {
            $result = $operandB;
            $result[] = $operandA;
            $context->setParameter($resultParameter, $result);
        }

        return $context;
    }

    /**
     * Returns true if the value is a number or a valid numeric string (whitespace allowed).
     * Prevents treating null/array/object/bool as numbers.
     */
    private function isNumericLike($value): bool
    {
        if (is_int($value) || is_float($value)) {
            return true;
        }
        if (!is_string($value)) {
            return false; // exclude null, bool, array, object
        }
        $s = trim($value);
        if ($s === '') {
            return false;
        }
        return is_numeric($s); // also recognizes "2", "2.5", "-3", "2e3"
    }

}