<?php

declare(strict_types=1);

namespace Pollen\FormRecord;

use Illuminate\Database\Schema\Blueprint;
use Pollen\Form\AddonDriver;
use Pollen\Form\AddonDriverInterface;
use Pollen\Support\Arr;
use Pollen\Support\DateTime;
use Pollen\Support\Str;

class FormAddonDriver extends AddonDriver implements FormAddonDriverInterface
{
    /**
     * Indicateur d'existance d'une instance.
     * @var boolean
     */
    protected static $instance = false;

    /**
     * @inheritDoc
     */
    public function build(): AddonDriverInterface
    {
        if (!$this->isBuilt()) {
            parent::build();

            Database::addConnection(
                array_merge(Database::getConnection()->getConfig(), ['strict' => false]),
                'form.addon.record'
            );

            if (is_multisite()) {
                global $wpdb;

                Database::getConnection('form.addon.record')->setTablePrefix($wpdb->prefix);
            }

            $schema = Schema::connexion('form.addon.record');

            if (!$schema->hasTable('tify_forms_record')) {
                $schema->create('tify_forms_record', function (Blueprint $table) {
                    $table->bigIncrements('ID');
                    $table->string('form_id', 255);
                    $table->string('session', 255);
                    $table->string('status', 32)->default('publish');
                    $table->dateTime('created_date')->default('0000-00-00 00:00:00');
                    $table->index('form_id', 'form_id');
                });
            }

            if (!$schema->hasTable('tify_forms_recordmeta')) {
                $schema->create('tify_forms_recordmeta', function (Blueprint $table) {
                    $table->bigIncrements('meta_id');
                    $table->bigInteger('tify_forms_record_id')->default(0);
                    $table->string('meta_key', 255)->nullable();
                    $table->longText('meta_value')->nullable();
                    $table->index('tify_forms_record_id', 'tify_forms_record_id');
                    $table->index('meta_key', 'meta_key');
                });
            }

            add_action('admin_menu', function () {
                add_menu_page(
                    __('Formulaires', 'tify'),
                    __('Formulaires', 'tify'),
                    null,
                    'form_addon_record',
                    '',
                    'dashicons-clipboard'
                );
            });
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function boot(): AddonDriverInterface
    {
        if (!$this->isBooted()) {
            parent::boot();

            $this->form()->events()->listen('form.booted', function () {
                $columns = [
                    '__record' => [
                        'content' => function ($item) {
                            return (string)$this->form()->view(
                                'addon/record/list-table/col-details',
                                compact('item')
                            );
                        },
                        'title'   => __('Informations d\'enregistrement', 'tify')
                    ]
                ];

                foreach ($this->form()->fields() as $field) {
                    if ($column = $field->getAddonOption('record.column')) {
                        if (is_string($column)) {
                            $column = ['title' => $column];
                        } elseif (!is_array($column)) {
                            $column = [];
                        }

                        $slug = $field->getSlug();
                        $columns[$slug] = array_merge([
                            'title'   => $field->getTitle(),
                            'content' => function ($item) use ($slug) {
                                return $item->{$slug} ?? '';
                            }
                        ], $column);
                    }
                }

                Template::set(
                    'FormAddonRecord' . Str::studly($this->form()->getAlias()),
                    (new ListTableFactory())->setAddon($this)->set([
                        'labels'    => [
                            'gender'   => $this->form()->labels()->gender(),
                            'singular' => $this->form()->labels()->singular(),
                            'plural'   => $this->form()->labels()->plural(),
                        ],
                        'params'    => [
                            'bulk-actions' => false,
                            'columns'      => $columns,
                            'query_args'   => [
                                'order' => 'DESC'
                            ],
                            'row-actions'  => false,
                            'search'       => false,
                            'view-filters' => false,
                            'wordpress'    => [
                                'admin_menu' => [
                                    'parent_slug' => 'form_addon_record'
                                ],
                            ],
                        ],
                        'providers' => [
                            'db' => (new ListTableModel())->setAddon($this)
                        ]
                    ])
                );
            });

            $this->form()->events()
                ->listen('handle.validated', function () {
                    $this->form()->event('addon.record.save');
                })
                ->listen('addon.record.save', [$this, 'save']);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultFieldOptions(): array
    {
        return [
            //'export'   => false,
            //'editable' => false,
            'column'   => true,
            'preview'  => true,
            'save'     => true,
        ];
    }

    /**
     * Sauvegarde des données de formulaire.
     *
     * @return void
     */
    public function save(): void
    {
        $datas = [
            'form_id'      => $this->form()->getAlias(),
            'session'      => $this->form()->session()->getToken(),
            'status'       => 'publish',
            'created_date' => DateTime::now()->toDateTimeString(),
        ];

        if ($id = RecordModel::on()->insertGetId($datas)) {
            $record = RecordModel::on()->find($id);

            foreach ($this->form()->fields() as $field) {
                if ($column = $field->getAddonOption('record.save')) {
                    $record->saveMeta($field->getSlug(), Arr::stripslashes($field->getValues()));
                }
            }
        }
    }
}