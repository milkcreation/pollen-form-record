<?php

declare(strict_types=1);

namespace Pollen\FormRecord;

use LogicException;
use tiFy\Contracts\Form\AddonDriver;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Template\Templates\ListTable\Factory as BaseFactory;

class ListTableFactory extends BaseFactory
{
    /**
     * Instance de l'addon de formulaire associé.
     * @var AddonDriver|null
     */
    protected $addon;

    /**
     * Instance du formulaire associé.
     * @var FormFactory|null
     */
    protected $form;

    /**
     * Récupération de l'instance de l'addon associé.
     *
     * @return AddonDriver
     */
    public function addon(): AddonDriver
    {
        if ($this->addon instanceof AddonDriver) {
            return $this->addon;
        }

        throw new LogicException('Invalid related AddonDriver');
    }

    /**
     * Définition de l'addon associé.
     *
     * @param AddonDriver $addon
     *
     * @return $this
     */
    public function setAddon(AddonDriver $addon): self
    {
        $this->addon = $addon;

        return $this;
    }
}