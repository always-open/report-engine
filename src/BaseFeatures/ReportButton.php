<?php

namespace BluefynInternational\ReportEngine\BaseFeatures;

class ReportButton
{
    protected string $name = '';

    protected ?string $link = null;

    protected ?string $function = null;

    protected ?string $cssClass = 'btn btn-go';

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function setJsFunction(string $function) : self
    {
        $this->function = $function;

        return $this;
    }

    public function setCssClass(string $cssClass) : self
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    public function __toString() : string
    {
        return view('report-engine::partials.button')
            ->with([
                'href'     => $this->link,
                'function' => $this->function,
                'class'    => $this->cssClass,
                'label'    => $this->name,
            ])
            ->render();
    }
}
