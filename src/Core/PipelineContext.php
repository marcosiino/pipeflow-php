<?php
namespace Marcosiino\Pipeflow\Core;

/**
 * A Pipeline Context, which represents the full state of a pipeline at a given moments, with all its parameters and values at that point.
 */
class PipelineContext
{
    /**
     * @var array an associative array of string => mixed which maps parameters name to their values
     */
    private array $context = array();

    /**
     * Sets the parameter with the given name. If a parameter with the given name already exists in the context, its value is replaced
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setParameter(string $name, mixed $value): void {
        $this->context[$name] = $value;
    }

    /**
     * Deletes the given parameter from the context.
     *
     * @param string $name
     * @return void
     */
    public function deleteParameter(string $name): void {
        unset($this->context[$name]);
    }

    /**
     * Gets the parameter with the given name.
     *
     * @param string $name - The name of the context parameter to be returned.
     * @return mixed|null - Returns the context parameter's value or null if it doesn't exist in the context.
     */
    public function getParameter(string $name): mixed {
        if(!array_key_exists($name, $this->context)) {
            return null;
        }
        return $this->context[$name];
    }

    /**
     * Checks if the given parameter exists in the context
     *
     * @param string $name
     * @return bool Returns true if the parameter exists, false otherwise
     */
    public function checkParameterExists(string $name): bool {
        return array_key_exists($name, $this->context);
    }

    /**
     * Returns a html which visual represents the current context status
     * @return String
     */
    public function getHTMLDescription(): String {
        $html = "<div class=\"pipeline-context\">";
        $html .= "<table style='border: 1px solid black;'>";
        foreach($this->context as $paramName => $value) {
            $html .= "<tr style='border: 1px solid black;'>";
            //Parameter name column
            $html .= "<td style='border: 1px solid black;padding: 1em;'><pre><strong><span style='text-decoration: underline;'>$paramName<span></strong></pre></td>";
            //Parameter value column
            $html .= "<td style='border: 1px solid black;padding: 1em;'><ul>";
            if(!is_array($value)) {
                $html .= "<li><pre>" . htmlspecialchars($value) . "</pre></li>";
            }
            else {
                //Format the array
                $html .= "<li><pre>" . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . "</pre></li>";
            }
            $html .= "</ul></td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        $html .= "</div>";
        return $html;
    }

}