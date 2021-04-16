<?php

namespace SiteRig\MailerLite\Fieldtypes;

use SiteRig\MailerLite\MailerLite;
use Statamic\Fieldtypes\Relationship;
use Statamic\Support\Arr;

class SubscriberGroup extends Relationship
{
    private $mailerlite = null;

    protected $canCreate = false;

    public function __construct()
    {
        $this->mailerlite = new MailerLite;
    }

    public function getIndexItems($request)
    {
        return $this->mailerlite->getSubscriberGroups();
    }

    protected function toItemArray($id)
    {
        return[];
    }
}
