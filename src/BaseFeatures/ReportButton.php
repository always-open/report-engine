<?php

namespace BluefynInternational\ReportEngine\BaseFeatures;

class ReportButton
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $link = '';

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $link
     *
     * @return ReportButton
     */
    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function __toString() : string
    {
        return \Html::link($this->link, $this->name, ['class' => 'btn btn-go'])->toHtml();
    }
}
