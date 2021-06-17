<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Row implements Arrayable
{
    /**
     * @var Collection
     */
    protected $cells;

    /**
     * @var object|null
     */
    protected $resultItems = null;

    public function __construct()
    {
        $this->cells = collect();
    }

    /**
     * @param object $result
     *
     * @return $this
     */
    public function setResultItems(object $result): self
    {
        $this->resultItems = $result;

        return $this;
    }

    /**
     * @return object|null
     */
    public function getResultItems(): ?object
    {
        return $this->resultItems;
    }

    /**
     * @return Collection
     */
    public function getCells(): Collection
    {
        return $this->cells;
    }

    /**
     * @param iterable $cells
     *
     * @return $this
     */
    public function setCells(iterable $cells): self
    {
        $this->cells = collect($cells);

        return $this;
    }

    /**
     * @param Cell $cell
     *
     * @return $this
     */
    public function appendCell(Cell $cell) : self
    {
        $this->cells->put($cell->getColumn()->name(), $cell);

        return $this;
    }

    /**
     * @param Cell $cell
     *
     * @return $this
     */
    public function prependCell(Cell $cell) : self
    {
        $this->cells->prepend($cell);

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'cells' => $this->getCells()->map(function (Cell $cell) {
                return $cell->toArrayWithResult($this->getResultItems());
            }),
        ];
    }
}
