<?php

namespace SiteRig\MailerLite\Fieldtypes;

use Statamic\Facades\Form;
use Statamic\Fieldtypes\Relationship;

class FormFields extends Relationship
{
    private $form = null;

    private $formHandle = 'newsletter';

    protected $canCreate = false;

    public function __construct()
    {
        $this->form = Form::find($this->formHandle);
    }

    public function getIndexItems($request)
    {
        $form_fields = $this->form->fields()->all();

        foreach ($form_fields as $id => $field)
        {

            // Add field to array
            $options[] = [
                'id' => $id,
                'title' => $id,
            ];

        }
        return $options;
    }

    protected function toItemArray($id)
    {
        return [];
    }
}
