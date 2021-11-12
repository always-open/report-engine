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

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $action = '';

    /**
     * @var string
     */
    protected $linkTemplate = '';

    /**
     * @var array
     */
    protected $linkTemplateReplacements = [];

    /**
     * @var string
     */
    protected $httpAction = 'POST';

    /**
     * BaseAction constructor.
     *
     * @param string $label
     * @param string $action
     */
    public function __construct(string $label, string $action)
    {
        $this->setAction($action)
            ->setLabel($label);
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setHttpAction(string $action): self
    {
        $this->httpAction = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpAction() : string
    {
        return $this->httpAction;
    }

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLinkTemplate(string $link) : self
    {
        $this->linkTemplate = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkTemplate() : string
    {
        return $this->linkTemplate;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function addLinkTemplateReplacement(string $key) : self
    {
        $this->linkTemplateReplacements[] = $key;

        return $this;
    }

    /**
     * @return array
     */
    public function getLinkTemplateReplacements() : array
    {
        return $this->linkTemplateReplacements;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction(string $action) : self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return "Are you sure your want to {$this->action} this record?";
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label) : self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    public function toArray(): array
    {
        return [
            'http_action' => $this->getHttpAction(),
            'label' => $this->getLabel(),
            'link_template' => $this->getLinkTemplate(),
            'link_template_replacements' => $this->getLinkTemplateReplacements(),
            'message' => $this->getMessage(),
        ];
    }
}
