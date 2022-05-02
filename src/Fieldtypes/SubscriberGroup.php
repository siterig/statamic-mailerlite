<?php

namespace SiteRig\MailerLite\Fieldtypes;

use SiteRig\MailerLite\MailerLite;
use Statamic\Fieldtypes\Relationship;

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
        if ($id && $subscriber_group = $this->mailerlite->getSubscriberGroups($id)) {
            return $subscriber_group;
        }

        return [];
    }
}
