<?php

namespace SiteRig\MailerLite\Fieldtypes;

use SiteRig\MailerLite\MailerLite;
use Statamic\Fieldtypes\Relationship;

class SubscriberField extends Relationship
{
    private $mailerlite = null;

    protected $canCreate = false;

    public function __construct()
    {
        $this->mailerlite = new MailerLite;
    }

    public function getIndexItems($request)
    {
        return $this->mailerlite->getSubscriberFields();
    }

    protected function toItemArray($id)
    {
        return[];
    }
}
