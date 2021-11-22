<?php

namespace BluefynInternational\ReportEngine\BaseFeatures\ContextActions;

use Illuminate\Contracts\Support\Arrayable;

class BaseAction implements Arrayable
{
    public const POST = 'post';
    public const GET = 'get';
    public const DELETE = 'delete';
    public const PATCH = 'patch';
    public const PUT = 'put';

    protected string $label;

    protected string $template;

    protected string $action = '';

    protected string $linkTemplate = '';

    protected array $linkTemplateReplacements = [];

    protected string $httpAction = 'POST';

    protected ?string $functionName = null;

    public function __construct(string $label, string $action = '')
    {
        $this->setAction($action)
            ->setLabel($label);
    }

    public function setHttpAction(string $action): self
    {
        $this->httpAction = $action;

        return $this;
    }

    public function getHttpAction() : string
    {
        return $this->httpAction;
    }

    public function setLinkTemplate(string $link) : self
    {
        $this->linkTemplate = $link;

        return $this;
    }

    public function getLinkTemplate() : string
    {
        return $this->linkTemplate;
    }

    public function addLinkTemplateReplacement(string $key) : self
    {
        $this->linkTemplateReplacements[] = $key;

        return $this;
    }

    public function getLinkTemplateReplacements() : array
    {
        return $this->linkTemplateReplacements;
    }

    public function setAction(string $action) : self
    {
        $this->action = $action;

        return $this;
    }

    public function getMessage() : string
    {
        return "Are you sure your want to {$this->action} this record?";
    }

    public function setLabel(string $label) : self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setFunction(?string $functionName) : self
    {
        $this->functionName = $functionName;

        return $this;
    }

    public function getFunction() : ?string
    {
        return $this->functionName;
    }

    public function toArray(): array
    {
        return [
            'http_action' => $this->getHttpAction(),
            'label' => $this->getLabel(),
            'link_template' => $this->getLinkTemplate(),
            'link_template_replacements' => $this->getLinkTemplateReplacements(),
            'message' => $this->getMessage(),
            'function' => $this->getFunction(),
        ];
    }
}
