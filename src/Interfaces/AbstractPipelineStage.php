<?php
namespace Marcosiino\Pipeflow\Interfaces;

use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Exceptions\PipelineExecutionException;

/**
 * Represents an abstract PipelineStage
 */
abstract class AbstractPipelineStage
{
    protected array $subStagesBlocks = []; // Associative array of sub stages blocks where the key is the block name and the value is an array of stages inside that block
    protected array $allowedSubStagesBlocks = []; // List of allowed sub stages blocks names
    /**
     * Executes the pipeline stage with the context passed as argument, and returns the output context
     * @param PipelineContext $context
     * @return PipelineContext
     * @throws PipelineExecutionException
     */
    abstract public function execute(PipelineContext $context): PipelineContext;
    
    public function addSubStagesBlock(string $blockName, array $stages): void
    {
        if(!in_array($blockName, $this->allowedSubStagesBlocks)) {
            throw new PipelineExecutionException("This stage does not support a block of sub stages named '$blockName'");
        }
        $this->subStagesBlocks[$blockName] = $stages;
    }
}