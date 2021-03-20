<?php

namespace SiteRig\MailerLite\Fieldtypes;

use Statamic\Fieldtypes\Relationship;

class MailerLiteSubscriberGroups extends Relationship
{
    public function getIndexItems($request)
    {
        $tweets = Twitter::getUserTimeline([
            'screen_name' => $this->config('screen_name')
        ]);

        return $this->formatTweets($tweets);
    }

    protected function formatTweets($tweets)
    {
        return collect($tweets)->map(function ($tweet) {
            $date = Carbon::parse($tweet->created_at);

            return [
                'id'            => $tweet->id_str,
                'text'          => $tweet->text,
                'date'          => $date->timestamp,
                'date_relative' => $date->diffForHumans(),
                'user'          => $tweet->user->screen_name,
            ];
        });
    }
}

