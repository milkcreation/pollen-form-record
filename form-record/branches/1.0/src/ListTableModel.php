<?php

declare(strict_types=1);

namespace Pollen\FormRecord;

use tiFy\Form\Concerns\AddonAwareTrait;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ListTableModel extends RecordModel
{
    use AddonAwareTrait;

    /**
     * @return Builder|EloquentBuilder
     */
    public function newQuery()
    {
        return $this->form() ? parent::newQuery()->where('form_id', $this->form()->getAlias()) : parent::newQuery();
    }
}