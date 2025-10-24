<?php
namespace Marcosiino\Pipeflow\Stages\If;

use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;

class IfStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;
    
    protected array $allowedSubStagesBlocks = ['then', 'else'];

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $leftOperand = $this->stageConfiguration->getSettingValue("leftOperand", $context, true);
        $operator = $this->stageConfiguration->getSettingValue("operator", $context, true);
        $rightOperand = $this->stageConfiguration->getSettingValue("rightOperand", $context, true);

        $condition = false;

        switch($operator) {
            case "equal":
                $condition = ($leftOperand == $rightOperand);
                break;
            case "notEqual":
                $condition = ($leftOperand != $rightOperand);
                break;
            case "greater":
                $condition = ($leftOperand > $rightOperand);
                break;
            case "less":
                $condition = ($leftOperand < $rightOperand);
                break;
            case "greaterOrEqual":
                $condition = ($leftOperand >= $rightOperand);
                break;
            case "lessOrEqual":
                $condition = ($leftOperand <= $rightOperand);
                break;
            case "contains":
                $condition = str_contains($leftOperand, $rightOperand);
                break;
            case "notContains":
                $condition = !str_contains($leftOperand, $rightOperand);
                break;
            case "caseInsensitiveContains":
                $condition = stripos($leftOperand, $rightOperand) !== false;
                break;
            case "caseInsensitiveNotContains":
                $condition = stripos($leftOperand, $rightOperand) === false;
                break;
            default:
                throw new \Marcosiino\Pipeflow\Exceptions\PipelineExecutionException("Invalid operator '$operator' in If Stage");
        }

        if ($condition) {
            if(!isset($this->subStagesBlocks['then']) || !is_array($this->subStagesBlocks['then']) || count($this->subStagesBlocks['then']) === 0) {   
                throw new \Marcosiino\Pipeflow\Exceptions\PipelineExecutionException("If stage must contain at least a stage in the 'then' block");
            }

            foreach ($this->subStagesBlocks['then'] as $stage) {
                // Execute each stage in the 'then' block
                $context = $stage->execute($context);
            }
        }
        else {
            if(isset($this->subStagesBlocks['else']) && is_array($this->subStagesBlocks['else']) && count($this->subStagesBlocks['else']) > 0) {
                foreach ($this->subStagesBlocks['else'] as $stage) {
                    // Execute each stage in the 'else' block
                    $context = $stage->execute($context);
                }
            }
        }
        
        //Output
        return $context;
    }
}