# Pipeflow PHP Library Documentation

**Note:** The following documentation has been written and is maintained with the support of AI. Please report any errors you encounter in the documentation instructions so that I can fix them manually.

## Table of Contents
- [Introduction](#introduction)
  - [Why Pipeflow?](#why-pipeflow)
  - [Sample Use Cases](#sample-use-cases)
- [Installation](#installation)
- [Quick Start](#quick-start)
  - [Bootstrapping the Stage Catalog](#bootstrapping-the-stage-catalog)
  - [Hello World Pipeline (Code)](#hello-world-pipeline-code)
  - [Hello World Pipeline (XML)](#hello-world-pipeline-xml)
- [Core Concepts](#core-concepts)
  - [Pipeline](#pipeline)
  - [Pipeline Context](#pipeline-context)
  - [Stages and Stage Factories](#stages-and-stage-factories)
  - [Stage Configuration](#stage-configuration)
- [Referencing Context Data](#referencing-context-data)
  - [String Placeholders (`%%name%%`)](#string-placeholders-name)
  - [Context References (`contextReference`)](#context-references-contextreference)
  - [Key Paths for Nested Structures](#key-paths-for-nested-structures)
  - [Working with Arrays, Lists, and Nested Objects](#working-with-arrays-lists-and-nested-objects)
- [Configuring Pipelines in PHP](#configuring-pipelines-in-php)
- [Configuring Pipelines with XML](#configuring-pipelines-with-xml)
  - [XML Schema Essentials](#xml-schema-essentials)
  - [Executing an XML Pipeline](#executing-an-xml-pipeline)
- [Stage Catalogue](#stage-catalogue)
  - [Control-Flow Stages](#control-flow-stages)
    - [`If` Stage](#if-stage)
    - [`ForEach` Stage](#foreach-stage)
    - [`For` Stage](#for-stage)
  - [ArrayCount](#arraycount)
  - [ArrayPath](#arraypath)
  - [Delay](#delay)
  - [ExplodeString](#explodestring)
  - [JSONDecode](#jsondecode)
  - [JSONEncode](#jsonencode)
  - [RandomArrayItem](#randomarrayitem)
  - [RandomValue](#randomvalue)
  - [SetValue](#setvalue)
  - [SumOperation](#sumoperation)
- [Creating Custom Stages](#creating-custom-stages)
- [Troubleshooting & Diagnostics](#troubleshooting--diagnostics)
- [Next Steps](#next-steps)

## Introduction
Pipeflow is a lightweight PHP pipeline engine that helps developers compose complex automation flows out of small, focused processing units called *stages*. A pipeline starts from a shared **context**, executes every configured stage in sequence (or conditionally, based on control-flow stages), and gradually enriches or mutates the context until the desired outcome is reached.

### Why Pipeflow?
- **Composable automation** &mdash; break down larger jobs into granular, reusable stages that can be wired together dynamically.
- **Consistent execution model** &mdash; every stage consumes a `PipelineContext` and returns it, so you can focus on the logic you need.
- **XML or PHP configuration** &mdash; author pipelines visually or programmatically depending on your workflow.
- **Extensible stage catalog** &mdash; implement your own stages for CMS, e-commerce, CRM, or internal systems and plug them into the runtime by registering a factory.

### Sample Use Cases
- **WordPress plug-ins** &mdash; wire Pipeflow into a WP plugin to automate editorial or e-commerce flows: fetch WooCommerce products, run price adjustments, trigger marketing APIs, or schedule content generation tasks. Pipelines can be launched manually (e.g., from an admin action) or scheduled through WP Cron.
- **Back-office jobs** &mdash; build nightly ETL-like workflows: read JSON feeds, transform them, iterate over records, and push them to REST endpoints.
- **Content generation** &mdash; orchestrate AI prompts, randomization, and templating by combining built-in stages with custom ones that integrate external APIs.

## Installation
Pipeflow is distributed as a Composer package. Require it in your project:

```bash
composer require marcosiino/pipeflow
```

Composer will autoload every class under the `Marcosiino\Pipeflow\` namespace thanks to the PSR-4 configuration in `composer.json`.

## Quick Start

### Bootstrapping the Stage Catalog
Pipeflow resolves stage instances through `StageFactory`. Before instantiating or parsing any pipeline, register the available factories by calling:

```php
use Marcosiino\Pipeflow\PipeFlow;

PipeFlow::registerStages();
```

You can also register only the factories you need by invoking `StageFactory::registerFactory()` manually.

### Hello World Pipeline (Code)
The following snippet uses the fluent PHP API to set a value and then JSON-decode it:

```php
use Marcosiino\Pipeflow\Core\Pipeline;
use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageSetting;
use Marcosiino\Pipeflow\Core\StageFactory;
use Marcosiino\Pipeflow\PipeFlow;

PipeFlow::registerStages();

$pipeline = new Pipeline(new PipelineContext());

// Stage 1: SetValue
$setConfig = new StageConfiguration();
$setConfig->addSetting(new StageSetting('parameterName', 'raw_json'));
$setConfig->addSetting(new StageSetting('parameterValue', '{"message":"Hello Pipeflow"}'));
$pipeline->addStage(StageFactory::instantiateStageOfType('SetValue', $setConfig));

// Stage 2: JSONDecode
$decodeConfig = new StageConfiguration();
$decodeConfig->addSetting(new StageSetting('jsonString', '%%raw_json%%'));
$decodeConfig->addSetting(new StageSetting('resultTo', 'data'));
$pipeline->addStage(StageFactory::instantiateStageOfType('JSONDecode', $decodeConfig));

$resultContext = $pipeline->execute();
var_dump($resultContext->getParameter('data')['message']); // "Hello Pipeflow"
```

### Hello World Pipeline (XML)
`Pipeline::setupWithXML()` accepts a validated XML configuration. Here is the equivalent pipeline:

```xml
<?xml version="1.0" encoding="utf-8"?>
<pipeline id="hello">
  <stages>
    <stage type="SetValue">
      <settings>
        <param name="parameterName">raw_json</param>
        <param name="parameterValue">{"message":"Hello Pipeflow"}</param>
      </settings>
    </stage>

    <stage type="JSONDecode">
      <settings>
        <param name="jsonString" contextReference="plain">raw_json</param>
        <param name="resultTo">data</param>
      </settings>
    </stage>
  </stages>
</pipeline>
```

Load and execute it:

```php
$xml = file_get_contents('hello_pipeline.xml');
$pipeline = new Pipeline();
PipeFlow::registerStages();
$pipeline->setupWithXML($xml);
$pipeline->execute();
```

## Core Concepts

### Pipeline
A `Pipeline` (`src/Core/Pipeline.php`) holds an ordered list of stages and a **context history**. When you call `execute()`, each stage receives the current `PipelineContext`, mutates it, and returns it. The pipeline tracks the resulting contexts after each stage, allowing you to inspect past states if needed.

### Pipeline Context
`PipelineContext` (`src/Core/PipelineContext.php`) is an associative map of parameters. Stages use it to read input data and to persist their output. Relevant API:

- `setParameter(string $name, mixed $value)` &mdash; add or update a context value.
- `getParameter(string $name): mixed|null` &mdash; retrieve a value or `null` if absent.
- `deleteParameter(string $name)` &mdash; remove a value.
- `checkParameterExists(string $name): bool` &mdash; test if a value exists.

Because the context is passed by reference across the pipeline, every change is immediately visible to subsequent stages.

### Stages and Stage Factories
A stage extends `AbstractPipelineStage` (`src/Interfaces/AbstractPipelineStage.php`). Each stage implements `execute(PipelineContext $context): PipelineContext` and may declare optional **sub-stage blocks** (for example, the `If` stage exposes `then` and `else`).

Factories implement `AbstractStageFactory` (`src/Interfaces/AbstractStageFactory.php`). They serve two purposes:

1. Provide metadata (`StageDescriptor`) about the stage (identifier, description, setup parameters, context I/O).
2. Instantiate concrete stage classes from a `StageConfiguration`.

Use `StageFactory::registerFactory()` to attach custom factories, and `StageFactory::instantiateStageOfType()` to resolve concrete stages.

### Stage Configuration
`StageConfiguration` (`src/Core/StageConfiguration/StageConfiguration.php`) stores typed stage settings. Each setting is either:

- A `StageSetting` (fixed value defined in XML or code), or
- A `ReferenceStageSetting` (value read dynamically from the context at runtime).

`StageConfiguration::getSettingValue($name, $context, $required, $default)` resolves the final value and performs placeholder expansion when appropriate, or if the parameter is a `contextReference` (see [Referencing Context Data](#referencing-context-data)), this function automatically resolves the reference and returns it's value by using the provided `$context`.

## Referencing Context Data
Stages often need to consume values that were produced earlier in the pipeline. Pipeflow provides two complementary approaches.

### String Placeholders (`%%name%%`)
Any string setting supports placeholders processed by `PlaceholderProcessor` (`src/Utils/PlaceholderProcessor.php`). The patterns are parsed by `InputParser` (`src/Utils/Parser/InputParser.php`). Supported syntax:

| Syntax | Meaning |
| --- | --- |
| `%%param%%` | Replaced with the value of the `param` context entry.
| `%%param[2]%%` | Replaced with the item at index `2` of the array stored in `param`.

Example:

```xml
<param name="parameterValue">Hello %%user_name%%!</param>
```

If the context contains `user_name = 'Marco'`, the resolved value becomes `"Hello Marco!"`.

### Context References (`contextReference`)
In some cases it's better to reference context parameters within stages using the `contextReference` attribute inside `<param>`:

```xml
<param name="jsonString" contextReference="plain">raw_json</param>
```

In the example below, the parameter `jsonString` of the stage is set with the value contained inside the raw_json context parameter (supposing it has been set previously by another stage). At runtime this becomes a `ReferenceStageSetting` (`src/Core/StageConfiguration/ReferenceStageSetting.php`).

Available reference types (see `ReferenceStageSettingType.php`):

- `plain` &mdash; inject the entire value of the referenced context parameter.
- `keypath` &mdash; select a nested key inside an array using dot-notation (See [Key Paths for Nested Structures](#key-paths-for-nested-structures))
- `last` &mdash; retrieve the last element of an array.

Programmatic usage mirrors the XML form:

```php
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSetting;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSettingType;

$config->addSetting(new ReferenceStageSetting(
    ReferenceStageSettingType::plain,
    'jsonString',
    'raw_json'
));
```

### Key Paths for Nested Structures
Key paths are dot-separated strings resolved through `Helpers::getArrayItemAtPath()` (`src/Utils/Helpers.php`). Example:

```xml
<param name="exampleInputParam" contextReference="keypath" keypath="data.2.attributes.details">
  decoded_payload
</param>
```

Given a context parameter `decoded_payload` that stores a nested associative array, Pipeflow navigates the path (`data ➝ 2 ➝ attributes ➝ details`) and injects the final nested structure in the stage input parameter `exampleInputParam`

If a key path does not exist, the engine raises a `PipelineExecutionException` so you can fail fast and surface a clear error.

### Working with Arrays, Lists, and Nested Objects
You can combine references and placeholders to compose complex values:

```xml
<!-- Extract a nested array -->
<param name="collection" contextReference="keypath" keypath="items.active">feed</param>

<!-- Capture the last element of a list -->
<param name="parameterValue" contextReference="last">recent_imports</param>

<!-- Build strings out of array data -->
<param name="parameterValue">Item %%currentItem[id]%% has status %%currentItem[status]%%</param>
```

When you need to pass literal arrays from XML, wrap values in `<item>` elements. Example, to configure a `SetValue` stage with a predefined list:

```xml
<param name="exampleInputParam">
  <item>pending</item>
  <item>processing</item>
  <item>completed</item>
</param>
```

`StageConfiguration` preserves these lists as PHP arrays and pass this array to the `exampleInputParam` input parameter of the stage

## Configuring Pipelines programmatically in PHP
Construct pipelines programmatically when you need full control or dynamic wiring:

1. Instantiate `Pipeline` with an optional starting `PipelineContext`.
2. Build `StageConfiguration` objects and populate them with `StageSetting` or `ReferenceStageSetting` instances.
3. Resolve stage instances through `StageFactory::instantiateStageOfType()`.
4. Register each stage via `Pipeline::addStage()`.
5. Run `Pipeline::execute()` and inspect the resulting context.

Example: iterate over a dynamic collection, compute totals, and save the payload:

```php
use Marcosiino\Pipeflow\PipeFlow;
use Marcosiino\Pipeflow\Core\Pipeline;
use Marcosiino\Pipeflow\Core\StageFactory;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageSetting;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSetting;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSettingType;

$pipeline = new Pipeline();
PipeFlow::registerStages();

// Seed the context with data by setting a context parameter order_ids with an array of order ids
$pipeline->getCurrentContext()->setParameter('order_ids', [101, 102, 103]);

// Use the ArrayCount stage to count the items of the array by storing the count in the orders_count context parameter
$countConfig = new StageConfiguration();
$countConfig->addSetting(new StageSetting('arrayParameterName', 'order_ids'));
$countConfig->addSetting(new StageSetting('resultTo', 'orders_count'));
$pipeline->addStage(StageFactory::instantiateStageOfType('ArrayCount', $countConfig));

$output_context = $pipeline->execute();
//$output_context will contain the following context parameters: order_ids and order_counts
```

Control-flow stages (`If`, `ForEach`, `For`) accept nested stages programmatically by calling `addSubStagesBlock($blockName, $stagesArray)` defined in `AbstractPipelineStage`.

## Configuring Pipelines with XML
XML configuration is the cleareast way to configure pipelines because, using custom defined xml nodes, you can better understand "visually" the structure of the pipeline and it's stage compared to configuring it programmatically. This allows also non-developers to configure or edit pipelines. 
The `PipelineXMLConfigurator` (`src/Utils/PipelineXMLConfigurator.php`) parses XML into `StageConfiguration` instances, validates the structure against an XSD, and wires sub-stage blocks automatically.

### XML Schema Essentials
The schema lives in `src/Utils/pipeline_schema_definition.xsd`. Key rules:

- Root element: `<pipeline id="...">` containing a single `<stages>` block. The pipeline id can be whatever you want.
- Each `<stage>` must declare a `type` attribute (which corresponds to the stage identifier. See [Stage Catalogue](#stage-catalogue) for all the available stage types) and a `<settings>` child.
- `<settings>` includes one or more `<param>` elements, which are the stage input parameters. Use `<item>` sub nodes to express arrays.
- Control-flow stages can declare `<then>`, `<else>`, and `<do>` blocks containing nested `<stage>` elements. See [Control-Flow Stages](#control-flow-stages) for more info.
- `contextReference` accepts `plain`, `keypath`, or `last`.

Following an example of a pipeline typical xml configuration:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<pipeline id="example-pipeline">
  <stages>
    <stage type="StageIdentifier">
      <settings>
        <param name="inputParam1">example vValue</param> <!-- example input -->
        <param name="inputParam2">another example value</param> <!-- example input -->
        <param name="resultTo">outputParam1</param> <!-- the name of the context parameter to which to store the output of the stage -->
      </settings>
    </stage>
    <stage type="AnotherStageIdentifier">
      <settings>
        <param name="inputParam1" contextReference="plain">outParam1</param> <!-- example input set to the value of the output of the previous stage -->
        <param name="inputParam2">hello world</param> <!-- another example input -->
        <param name="resultTo">finalOutput</param> <!-- the output of this stage is set to finalOutput context parameter -->
      </settings>
    </stage>
  </stages>
</pipeline>
```

### Executing an XML Pipeline

```php
use Marcosiino\Pipeflow\Core\Pipeline;
use Marcosiino\Pipeflow\PipeFlow;

PipeFlow::registerStages();
$pipeline = new Pipeline();
$xml = file_get_contents('pipeline.xml');
$pipeline->setupWithXML($xml); // Validates against the bundled XSD, instantiates and configures the stages
//...
$result = $pipeline->execute();
$result //is contains the processed context with all the context parameters set by the pipeline
```

Validation errors are collected and reported by `PipelineXMLConfigurator::validateXMLConfiguration()`. Ensure libxml errors are enabled (the configurator manages this automatically).

## Stage Catalogue
Below is the full list of built-in stages registered by `PipeFlow::registerStages()` (`src/PipeFlow.php`). Each entry includes description, parameters, and examples.

### Control-Flow Stages
Control-flow stages are the backbone of Pipeflow's DSL. They evaluate conditions, branch execution, or repeat sub-stages.

#### `If` Stage
- **Purpose**: Conditionally execute `then` or `else` blocks based on a comparison (`equal`, `notEqual`, `greater`, `less`, `greaterOrEqual`, `lessOrEqual`, `contains`, `notContains`, `caseInsensitiveContains`, `caseInsensitiveNotContains`).
- **Allowed sub-blocks**: `<then>`, `<else>`.

**XML Example**
```xml
<stage type="If">
  <settings>
    <param name="leftOperand" contextReference="plain">orders_count</param>
    <param name="operator">greater</param>
    <param name="rightOperand">0</param>
  </settings>
  <then>
    <stage type="SetValue">
      <settings>
        <param name="parameterName">status</param>
        <param name="parameterValue">Processing</param>
      </settings>
    </stage>
  </then>
  <else>
    <stage type="SetValue">
      <settings>
        <param name="parameterName">status</param>
        <param name="parameterValue">Idle</param>
      </settings>
    </stage>
  </else>
</stage>
```

**PHP Example**
```php
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSetting;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSettingType;

$ifConfig = new StageConfiguration();
$ifConfig->addSetting(new ReferenceStageSetting(
    ReferenceStageSettingType::plain,
    'leftOperand',
    'orders_count'
));
$ifConfig->addSetting(new StageSetting('operator', 'greater'));
$ifConfig->addSetting(new StageSetting('rightOperand', '0'));
$ifStage = StageFactory::instantiateStageOfType('If', $ifConfig);
$ifStage->addSubStagesBlock('then', [$approveStage]);
$ifStage->addSubStagesBlock('else', [$fallbackStage]);
$pipeline->addStage($ifStage);
```

#### `ForEach` Stage
- **Purpose**: Iterate over an array and execute the `do` block for each item. During iteration the context exposes `currentItem` and `currentItem_index`.
- **Allowed sub-blocks**: `<do>`.

**XML Example**
```xml
<stage type="ForEach">
  <settings>
    <param name="collection" contextReference="plain">products</param>
  </settings>
  <do>
    <stage type="SetValue">
      <settings>
        <param name="parameterName">last_product_id</param>
        <param name="parameterValue">%%currentItem[id]%%</param>
      </settings>
    </stage>
  </do>
</stage>
```

**PHP Example**
```php
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSetting;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSettingType;

$forEachConfig = new StageConfiguration();
$forEachConfig->addSetting(new ReferenceStageSetting(
    ReferenceStageSettingType::plain,
    'collection',
    'products'
));
$forEachStage = StageFactory::instantiateStageOfType('ForEach', $forEachConfig);
$forEachStage->addSubStagesBlock('do', [$processProductStage]);
$pipeline->addStage($forEachStage);
```

#### `For` Stage
- **Purpose**: Execute a classic counted loop from `from` (inclusive) to `to` (exclusive) with a configurable `step`. The current index is stored in the context (default `currentIndex`).
- **Allowed sub-blocks**: `<do>`.

**XML Example**
```xml
<stage type="For">
  <settings>
    <param name="from">0</param>
    <param name="to" contextReference="plain">orders_count</param>
    <param name="step">1</param>
    <param name="indexParameterName">loop_index</param>
  </settings>
  <do>
    <stage type="SetValue">
      <settings>
        <param name="parameterName">last_index</param>
        <param name="parameterValue">%%loop_index%%</param>
      </settings>
    </stage>
  </do>
</stage>
```

**PHP Example**
```php
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSetting;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSettingType;

$forConfig = new StageConfiguration();
$forConfig->addSetting(new StageSetting('from', 0));
$forConfig->addSetting(new ReferenceStageSetting(
    ReferenceStageSettingType::plain,
    'to',
    'orders_count'
));
$forConfig->addSetting(new StageSetting('step', 1));
$forConfig->addSetting(new StageSetting('indexParameterName', 'loop_index'));
$forStage = StageFactory::instantiateStageOfType('For', $forConfig);
$forStage->addSubStagesBlock('do', [$someStage]);
$pipeline->addStage($forStage);
```

### ArrayCount
- **Identifier**: `ArrayCount`
- **Purpose**: Count elements inside an array context parameter.
- **Settings**:
  - `arrayParameterName` (required) &mdash; name of the context array.
  - `resultTo` (required) &mdash; context key that stores the count.

**XML**
```xml
<stage type="ArrayCount">
  <settings>
    <param name="arrayParameterName">orders</param>
    <param name="resultTo">orders_count</param>
  </settings>
</stage>
```

**PHP**
```php
$config = new StageConfiguration();
$config->addSetting(new StageSetting('arrayParameterName', 'orders'));
$config->addSetting(new StageSetting('resultTo', 'orders_count'));
$stage = StageFactory::instantiateStageOfType('ArrayCount', $config);
```

### ArrayPath
- **Identifier**: `ArrayPath`
- **Purpose**: Retrieve a nested value from an array via dot-separated path and store it in the context.
- **Settings**:
  - `array` (required) &mdash; array to inspect (can reference the context).
  - `path` (required) &mdash; dot notation path.
  - `defaultPath` (optional) &mdash; fallback value when the path does not exist. *Note: the current implementation expects the setting to be named `defaultPath` even though earlier descriptors referenced `defaultValue`.*
  - `resultTo` (required) &mdash; destination context key.

**XML**
```xml
<stage type="ArrayPath">
  <settings>
    <param name="array" contextReference="plain">decoded_payload</param>
    <param name="path">data.0.attributes.details</param>
    <param name="resultTo">first_details</param>
  </settings>
</stage>
```

**PHP**
```php
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSetting;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSettingType;

$config = new StageConfiguration();
$config->addSetting(new ReferenceStageSetting(
    ReferenceStageSettingType::plain,
    'array',
    'decoded_payload'
));
$config->addSetting(new StageSetting('path', 'data.0.attributes.details'));
$config->addSetting(new StageSetting('resultTo', 'first_details'));
$stage = StageFactory::instantiateStageOfType('ArrayPath', $config);
```

### Delay
- **Identifier**: `Delay`
- **Purpose**: Pause the pipeline for a number of milliseconds (useful for throttling external APIs).
- **Settings**:
  - `milliseconds` (optional, default `1000`).

**XML**
```xml
<stage type="Delay">
  <settings>
    <param name="milliseconds">500</param>
  </settings>
</stage>
```

**PHP**
```php
$config = new StageConfiguration();
$config->addSetting(new StageSetting('milliseconds', 500));
$stage = StageFactory::instantiateStageOfType('Delay', $config);
```

### ExplodeString
- **Identifier**: `ExplodeString`
- **Purpose**: Split a string into an array using a separator.
- **Settings**:
  - `inputString` (required).
  - `separator` (required, non-empty).
  - `resultTo` (required).

**XML**
```xml
<stage type="ExplodeString">
  <settings>
    <param name="inputString">tag1,tag2,tag3</param>
    <param name="separator">,</param>
    <param name="resultTo">tags</param>
  </settings>
</stage>
```

**PHP**
```php
$config = new StageConfiguration();
$config->addSetting(new StageSetting('inputString', 'tag1,tag2,tag3'));
$config->addSetting(new StageSetting('separator', ','));
$config->addSetting(new StageSetting('resultTo', 'tags'));
$stage = StageFactory::instantiateStageOfType('ExplodeString', $config);
```

### For
See [Control-Flow Stages](#for-stage).

### ForEach
See [Control-Flow Stages](#foreach-stage).

### If
See [Control-Flow Stages](#if-stage).

### JSONDecode
- **Identifier**: `JSONDecode`
- **Purpose**: Decode a JSON string into an associative array.
- **Settings**:
  - `jsonString` (required).
  - `resultTo` (required).

**XML**
```xml
<stage type="JSONDecode">
  <settings>
    <param name="jsonString" contextReference="plain">raw_json</param>
    <param name="resultTo">payload</param>
  </settings>
</stage>
```

**PHP**
```php
$config = new StageConfiguration();
$config->addSetting(new StageSetting('jsonString', '%%raw_json%%'));
$config->addSetting(new StageSetting('resultTo', 'payload'));
$stage = StageFactory::instantiateStageOfType('JSONDecode', $config);
```

### JSONEncode
- **Identifier**: `JSONEncode`
- **Purpose**: Encode an associative array to JSON and store the string.
- **Settings**:
  - `associativeArray` (required) &mdash; context parameter name.
  - `resultTo` (required).

**XML**
```xml
<stage type="JSONEncode">
  <settings>
    <param name="associativeArray">payload</param>
    <param name="resultTo">payload_json</param>
  </settings>
</stage>
```

**PHP**
```php
$config = new StageConfiguration();
$config->addSetting(new StageSetting('associativeArray', 'payload'));
$config->addSetting(new StageSetting('resultTo', 'payload_json'));
$stage = StageFactory::instantiateStageOfType('JSONEncode', $config);
```

### RandomArrayItem
- **Identifier**: `RandomArrayItem`
- **Purpose**: Pick a random element from an array context parameter.
- **Settings**:
  - `arrayParameterName` (required).
  - `resultTo` (required).

**XML**
```xml
<stage type="RandomArrayItem">
  <settings>
    <param name="arrayParameterName">products</param>
    <param name="resultTo">featured_product</param>
  </settings>
</stage>
```

**PHP**
```php
$config = new StageConfiguration();
$config->addSetting(new StageSetting('arrayParameterName', 'products'));
$config->addSetting(new StageSetting('resultTo', 'featured_product'));
$stage = StageFactory::instantiateStageOfType('RandomArrayItem', $config);
```

### RandomValue
- **Identifier**: `RandomValue`
- **Purpose**: Generate a random number within a range.
- **Settings**:
  - `parameterName` (required) &mdash; destination key.
  - `minValue` (optional, default `0`).
  - `maxValue` (optional, default `getrandmax()`).

**XML**
```xml
<stage type="RandomValue">
  <settings>
    <param name="parameterName">captcha_seed</param>
    <param name="minValue">100000</param>
    <param name="maxValue">999999</param>
  </settings>
</stage>
```

**PHP**
```php
$config = new StageConfiguration();
$config->addSetting(new StageSetting('parameterName', 'captcha_seed'));
$config->addSetting(new StageSetting('minValue', 100000));
$config->addSetting(new StageSetting('maxValue', 999999));
$stage = StageFactory::instantiateStageOfType('RandomValue', $config);
```

### SetValue
- **Identifier**: `SetValue`
- **Purpose**: Store a fixed (or computed) value into the context.
- **Settings**:
  - `parameterName` (required).
  - `parameterValue` (required).

**XML**
```xml
<stage type="SetValue">
  <settings>
    <param name="parameterName">status</param>
    <param name="parameterValue">active</param>
  </settings>
</stage>
```

**PHP**
```php
$config = new StageConfiguration();
$config->addSetting(new StageSetting('parameterName', 'status'));
$config->addSetting(new StageSetting('parameterValue', 'active'));
$stage = StageFactory::instantiateStageOfType('SetValue', $config);
```

### SumOperation
- **Identifier**: `SumOperation`
- **Purpose**: Add, merge, or concatenate two operands based on their types and store the result.
- **Settings**:
  - `operandA` (required).
  - `operandB` (required).
  - `resultTo` (optional, default `SUM_RESULT`).

**XML**
```xml
<stage type="SumOperation">
  <settings>
    <param name="operandA" contextReference="plain">line_totals</param>
    <param name="operandB">%%currentItem[price]%%</param>
    <param name="resultTo">line_totals</param>
  </settings>
</stage>
```

**PHP**
```php
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSetting;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSettingType;

$config = new StageConfiguration();
$config->addSetting(new ReferenceStageSetting(
    ReferenceStageSettingType::plain,
    'operandA',
    'line_totals'
));
$config->addSetting(new StageSetting('operandB', '%%currentItem[price]%%'));
$config->addSetting(new StageSetting('resultTo', 'line_totals'));
$stage = StageFactory::instantiateStageOfType('SumOperation', $config);
```

## Creating Custom Stages
You can extend Pipeflow by implementing new stages which performs custom "piece" of work and interacts with the other stages to perform complex composable jobs. Custom stages could, for example, perform api calls, interacts with an ecommerce features (for example fetching or saving orders, user feedbacks, products...), or with wordpress elements (fetching/creating posts, comments, saving images, etc...), or even calling generative AI apis to perform text completions, ai image generation tasks, etc... all withing a pipeline!

1. **Create the concrete Stage class**

This is the core class of the stage.

   - Extend `AbstractPipelineStage`.
   - Accept a `StageConfiguration` in the constructor.
   - Implement `execute()` and return the mutated `PipelineContext`. This is the core of the stage, where you take the input parameters, perform the operations the stage is designed to do, then access and manipulate the current context by writing the stage output there, that will be available to any other subsequent stage that will want to use it.

```php
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;

class WPFetchPostsStage extends AbstractPipelineStage
{
    private StageConfiguration $config;

    public function __construct(StageConfiguration $config)
    {
        $this->config = $config;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        $status = $this->config->getSettingValue('status', $context, false, 'publish');
        $posts = get_posts(['post_status' => $status]);
        // ...
        $context->setParameter('wp_posts', $posts);
        return $context;
    }
}
```

2. **Create the concrete Stage factory**

The purpose of the stage factory is to instantiate the concrete stage class by decoupling it from the rest of the architecture, and to define the stage requirements by returning a StageDescriptor (which is something like the "manifest" of the stage)

```php
use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Core\StageDescriptor;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;

class WPFetchPostsStageFactory implements AbstractStageFactory
{
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        return new WPFetchPostsStage($configuration);
    }

    public function getStageDescriptor(): StageDescriptor
    {
        return new StageDescriptor(
            'WPFetchPosts',
            'Load posts from WordPress using get_posts()',
            ['status' => 'Optional post status filter.'],
            [],
            ['wp_posts' => 'List of WP_Post objects returned by get_posts()']
        );
    }
}
```

3. **Register the factory**

```php
use Marcosiino\Pipeflow\Core\StageFactory;

StageFactory::registerFactory(new WPFetchPostsStageFactory());
```

Once registered, the stage becomes available to both PHP and XML pipelines. This approach makes it easy to integrate e-commerces, wordpress features, third party apis, or any bespoke custom business logic or third party systems into your automation flows.

## Troubleshooting & Diagnostics

- **Missing factory**: `StageFactory::instantiateStageOfType()` throws `StageConfigurationException::invalidStageTypeIdentifier()` if a factory is not registered. Ensure `PipeFlow::registerStages()` (and custom factories) execute before parsing the pipeline.
- **Context lookups**: When a `contextReference` points to a missing key, `StageConfiguration::getSettingValue()` raises a `PipelineExecutionException`. Confirm earlier stages populate the required values.
- **XML validation**: If `setupWithXML()` fails, validation errors are printed via `PipelineXMLConfigurator`. Fix the XML according to the XSD schema.

## Example use cases for custom stages

- **Automate WordPress tasks**: wire Pipeflow inside a plugin, schedule pipelines via WP Cron, and build custom stages that wrap WP, WooCommerce, or third-party plugin APIs.
- **Expand the catalogue**: implement stages that is not available in the core library (or if it is a "generic" stage which could fit in the core stage catalogue, and you want to contribute with the project, you can implement as part of the core stages and make a PR to ask me to merge your work into the library!)

Pipeflow provides the scaffolding; the value comes from composing the right stages for your business logic. Happy building!
