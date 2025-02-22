<?php

declare(strict_types=1);

namespace Speicher210\OpenApiGenerator\Describer;

use cebe\openapi\spec\Reference;
use cebe\openapi\spec\Schema;
use cebe\openapi\SpecObjectInterface;
use InvalidArgumentException;
use Psl;
use Speicher210\OpenApiGenerator\Describer\ExampleDescriber\ExampleDescriber;
use Speicher210\OpenApiGenerator\Describer\Form\FormFactory;
use Speicher210\OpenApiGenerator\Model\Definition;
use Speicher210\OpenApiGenerator\Model\Path\Output;
use Speicher210\OpenApiGenerator\Model\Path\ReferencableOutput;

final class OutputDescriber
{
    private ObjectDescriber $objectDescriber;

    /** @var array<OutputDescriber\OutputDescriber> */
    private array $outputDescribers;

    public function __construct(
        ObjectDescriber $objectDescriber,
        FormFactory $formFactory,
        ExampleDescriber $exampleDescriber
    ) {
        $this->objectDescriber = $objectDescriber;

        $this->outputDescribers = [
            new OutputDescriber\ScalarOutputDescriber(),
            new OutputDescriber\SimpleOutputDescriber(),
            new OutputDescriber\FileOutputDescriber(),
            new OutputDescriber\CollectionOutputDescriber($this, $exampleDescriber),
            new OutputDescriber\PaginatedOutputDescriber($this),
            new OutputDescriber\FormErrorOutputDescriber($formFactory),
            new OutputDescriber\ObjectOutputDescriber($this->objectDescriber, $exampleDescriber),
        ];
    }

    /**
     * @return Reference|Schema
     */
    public function describe(Output $output): SpecObjectInterface
    {
        if ($output instanceof ReferencableOutput) {
            $definition = Definition::fromObjectOutput($output->output());

            return $this->objectDescriber->describeAsReference($definition, $output->referencePath());
        }

        foreach ($this->outputDescribers as $outputDescriber) {
            if ($outputDescriber->supports($output)) {
                return $outputDescriber->describe($output);
            }
        }

        throw new InvalidArgumentException(
            Psl\Str\format('Can not handle object to describe of type "%s"', $output::class)
        );
    }
}
